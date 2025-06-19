<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LoanDetail;
use Illuminate\Support\Facades\Log;
use Exception;

class LoanDetailsSeeder extends Seeder
{
   
    public function run(): void
    {
        $loans = $this->getLoanSeedData();

        try {
            LoanDetail::insert($loans);
            Log::info('LoanDetailsSeeder: Successfully seeded loan details.');
        } catch (Exception $e) {
            Log::error('LoanDetailsSeeder Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
        }
    }


    private function getLoanSeedData(): array
    {
        return [
            [
                'clientid'           => 1001,
                'num_of_payment'     => 12,
                'first_payment_date' => '2018-06-29',
                'last_payment_date'  => '2019-05-29',
                'loan_amount'        => 1550.00,
            ],
            [
                'clientid'           => 1003,
                'num_of_payment'     => 7,
                'first_payment_date' => '2019-02-15',
                'last_payment_date'  => '2019-08-15',
                'loan_amount'        => 6851.94,
            ],
            [
                'clientid'           => 1005,
                'num_of_payment'     => 17,
                'first_payment_date' => '2017-11-09',
                'last_payment_date'  => '2019-03-09',
                'loan_amount'        => 1800.01,
            ],
        ];
    }
}
