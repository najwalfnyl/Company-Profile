<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Career extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'images_career',
        'description'

    ];

    public function vacancies()
    {
        return $this->hasMany(Vacancy::class);
    }
}
