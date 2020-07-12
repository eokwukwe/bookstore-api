<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Services\JSONAPIService;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\JSONAPIRelationshipRequest;
use Illuminate\Auth\Access\AuthorizationException;

class AuthorsBooksRelationshipsController extends Controller
{
    /**
     * @var JSONAPIService
     */
    private $service;

    public function __construct(JSONAPIService $service)
    {
        $this->service = $service;
    }

    public function index(Author $author)
    {
        return $this->service->fetchRelationship($author, 'books');
    }

    public function update(
        JSONAPIRelationshipRequest $request,
        Author $author
    ) {
        if (Gate::denies('admin-only')) {
            throw new AuthorizationException('This action is unauthorized.');
        }

        return $this->service
            ->updateManyToManyRelationships(
                $author,
                'books',
                $request->input('data.*.id')
            );
    }
}
