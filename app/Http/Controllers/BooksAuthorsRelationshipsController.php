<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Services\JSONAPIService;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\JSONAPIRelationshipRequest;
use Illuminate\Auth\Access\AuthorizationException;

class BooksAuthorsRelationshipsController extends Controller
{
    /**
     * @var JSONAPIService
     */
    private $service;

    public function __construct(JSONAPIService $service)
    {
        $this->service = $service;
    }

    public function index(Book $book)
    {
        return $this->service->fetchRelationship($book, 'authors');
    }

    public function update(
        JSONAPIRelationshipRequest $request,
        Book $book
    ) {
        if(Gate::denies('admin-only')) {
            throw new AuthorizationException('This action is unauthorized.');
        }

        return $this->service
            ->updateManyToManyRelationships(
                $book,
                'authors',
                $request->input('data.*.id')
            );
    }
}
