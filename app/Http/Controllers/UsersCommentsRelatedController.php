<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\JSONAPIService;

class UsersCommentsRelatedController extends Controller
{
    /**
     * @var JSONAPIService
     */
    private $service;

    public function __construct(JSONAPIService $service)
    {
        $this->service = $service;
    }

    public function index(User $user)
    {
        return $this->service->fetchRelated($user, 'comments');
    }
}
