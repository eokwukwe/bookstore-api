<?php

namespace App\Http\Resources;

use Illuminate\Support\Str;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Http\Resources\Json\JsonResource;

class JSONAPIResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return array
   */
  public function toArray($request)
  {
    return [
      'id' => (string) $this->id,
      'type' => $this->type(),
      'attributes' => $this->allowedAttributes(),
      'relationships' => $this->prepareRelationships(),
    ];
  }

  public function included($request)
  {
    return collect($this->relations())
      ->filter(function ($resource) {
        return $resource->collection !== null;
      })->flatMap->toArray($request);
  }

  public function with($request)
  {
    $with = [];

    if ($this->included($request)->isNotEmpty()) {
      $with['included'] = $this->included($request);
    }

    return $with;
  }

  private function relations()
  {
    return collect(config("jsonapi.resources.{$this->type()}.relationships"))
      ->map(function ($relation) {
        return JSONAPIResource::collection($this->whenLoaded(
          $relation['method']
        ));
      });
  }

  private function prepareRelationships()
  {
    return collect(config("jsonapi.resources.{$this->type()}.relationships"))
      ->flatMap(function ($related) {
        $relatedType = $related['type'];
        $relationship = $related['method'];
        return [
          $relatedType => [
            'links' => [
              'self' => route(
                "{$this->type()}.relationships.{$relatedType}",
                [Str::singular($this->type()) => $this->id]
              ),
              'related' => route(
                "{$this->type()}.{$relatedType}",
                [Str::singular($this->type()) => $this->id]
              ),
            ],
            'data' => !$this->whenLoaded($relationship)
              instanceof MissingValue ? JSONAPIIdentifierResource::collection($this->{$relationship}) : new MissingValue(),
          ],
        ];
      });
  }
}
