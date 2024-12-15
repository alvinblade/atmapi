<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\OzioATM;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SeederATM extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $qty1 = 10;
        $qty5 = 5;
        $qty10 = 1;
        $qty20 = 1;
        $qty50 = 1;
        $qty100 = 1;
        $qty200 = 1;
        $qty500 = 1;

        $totalAZN = $qty1 + $qty5 * 5 + $qty10 * 10 + $qty20 * 20 +
            $qty50 * 50 + $qty100 * 100 + $qty200 * 200 + $qty500 * 500;

        OzioATM::query()->create([
            "qty_1" => $qty1,
            "qty_5" => $qty5,
            "qty_10" => $qty10,
            "qty_20" => $qty20,
            "qty_50" => $qty50,
            "qty_100" => $qty100,
            "qty_200" => $qty200,
            "qty_500" => $qty500,
            "total_amount" => $totalAZN
        ]);

        BankAccount::query()->create([
            "user_id" => 1,
            "balance" => 100
        ]);
    }
}
