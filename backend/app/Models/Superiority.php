<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Superiority extends Model
{
    use HasFactory;

    protected $table = 'superiorities';
    protected $fillable = [
        'logo_superiority',
        'name',
        'description',
    ];

    // Define the many-to-many relationship
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_superiority', 'superiority_id', 'project_id');
    }
}
