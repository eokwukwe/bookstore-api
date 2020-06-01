<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    protected $fillable = [
        'first_name', 'last_name', 'other_name'
    ];

    public function books()
    {
        return $this->belongsToMany(Book::class);
    }
}
