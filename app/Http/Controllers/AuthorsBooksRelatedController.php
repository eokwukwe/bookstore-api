<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Services\JSONAPIService;

class AuthorsBooksRelatedController extends Controller
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
        return $this->service->fetchRelated($author, 'books');
    }
}
