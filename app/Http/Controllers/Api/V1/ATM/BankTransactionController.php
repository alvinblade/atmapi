<?php

namespace App\Http\Controllers\Api\V1\ATM;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ATM\BankTransaction\BankTransactionCollection;
use App\Http\Resources\Api\V1\ATM\BankTransaction\BankTransactionResource;
use App\Models\BankTransaction;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BankTransactionController extends Controller
{
    use HttpResponses;

    public function getAllBankTransactions(Request $request): JsonResponse
    {
        $bankTransactions = BankTransaction::query()
            ->with([
                'bankAccount:id,balance,user_id',
                'bankAccount.user:id,name,surname'
            ])->paginate($request->input('limit', 10));

        return $this->success(
            data: new BankTransactionCollection($bankTransactions)
        );
    }

    public function getBankTransactionsByUser(Request $request, $userId): JsonResponse
    {
        $bankTransactions = BankTransaction::query()
            ->whereHas('bankAccount', function ($query) use ($userId) {
                $query->where('user_id', '=', $userId);
            })
            ->paginate($request->input('limit', 10));

        return $this->success(
            data: new BankTransactionCollection($bankTransactions)
        );
    }

    public function getBankTransactionsByAuth(Request $request): JsonResponse
    {
        $bankTransactions = BankTransaction::query()
            ->whereHas('bankAccount', function ($query) {
                $query->where('user_id', '=', auth()->id());
            })
            ->paginate($request->input('limit', 10));

        return $this->success(
            data: new BankTransactionCollection($bankTransactions)
        );
    }

    public function showBankTransaction($bankTransactionId): JsonResponse
    {
        $bankTransAction = BankTransaction::query()
            ->with([
                'bankAccount:id,balance,user_id',
                'bankAccount.user:id,name,surname'
            ])->findOrFail($bankTransactionId);

        return $this->success(
            data: BankTransactionResource::make($bankTransAction)
        );
    }

    public function deleteBankTransaction($bankTransactionId): JsonResponse
    {
        $bankTransaction = BankTransaction::query()
            ->with(['bankAccount'])
            ->findOrFail($bankTransactionId);

        if (auth()->user()->is_admin) {
            $bankTransaction->delete();

            return $this->success(
                message: "Bank köçürməsi uğurla silindi"
            );
        }

        if (auth()->user()->is_featured && auth()->id() == $bankTransaction->bankAccount->user_id) {
            $bankTransaction->delete();
        } else {
            return $this->error(
                message: "Bank köçürməsini silməyə səlahiyyətiniz yoxdur",
                code: 403
            );
        }

        return $this->success(
            message: "Bank köçürməsi uğurla silindi"
        );
    }
}
