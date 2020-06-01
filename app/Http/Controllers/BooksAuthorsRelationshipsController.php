<?php

namespace App\Http\Controllers;

use App\Book;
use Illuminate\Http\Request;
use App\Http\Resources\AuthorsIdentifierResource;
use App\Http\Requests\BooksAuthorsRelationshipsRequest;

class BooksAuthorsRelationshipsController extends Controller
{
    public function index(Book $book)
    {
        return AuthorsIdentifierResource::collection($book->authors);
    }

    public function update(
        BooksAuthorsRelationshipsRequest $request,
        Book $book
    ) {
        $ids = $request->input('data.*.id');
        $book->authors()->sync($ids);

        return response(null, 204);
    }
}
