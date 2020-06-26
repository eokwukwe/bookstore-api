<?php

namespace App\Models;

class Author extends AbstractAPIModel
{
    protected $fillable = [
        'first_name', 'last_name', 'other_name'
    ];

    public function books()
    {
        return $this->belongsToMany(Book::class);
    }

    /**
     * @return string
     */
    public function type()
    {
        return 'authors';
    }
}
