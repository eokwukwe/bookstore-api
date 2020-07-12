<?php

namespace App\Services;

use Illuminate\Support\Str;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\JSONAPIResource;
use App\Http\Resources\JSONAPICollection;
use App\Http\Resources\JSONAPIIdentifierResource;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
     * @param  array $relationships
     * @return \Illuminate\Http\Response
     */
    public function createResource(
        string $modelClass,
        array $attributes,
        array $relationships = null
    ) {
        $model = $modelClass::create($attributes);

        if ($relationships) {
            $this->handleRelationship($relationships, $model);
        }

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
     * @param  array $relationships
     * @return Illuminate\Http\Resources\Json\JsonResource
     */
    public function updateResource(
        $model,
        array $attributes,
        array $relationships = null
    ) {
        $model->update($attributes);

        if ($relationships) {
            $this->handleRelationship($relationships, $model);
        }

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
        if ($model->$relationship instanceof Model) {
            return new JSONAPIIdentifierResource($model->$relationship);
        }

        return JSONAPIIdentifierResource::collection($model->$relationship);
    }

    /**
     * Fetch the specified resource with related authors.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @param string $relationship
     * @return \Illuminate\Http\Response
     */
    public function fetchRelated($model, string $relationship)
    {
        if ($model->$relationship instanceof Model) {
            return new JSONAPIResource($model->$relationship);
        }

        return new JSONAPICollection($model->$relationship);
    }

    /**
     * Update the specified resource with one-to-one relationship.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @param string $relationship
     * @param array $ids
     * @return \Illuminate\Http\Response
     */
    public function updateToOneRelationship($model, $relationship, $id)
    {
        $relatedModel = $model->$relationship()->getRelated();
        $model->$relationship()->dissociate();

        if ($id) {
            $newModel = $relatedModel->newQuery()->findOrFail($id);
            $model->$relationship()->associate($newModel);
        }

        $model->save();

        return response(null, 204);
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

    /**
     * Add relationships with creating and updating a resource.
     *
     * @param array $relationships
     * @param  \Illuminate\Database\Eloquent\Model $model
     */
    protected function handleRelationship(array $relationships, $model): void
    {
        foreach ($relationships as $relationshipName => $contents) {
            if ($model->$relationshipName() instanceof BelongsTo) {
                $this->updateToOneRelationship(
                    $model,
                    $relationshipName,
                    $contents['data']['id']
                );
            }

            if ($model->$relationshipName() instanceof BelongsToMany) {
                $this->updateManyToManyRelationships(
                    $model,
                    $relationshipName,
                    collect($contents['data'])->pluck('id')->toArray()
                );
            }
        }

        $model->load(array_keys($relationships));
    }
}
