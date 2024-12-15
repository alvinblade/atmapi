<?php

namespace App\Http\Resources\Api\V1\ATM\BankTransaction;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BankTransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'bank_account_id' => $this->bank_account_id,
            'bankAccount' => $this->whenLoaded('bankAccount'),
            'extracted_amount' => $this->extracted_amount,
            'created_at' => $this->created_at->format('d.m.Y - H:i:s')
        ];
    }
}
