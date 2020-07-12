<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Services\JSONAPIService;

class CommentsBooksRelatedController extends Controller
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
        return $this->service->fetchRelated($comment, 'books');
    }
}
