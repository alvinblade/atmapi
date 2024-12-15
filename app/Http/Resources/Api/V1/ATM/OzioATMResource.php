<?php

namespace App\Http\Resources\Api\V1\ATM;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OzioATMResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'qty_1' => $this->qty_1,
            'qty_5' => $this->qty_5,
            'qty_10' => $this->qty_10,
            'qty_20' => $this->qty_20,
            'qty_50' => $this->qty_50,
            'qty_100' => $this->qty_100,
            'qty_200' => $this->qty_200,
            'qty_500' => $this->qty_500,
            'total_amount' => $this->total_amount
        ];
    }
}
