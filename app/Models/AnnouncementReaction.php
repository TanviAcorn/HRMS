<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnnouncementReaction extends Model
{
    protected $table = 'announcement_reactions';

    protected $fillable = [
        'announcement_id',
        'user_id',
        'emoji',
    ];

    public function announcement()
    {
        return $this->belongsTo(Announcement::class, 'announcement_id');
    }

    public function user()
    {
        // Adjust model if your auth model differs
        return $this->belongsTo(\App\Login::class, 'user_id');
    }
}
