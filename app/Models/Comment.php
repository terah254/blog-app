<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'blog_id',
        'admin_id',
        'body',
    ];

    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}