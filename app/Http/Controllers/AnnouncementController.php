<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Services\SharePointService;
use App\Services\GraphServices;
use App\Models\AnnouncementNew;
use App\Models\AnnouncementReaction;
use App\Login;
use App\Helpers\Twt\Wild_tiger;
use Illuminate\Support\Facades\File;

class AnnouncementController extends Controller
{
    protected $sharePointService;

    public function __construct(SharePointService $sharePointService)
    {
        $this->sharePointService = $sharePointService;
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            // Restrict to images only stored in public/announcements
            'media' => 'nullable|image|mimes:jpeg,jpg,png|max:51200',
            'category' => 'required|in:Events,Canteen Menu,Monday Motivation,Emergency,Under Maintenance,Internal Job Posting,Others',
        ]);

        $mediaPath = null;

        if ($request->hasFile('media')) {
            $file = $request->file('media');
            $original = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = strtolower($file->getClientOriginalExtension());
            $safeBase = Str::slug($original);
            $fileName = $safeBase . '_' . time() . '.' . $extension;

            $destDir = public_path('announcements');
            if (!File::exists($destDir)) {
                File::makeDirectory($destDir, 0755, true);
            }

            $file->move($destDir, $fileName);
            $mediaPath = 'announcements/' . $fileName;
        }

        $announcement = AnnouncementNew::create([
            'title' => $request->title,
            'content' => $request->content,
            'media' => $mediaPath,
            'category' => $request->category,
        ]);

        // Attempt to send notification email to all active users
        try {
            $emails = Login::query()
                ->where(['t_is_deleted' => 0, 't_is_active' => 1])
                ->whereNotNull('v_email')
                ->pluck('v_email')
                ->filter(function ($e) { return filter_var($e, FILTER_VALIDATE_EMAIL); })
                ->unique()
                ->values()
                ->all();

            foreach ($emails as $email) {
                try {
                    $mailCfg = [
                        'viewName' => 'emails.announcement_posted',
                        'mailData' => [ 'announcement' => $announcement ],
                        'subject'  => 'New Announcement: ' . ($announcement->title ?? ''),
                        'to'       => $email,
                    ];
                    Wild_tiger::sendMailSMTP($mailCfg);
                } catch (\Throwable $inner) {
                    // continue on individual failures
                }
            }
        } catch (\Throwable $e) {
            // Silently ignore email errors to not block announcement creation
        }

        return redirect()->back()->with('success', 'Announcement posted successfully.');
    }
    public function index()
    {
        // Announcements are rendered on the dashboard; redirect there
        return redirect()->route('dashboard');
    }
    public function destroy($id)
{
    $announcement = AnnouncementNew::findOrFail($id);
    
    // Delete from public/announcements if present
    if (!empty($announcement->media)) {
        try {
            $abs = public_path(ltrim($announcement->media, '/'));
            if (File::exists($abs)) {
                File::delete($abs);
            }
        } catch (\Throwable $e) {
            // ignore delete failures
        }
    }

        $announcement->delete();

        return redirect()->back()->with('success', 'Announcement deleted successfully.');
    }

    /**
     * Get reaction counts per emoji and current user's reactions for an announcement
     */
    public function reactionsIndex($id)
    {
        $announcement = AnnouncementNew::findOrFail($id);

        $counts = AnnouncementReaction::select('emoji', DB::raw('COUNT(*) as count'))
            ->where('announcement_id', $announcement->id)
            ->groupBy('emoji')
            ->pluck('count', 'emoji');

        $userReacted = [];
        if (session()->has('user_id') && session('user_id') > 0) {
            $userReacted = AnnouncementReaction::where('announcement_id', $announcement->id)
                ->where('user_id', session('user_id'))
                ->pluck('emoji')
                ->toArray();
        }

        return response()->json([
            'counts' => $counts,
            'userReacted' => $userReacted,
        ]);
    }

    /**
     * Toggle a reaction for the logged-in user
     */
    public function reactionsToggle(Request $request, $id)
    {
        $request->validate([
            'emoji' => 'required|string|in:👍,❤️,🎉,😂,😮,😢',
        ]);

        if (!(session()->has('user_id') && session('user_id') > 0)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $announcement = AnnouncementNew::findOrFail($id);
        $userId = session('user_id');
        $emoji = $request->input('emoji');

        // Enforce single reaction per user per announcement
        $anyReaction = AnnouncementReaction::where('announcement_id', $announcement->id)
            ->where('user_id', $userId)
            ->first();

        if ($anyReaction) {
            if ($anyReaction->emoji === $emoji) {
                // Toggle off same emoji
                $anyReaction->delete();
            } else {
                // Block switching to a different emoji; client should disable others
                return response()->json([
                    'message' => 'You have already reacted. Remove your reaction to choose another.',
                ], 409);
            }
        } else {
            AnnouncementReaction::create([
                'announcement_id' => $announcement->id,
                'user_id' => $userId,
                'emoji' => $emoji,
            ]);
        }

        $counts = AnnouncementReaction::select('emoji', DB::raw('COUNT(*) as count'))
            ->where('announcement_id', $announcement->id)
            ->groupBy('emoji')
            ->pluck('count', 'emoji');

        $userReacted = AnnouncementReaction::where('announcement_id', $announcement->id)
            ->where('user_id', $userId)
            ->pluck('emoji')
            ->toArray();

        return response()->json(['counts' => $counts, 'userReacted' => $userReacted]);
    }

    /**
     * Get usernames who reacted with a specific emoji on an announcement
     */
    public function reactionsUsers(Request $request, $id, $emoji)
    {
        $allowed = ['👍','❤️','🎉','😂','😮','😢'];
        if (!in_array($emoji, $allowed, true)) {
            return response()->json(['message' => 'Invalid emoji'], 422);
        }

        // Ensure announcement exists
        $announcement = AnnouncementNew::findOrFail($id);

        $names = AnnouncementReaction::query()
            ->join((new Login)->getTable() . ' as u', 'u.i_id', '=', 'announcement_reactions.user_id')
            ->where('announcement_reactions.announcement_id', $announcement->id)
            ->where('announcement_reactions.emoji', $emoji)
            ->orderBy('u.v_name')
            ->limit(50)
            ->pluck('u.v_name');

        return response()->json(['users' => $names]);
    }
}
