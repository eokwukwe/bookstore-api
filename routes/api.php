<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->prefix('v1')->group(function () {


    Route::get('/users/current', 'CurrentAuthenticatedUserController@show');

    // Users
    Route::apiResource('users', 'UsersController');
    Route::get(
        'users/{user}/relationships/comments',
        'UsersCommentsRelationshipsController@index'
    )->name('users.relationships.comments');

    Route::patch(
        'users/{user}/relationships/comments',
        'UsersCommentsRelationshipsController@update'
    )->name('users.relationships.comments');

    Route::get(
        'users/{user}/comments',
        'UsersCommentsRelatedController@index'
    )->name('users.comments');

    // Books
    Route::apiResource('books', 'BooksController');

    // Books-Authors relationship
    Route::get(
        'books/{book}/authors',
        'BooksAuthorsRelatedController@index'
    )->name('books.authors');

    Route::get(
        'books/{book}/relationships/authors',
        'BooksAuthorsRelationshipsController@index'
    )->name('books.relationships.authors');

    Route::patch(
        'books/{book}/relationships/authors',
        'BooksAuthorsRelationshipsController@update'
    )->name('books.relationships.authors');

    // Books-Comments relationship
    Route::get(
        'books/{book}/comments',
        'BooksCommentsRelatedController@index'
    )->name('books.comments');

    Route::get(
        'books/{book}/relationships/comments',
        'BooksCommentsRelationshipsController@index'
    )->name('books.relationships.comments');

    Route::patch(
        'books/{book}/relationships/comments',
        'BooksCommentsRelationshipsController@update'
    )->name('books.relationships.comments');

    // Authors
    Route::apiResource('authors', 'AuthorsController');

    Route::get(
        'authors/{author}/books',
        'AuthorsBooksRelatedController@index'
    )->name('authors.books');

    Route::get(
        'authors/{author}/relationships/books',
        'AuthorsBooksRelationshipsController@index'
    )->name('authors.relationships.books');

    Route::patch(
        'authors/{author}/relationships/books',
        'AuthorsBooksRelationshipsController@update'
    )->name('authors.relationships.books');

    // Comments
    Route::apiResource('comments', 'CommentsController');

    // Comments-Users relationship
    Route::get(
        'comments/{comment}/users',
        'CommentsUsersRelatedController@index'
    )->name('comments.users');

    Route::get(
        'comments/{comment}/relationships/users',
        'CommentsUsersRelationshipsController@index'
    )->name('comments.relationships.users');

    Route::patch(
        'comments/{comment}/relationships/users',
        'CommentsUsersRelationshipsController@update'
    )->name('comments.relationships.users');

    // Comments-Books relationship
    Route::get(
        'comments/{comment}/books',
        'CommentsBooksRelatedController@index'
    )->name('comments.books');

    Route::get(
        'comments/{comment}/relationships/books',
        'CommentsBooksRelationshipsController@index'
    )->name('comments.relationships.books');

    Route::patch(
        'comments/{comment}/relationships/books',
        'CommentsBooksRelationshipsController@update'
    )->name('comments.relationships.books');
});
