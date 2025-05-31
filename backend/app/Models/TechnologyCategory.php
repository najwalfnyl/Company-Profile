<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechnologyCategory extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['name_category'];

    public function technologies()
    {
        return $this->hasMany(Technology::class);
    }
}
