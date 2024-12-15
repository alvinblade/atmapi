<?php

namespace App\Http\Controllers\Api\V1\ATM;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ATM\OzioATMRequest;
use App\Http\Resources\Api\V1\ATM\BankTransaction\BankTransactionResource;
use App\Http\Resources\Api\V1\ATM\OzioATMResource;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\OzioATM;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OzioATMController extends Controller
{
    use HttpResponses;

    public function showATM(): \Throwable|JsonResponse
    {
        $atm = OzioATM::query()->firstOrFail();

        return $this->success(data: OzioATMResource::make($atm));
    }

    public function updateATM(OzioATMRequest $request): \Throwable|JsonResponse
    {
        $atm = OzioATM::query()->firstOrFail();

        $qty1 = $request->input('qty_1', $atm->qty_1);
        $qty5 = $request->input('qty_5', $atm->qty_5);
        $qty10 = $request->input('qty_10', $atm->qty_10);
        $qty20 = $request->input('qty_20', $atm->qty_20);
        $qty50 = $request->input('qty_50', $atm->qty_50);
        $qty100 = $request->input('qty_100', $atm->qty_100);
        $qty200 = $request->input('qty_200', $atm->qty_200);
        $qty500 = $request->input('qty_500', $atm->qty_500);

        $totalAmount = $qty1 + $qty5 * 5 + $qty10 * 10 + $qty20 * 20 +
            $qty50 * 50 + $qty100 * 100 + $qty200 * 200 + $qty500 * 500;

        $atm->update([
            'qty_1' => $qty1,
            'qty_5' => $qty5,
            'qty_10' => $qty10,
            'qty_20' => $qty20,
            'qty_50' => $qty50,
            'qty_100' => $qty100,
            'qty_200' => $qty200,
            'qty_500' => $qty500,
            'total_amount' => $totalAmount
        ]);

        return $this
            ->success(
                data: OzioATMResource::make($atm),
                message: "ATM məlumatları yeniləndi"
            );
    }

    public function withdraw(Request $request, $bankAccountId): JsonResponse
    {
        $bankAccount = $this->findUserBankAccount($bankAccountId);

        $request->validate([
            'amount' => ['required', 'integer', 'min:1', 'max:' . $bankAccount->balance]
        ]);

        $withdrawAmount = $request->input('amount');
        $atm = OzioATM::query()->firstOrFail();

        $results = $this->calculateWithdrawal($withdrawAmount, $this->getBankNotes());

        if (empty($results)) {
            return $this->handleResidualBankNotes($this->getBankNotes());
        }

        $bankTransaction = BankTransaction::query()->create([
            'bank_account_id' => $bankAccountId,
            'extracted_amount' => $withdrawAmount
        ]);

        $this->updateATMAndAccount($atm, $bankAccount, $results, $withdrawAmount);

        return $this->success(
            data: BankTransactionResource::make($bankTransaction),
            message: "Çıxarış uğurla həyata keçirildi",
            code: 201
        );
    }

    private function findUserBankAccount($bankAccountId): BankAccount
    {
        return BankAccount::query()
            ->where('user_id', auth()->id())
            ->findOrFail($bankAccountId);
    }

    private function calculateWithdrawal(int $withdrawAmount, array $bankNotes): array
    {
        $results = [];
        foreach ($bankNotes as $nominal) {
            $qty = min(floor($withdrawAmount / $nominal['nominal']), $nominal['qty']);
            if ($qty > 0) {
                $withdrawAmount -= $qty * $nominal['nominal'];
                $results[] = [
                    'nominal' => $nominal['nominal'],
                    'qty' => $qty
                ];
            }
        }

        return $results;
    }

    private function handleResidualBankNotes(array $bankNotes): JsonResponse
    {
        $currentNominals = [];

        foreach ($bankNotes as $nominal) {
            if ($nominal['qty'] > 0) {
                $currentNominals[] = $nominal['nominal'];
            }
        }

        return $this->error(
            message: "İstənilən məbləğdə əskinazlar mövcud deyil. Qalan əskinazlar: " . implode(', ', $currentNominals),
            code: 400
        );
    }

    private function updateATMAndAccount(OzioATM $atm, BankAccount $bankAccount,
                                         array   $results, int $withdrawAmount): void
    {
        foreach ($results as $result) {
            $this->updateATMQuantities($atm, $result['nominal'], $result['qty']);
        }

        $atm->decrement('total_amount', $withdrawAmount);
        $bankAccount->decrement('balance', $withdrawAmount);
    }

    private function updateATMQuantities(OzioATM $atm, int $nominal, int $qty): void
    {
        $fields = [
            1 => 'qty_1', 5 => 'qty_5', 10 => 'qty_10', 20 => 'qty_20',
            50 => 'qty_50', 100 => 'qty_100', 200 => 'qty_200', 500 => 'qty_500'
        ];

        if (isset($fields[$nominal])) {
            $atm->decrement($fields[$nominal], $qty);
        }
    }

    private function getBankNotes(): array
    {
        $atm = OzioATM::query()->firstOrFail();

        return [
            ['nominal' => 500, 'qty' => $atm->qty_500],
            ['nominal' => 200, 'qty' => $atm->qty_200],
            ['nominal' => 100, 'qty' => $atm->qty_100],
            ['nominal' => 50, 'qty' => $atm->qty_50],
            ['nominal' => 20, 'qty' => $atm->qty_20],
            ['nominal' => 10, 'qty' => $atm->qty_10],
            ['nominal' => 5, 'qty' => $atm->qty_5],
            ['nominal' => 1, 'qty' => $atm->qty_1],
        ];
    }
}
