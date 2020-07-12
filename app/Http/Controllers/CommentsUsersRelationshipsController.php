<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Services\JSONAPIService;
use App\Http\Requests\JSONAPIRelationshipRequest;

class CommentsUsersRelationshipsController extends Controller
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
        return $this->service->fetchRelationship($comment, 'users');
    }

    public function update(
        JSONAPIRelationshipRequest $request,
        Comment $comment
    ) {
        return $this->service->updateToOneRelationship(
            $comment,
            'users',
            $request->input('data.id')
        );
    }
}
