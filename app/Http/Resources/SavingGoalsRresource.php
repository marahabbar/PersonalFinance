<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SavingGoalsRresource extends JsonResource
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
            'id'=> $this->id,
            'description'=> $this->description,
            'target_amount'=> $this->target_amount,
            'current_amount'=> $this->current_amount,
            'start_date'=> $this->start_date,
            'end_date'=> $this->end_date,
            'user_id'=> $this->user_id,

        ];
    }
}
