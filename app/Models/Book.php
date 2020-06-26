<?php

namespace App\Models;

class Book extends AbstractAPIModel
{
    protected $fillable = [
        'title', 'description', 'publication_year'
    ];

    public function authors()
    {
        return $this->belongsToMany(Author::class);
    }

    /**
     * @return string
     */
    public function type()
    {
        return 'books';
    }
}
