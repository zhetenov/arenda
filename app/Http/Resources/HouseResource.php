<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\House;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * Class HouseResource
 * @package App\Http\Resources
 * @mixin House
 */
class HouseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'description'   => $this->description,
            'rooms'         => $this->rooms,
            'is_active'     => $this->is_active,
            'images'        => HouseImageResource::collection($this->images),
            'user_id'       => $this->user_id,
            'area'          => $this->area,
            'address'       => $this->address,
            'city'          => new CityResource($this->region->city),
            'reviews'       => ReviewResource::collection($this->reviews)
        ];
    }
}