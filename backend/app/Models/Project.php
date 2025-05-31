<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_project';

    protected $fillable = [
        'name_project',
        'sub_title',
        'category_id',
        'perusahaan_id',
        'picture',
        'description1',
        'tanggal',
        'picture01',
        'picture02',
        'picture03',
        'picture04',
        'description2',
        'description3'
        ];

    public function category()
    {
        return $this->belongsTo(ProjectCategory::class, 'category_id', 'id_category');
    }

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'perusahaan_id', 'id');
    }
    public function superiorities()
    {
        return $this->belongsToMany(Superiority::class, 'project_superiority', 'project_id', 'superiority_id');
    }
    
}
