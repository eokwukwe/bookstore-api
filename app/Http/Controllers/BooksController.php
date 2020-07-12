<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Services\JSONAPIService;
use App\Http\Requests\JSONAPIRequest;

class BooksController extends Controller
{
    private $service;

    public function __construct(JSONAPIService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->service->fetchResources(Book::class, 'books');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(JSONAPIRequest $request)
    {
        $this->authorize('create', Book::class);

        return $this->service
            ->createResource(
                Book::class,
                $request->input('data.attributes'),
                $request->input('data.relationships'),
            );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function show($book)
    {

        return $this->service->fetchResource(Book::class, $book, 'books');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function update(JSONAPIRequest $request, Book $book)
    {
        $this->authorize('update', $book);

        return $this->service
            ->updateResource(
                $book,
                $request->input('data.attributes'),
                $request->input('data.relationships'),
            );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function destroy(Book $book)
    {
        $this->authorize('delete', $book);

        return $this->service->deleteResource($book);
    }
}
