<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 
class Announcement extends Model
{
    use HasFactory;
 
    // Specify custom table name
    protected $table = 'announcement_new';
 
    // Fields that are mass assignable
    protected $fillable = [
        'title',
        'content',
        'media',
        'category',
        'url',
    ];

    /**
     * Relationship: reactions on this announcement
     */
    public function reactions()
    {
        return $this->hasMany(\App\Models\AnnouncementReaction::class, 'announcement_id');
    }

    /**
     * Helper: counts grouped by emoji
     */
    public function reactionCounts()
    {
        return $this->reactions()
            ->select('emoji', \DB::raw('COUNT(*) as count'))
            ->groupBy('emoji');
    }
}