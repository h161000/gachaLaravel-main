<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class ProductLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'updated_at'=>$this->updated_at->diffForHumans(),
            'updated_at_time'=>$this->updated_at->format('Y年n月j日 H:i'),
            'delivery_deadline'=>$this->updated_at->addDays(7)->format('Y年n月j日 H:i')
        ];
    }
}
