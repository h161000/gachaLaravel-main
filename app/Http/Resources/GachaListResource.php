<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GachaListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $count_rest = $this->count_card-$this->count; 
        $timeStatus = $this->timeStatus();

        return [
            'id'=>$this->id,
            'point'=>$this->point,
            'count_card'=>$this->count_card,
            'count'=>$this->count,
            'ableCount'=>$this->ableCount,
            'count_rest'=>$count_rest,
            'image'=>getGachaImageUrl($this->image),
            'thumbnail'=>getGachaThumbnailUrl($this->thumbnail),
            'status'=>$this->status,
            'gacha_limit'=>$this->gacha_limit,
            'timeStatus'=>$timeStatus,
            'remaining'=>$timeStatus == 0 ? (strtotime($this->start_time) - strtotime(date('Y-m-d H:i:s'))) : 
                ($this->end_time ? strtotime($this->end_time) - strtotime(date('Y-m-d H:i:s')) : 0),
            'start_time'=>date('Y年n月j日 H:i', strtotime($this->start_time)),
            'buttons'=>$this->buttons
        ];
    }
}
