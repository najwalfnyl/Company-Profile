<?php

// app/Models/Gallery.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'image_activity1',
        'image_activity2',
        'image_activity3',
        'image_activity4',
        'image_activity5',
        'image_activity6',
        'image_activity7',
    ];
}
