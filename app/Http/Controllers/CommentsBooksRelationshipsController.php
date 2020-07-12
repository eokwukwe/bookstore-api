<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Services\JSONAPIService;
use App\Http\Requests\JSONAPIRelationshipRequest;

class CommentsBooksRelationshipsController extends Controller
{
    /**
     * @var JSONAPIService
     */
    private $service;

    public function __construct(JSONAPIService $service)
    {
        $this->service = $service;
    }

    public function index(Comment $comment)
    {
        return $this->service->fetchRelationship($comment, 'books');
    }

    public function update(JSONAPIRelationshipRequest $request, Comment
    $comment)
    {
        return $this->service->updateToOneRelationship(
            $comment,
            'books',
            $request->input('data.id')
        );
    }
}
