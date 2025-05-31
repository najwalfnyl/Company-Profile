<?php

// ProjectCategory.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectCategory extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_category';
    protected $fillable = ['name_category'];

    public function projects()
    {
        return $this->hasMany(Project::class, 'category_id', 'id_category');
    }
}
