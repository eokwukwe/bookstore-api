<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Services\JSONAPIService;

class BooksAuthorsRelatedController extends Controller
{
    private $service;

    public function __construct(JSONAPIService $service)
    {
        $this->service = $service;
    }

    public function index(Book $book)
    {
        return $this->service->fetchRelated($book, 'authors');
    }
}
