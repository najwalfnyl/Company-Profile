<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use HasFactory;

    protected $fillable = [
        'description1',
        'description2',
        'year',
        'email',
        'phone',
        'instagram',
        'facebook',
        'linkedln'
    ];
}
