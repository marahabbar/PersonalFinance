<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class IncomeRresource extends JsonResource
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
        'description' =>$this->description,
        'amount'=>$this->amount,
        'saving_amount'=>$this->saving_amount,
        'monthly'=>$this->monthly,
        'user_id'=>$this->user_id,
        'category_id'=>$this->category_id,
        'date'=>$this->date
    ];
        //parent::toArray($request);
    }
}
