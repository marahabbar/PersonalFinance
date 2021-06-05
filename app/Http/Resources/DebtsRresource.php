<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DebtsRresource extends JsonResource
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
            'amount'=> $this->amount,
            'rewind_amount'=> $this->rewind_amount,
            'creditor'=> $this->creditor,
            'debtor'=> $this->debtor,
            'debt_date'=> $this->debt_date,
            'due_date'=> $this->due_date,
        ];
    }
}
