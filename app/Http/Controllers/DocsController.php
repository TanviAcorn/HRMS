<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class DocsController extends Controller
{
    // Viewer page without download/print controls
    public function viewQuickLink($filename)
    {
        // Basic allowlist: only simple filenames to avoid traversal
        abort_unless(preg_match('/^[A-Za-z0-9._-]+$/', $filename) === 1, 404);

        $publicPath = public_path('quick-links/' . $filename);
        abort_unless(is_file($publicPath), 404);

        $streamUrl = url('docs/stream/' . $filename);
        return view('docs.viewer', [
            'title' => $filename,
            'streamUrl' => $streamUrl,
        ]);
    }

    // Streams the PDF bytes with inline disposition and no-store headers
    public function streamQuickLink($filename)
    {
        abort_unless(preg_match('/^[A-Za-z0-9._-]+$/', $filename) === 1, 404);
        $publicPath = public_path('quick-links/' . $filename);
        abort_unless(is_file($publicPath), 404);

        return response()->file($publicPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($publicPath) . '"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
