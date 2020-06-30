<?php

namespace App\Services;

use Illuminate\Support\Str;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\JSONAPIResource;
use App\Http\Resources\JSONAPICollection;
use App\Http\Resources\JSONAPIIdentifierResource;

class JSONAPIService
{
  /**
   * Fetch the specified resource.
   *
   * @param  \Illuminate\Database\Eloquent\Model $model
   * @param integer $id
   * @param  string $type
   * @return \Illuminate\Http\Response
   */
  public function fetchResource($model, $id = 0, $type = '')
  {
    if ($model instanceof Model) {
      return new JSONAPIResource($model);
    }

    $query = QueryBuilder::for($model::where('id', $id))
      ->allowedIncludes(config("jsonapi.resources.{$type}.allowedIncludes"))
      ->firstOrFail();

    return new JSONAPIResource($query);
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
      ->allowedSorts(config("jsonapi.resources.{$type}.allowedSorts"))
      ->allowedIncludes(config("jsonapi.resources.{$type}.allowedIncludes"))
      ->allowedFilters(config("jsonapi.resources.{$type}.allowedFilters"))
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
   * @param  \Illuminate\Database\Eloquent\Model $model
   * @param  array $attributes
   * @return Illuminate\Http\Resources\Json\JsonResource
   */
  public function updateResource($model, $attributes)
  {
    $model->update($attributes);
    return new JSONAPIResource($model);
  }

  /**
   * Delete the specified resource in storage.
   *
   * @param  \Illuminate\Database\Eloquent\Model $model
   * @return \Illuminate\Http\Response
   */
  public function deleteResource($model)
  {
    $model->delete();
    return response(null, 204);
  }

  /**
   * Fetch the specified resource with related resource in storage.
   *
   * @param  \Illuminate\Database\Eloquent\Model $model
   * @param string $relationship
   * @return \Illuminate\Http\Response
   */
  public function fetchRelationship($model, string $relationship)
  {
    return JSONAPIIdentifierResource::collection($model->$relationship);
  }

  /**
   * Fetch the specified book resource with related authors.
   *
   * @param  \Illuminate\Database\Eloquent\Model $model
   * @param string $relationship
   * @return \Illuminate\Http\Response
   */
  public function fetchRelated($model, string $relationship)
  {
    return new JSONAPICollection($model->$relationship);
  }


  /**
   * Update the specified resource with one-to-many relationship.
   *
   * @param  \Illuminate\Database\Eloquent\Model $model
   * @param string $relationship
   * @param array $ids
   * @return \Illuminate\Http\Response
   */
  public function updateToManyRelationships(
    $model,
    string $relationship,
    array $ids
  ) {
    $foreignKey = $model->$relationship()->getForeignKeyName();
    $relatedModel = $model->$relationship()->getRelated();

    $relatedModel->newQuery()->findOrFail($ids);

    $relatedModel->newQuery()->where($foreignKey, $model->id)->update([
      $foreignKey => null,
    ]);

    $relatedModel->newQuery()->whereIn('id', $ids)->update([
      $foreignKey => $model->id,
    ]);

    return response(null, 204);
  }

  /**
   * Update the specified resource with many-to-many relationship.
   *
   * @param  \Illuminate\Database\Eloquent\Model $model
   * @param string $relationship
   * @param array $ids
   * @return \Illuminate\Http\Response
   */
  public function updateManyToManyRelationships(
    $model,
    string $relationship,
    array $ids
  ) {
    $model->$relationship()->sync($ids);
    return response(null, 204);
  }
}
