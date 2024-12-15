<?php

namespace App\Http\Controllers\Api\V1\ATM;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ATM\BankAccount\BankAccountCollection;
use App\Http\Resources\Api\V1\ATM\BankAccount\BankAccountResource;
use App\Models\BankAccount;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use function Symfony\Component\Translation\t;

class BankAccountController extends Controller
{
    use HttpResponses;

    public function getAllBankAccounts(Request $request): JsonResponse
    {
        $bankAccounts = BankAccount::query()
            ->paginate($request->input('limit', 10));

        return $this->success(
            data: new BankAccountCollection($bankAccounts)
        );
    }

    public function getAuthUserBankAccounts(Request $request): JsonResponse
    {
        $bankAccountsOfAuthUser = BankAccount::query()
            ->where('user_id', '=', auth()->id())
            ->paginate($request->input('limit', 10));

        return $this->success(
            data: new BankAccountCollection($bankAccountsOfAuthUser)
        );
    }

    public function createBankAccount(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'balance' => ['required', 'numeric', 'min:0'],
            'is_blocked' => ['nullable', 'boolean']
        ]);

        $bankAccount = BankAccount::query()->create([
            'user_id' => $request->input('user_id'),
            'balance' => $request->input('balance'),
            'is_blocked' => $request->input('is_blocked', false)
        ]);

        return $this->success(
            data: BankAccountResource::make($bankAccount),
            message: "Bank hesabı uğurla əlavə olundu", code: 201);
    }

    public function updateBankAccount(Request $request, $bankAccountId): JsonResponse
    {
        $request->validate([
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'balance' => ['nullable', 'numeric', 'min:0'],
            'is_blocked' => ['nullable', 'boolean']
        ]);

        $bankAccount = BankAccount::query()->findOrFail($bankAccountId);

        $bankAccount->update([
            'user_id' => $request->input('user_id', $bankAccount->user_id),
            'balance' => $request->input('balance', $bankAccount->balance),
            'is_blocked' => $request->input('is_blocked', false)
        ]);

        return $this->success(data: BankAccountResource::make($bankAccount),
            message: "Bank hesabı uğurla yeniləndi");
    }

    public function deleteBankAccount($bankAccountId): JsonResponse
    {
        $bankAccount = BankAccount::query()->findOrFail($bankAccountId);

        $bankAccount->delete();

        return $this->success(message: "Bank hesabı uğurla silindi");
    }
}
