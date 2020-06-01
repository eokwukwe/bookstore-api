<?php

namespace App\Http\Controllers;

use App\Book;
use Illuminate\Http\Request;
use App\Http\Resources\AuthorsCollection;

class BooksAuthorsRelatedController extends Controller
{
    public function index(Book $book)
    {
        return new AuthorsCollection($book->authors);
    }
}
