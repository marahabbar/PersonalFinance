<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoriesRresource extends JsonResource
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
            'name'=> $this->name,
            'max_amount'=> $this->max_amount,
            'current_amount'=> $this->current_amount,
            'type'=> $this->type,
            'user_id'=> $this->usert_id,
        ];
    }
}
