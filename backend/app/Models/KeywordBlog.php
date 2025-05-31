<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeywordBlog extends Model
{
    use HasFactory;

    protected $fillable = ['name_keyword'];

    public function blogs()
{
    return $this->hasMany(Blog::class);
}

}
