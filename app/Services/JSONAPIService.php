<?php

namespace App\Services;

use Illuminate\Support\Str;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Resources\JSONAPIResource;
use App\Http\Resources\JSONAPICollection;

class JSONAPIService
{
  /**
   * Fetch the specified resource.
   *
   * @param  \App\Model  $model
   * @return \Illuminate\Http\Response
   */
  public function fetchResource($model)
  {
    return new JSONAPIResource($model);
  }

  /**
   * Fetch a listing of the resource.
   *
   * @param  string $modelClass
   * @param  string $type
   * @return \Illuminate\Http\Response
   */
  public function fetchResources(string $modelClass, string $type)
  {
    $models = QueryBuilder::for($modelClass)
      ->allowedSorts(config("jsonapi.resources.{$type}.allowSorts"))
      ->jsonPaginate();

    return new JSONAPICollection($models);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  string $modelClass
   * @param  array $attributes
   * @return \Illuminate\Http\Response
   */
  public function createResource(string $modelClass, array
  $attributes)
  {
    $model = $modelClass::create($attributes);
    return (new JSONAPIResource($model))
      ->response()
      ->header('Location', route("{$model->type()}.show", [
        Str::singular($model->type()) => $model,
      ]));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \App\Model  $model
   * @param  array $attributes
   * @return Illuminate\Http\Resources\Json\JsonResource
   */
  public function updateResource($model, $attributes)
  {
    $model->update($attributes);
    return new JSONAPIResource($model);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \App\Model  $model
   * @return \Illuminate\Http\Response
   */
  public function deleteResource($model)
  {
    $model->delete();
    return response(null, 204);
  }
}
