<?php

namespace App\Http\Controllers;

use App\Book;
use App\Http\Resources\JSONAPICollection;

class BooksAuthorsRelatedController extends Controller
{
    public function index(Book $book)
    {
        return new JSONAPICollection($book->authors);
    }
}
