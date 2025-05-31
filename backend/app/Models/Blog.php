<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'keyword_blog_id',
        'title',
        'description',
        'images_blog',
        'date',

    ];


    public function keywordBlog()
{
    return $this->belongsTo(KeywordBlog::class);
}
public function tags()
    {
        return $this->belongsToMany(Tag::class, 'blog_tag');
    }

}