<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnnouncementNew extends Model
{
    protected $table = 'announcement_new'; // Explicit table name

    protected $fillable = [
        'title',
        'content',
        'media',
        'category',
        'url',
    ];

    public $timestamps = true; // Set false if your table doesn't have created_at / updated_at
}
