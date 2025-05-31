<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perusahaan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_perusahaan',
        'logo',
        'testimony',
        'nama_client',
        'role',
    ];

    public function projects()
    {
        return $this->hasMany(Project::class, 'perusahaan_id', 'id');
    }
}
