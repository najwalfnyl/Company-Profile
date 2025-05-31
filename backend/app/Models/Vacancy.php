<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vacancy extends Model
{
    use HasFactory;
    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'age',
        'career_id',
        'CV',
        'Portofolio',
        'description',
        'status'

    ];
    public function career()
    {
        return $this->belongsTo(Career::class);
    } 

}
