<?php

namespace App\Http\Controllers;

use App\Author;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Resources\AuthorsResource;
use App\Http\Resources\AuthorsCollection;
use App\Http\Requests\CreateAuthorRequest;
use App\Http\Requests\UpdateAuthorRequest;

class AuthorsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $authors = QueryBuilder::for(Author::class)
            ->allowedSorts(['first_name', 'created_at', 'updated_at'])
            ->jsonPaginate();

        return new AuthorsCollection($authors);
        // return AuthorsResource::collection($authors);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateAuthorRequest $request)
    {
        $author = Author::create([
            'first_name' => $request->input('data.attributes.first_name'),
            'last_name' => $request->input('data.attributes.last_name'),
            'other_name' => $request->input('data.attributes.other_name'),
        ]);
        return (new AuthorsResource($author))
            ->response()
            ->header('Location', route('authors.show', ['author' => $author]));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Author  $author
     * @return \Illuminate\Http\Response
     */
    public function show(Author $author)
    {
        return new AuthorsResource($author);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Author  $author
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAuthorRequest $request, Author $author)
    {
        $author->update($request->input('data.attributes'));
        return new AuthorsResource($author);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Author  $author
     * @return \Illuminate\Http\Response
     */
    public function destroy(Author $author)
    {
        $author->delete();
        return response(null, 204);
    }
}
