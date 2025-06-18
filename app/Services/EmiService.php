<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Repositories\LoanRepository;
use Illuminate\Support\Facades\Log;


class EmiService
{
    protected $loanRepo;

    public function __construct(LoanRepository $loanRepo)
    {
        $this->loanRepo = $loanRepo;
    }

        public function processEmiData()
       {

        try {
            // Step 1: Drop and recreate emi_details table
            DB::statement('DROP TABLE IF EXISTS emi_details');

            $minDate = Carbon::parse($this->loanRepo->getMinStartDate())->startOfMonth();
            $maxDate = Carbon::parse($this->loanRepo->getMaxEndDate())->startOfMonth();

            $columns = [];
            while ($minDate <= $maxDate) {
                $columns[] = $minDate->format('Y_M');
                $minDate->addMonth();
            }

            $columnSql = "`id` INT AUTO_INCREMENT PRIMARY KEY, `clientid` INT";
            foreach ($columns as $col) {
                $columnSql .= ", `$col` DECIMAL(10,2) DEFAULT 0.00";
            }

            DB::statement("CREATE TABLE emi_details ($columnSql)");

            foreach ($this->loanRepo->getAllForEmiProcessing() as $loan) {
                $monthlyEmi = round($loan->loan_amount / $loan->num_of_payment, 2);
                $totalBeforeLast = $monthlyEmi * ($loan->num_of_payment - 1);
                $lastEmi = round($loan->loan_amount - $totalBeforeLast, 2);

                $currentDate = Carbon::parse($loan->first_payment_date)->startOfMonth();
                $values = array_fill_keys($columns, 0.00);

                for ($i = 0; $i < $loan->num_of_payment; $i++) {
                    $col = $currentDate->format('Y_M');
                    $values[$col] = $i == $loan->num_of_payment - 1 ? $lastEmi : $monthlyEmi;
                    $currentDate->addMonth();
                }

                $colNames = '`clientid`, ' . implode(', ', array_map(fn($k) => "`$k`", array_keys($values)));
                $colValues = array_merge([$loan->clientid], array_values($values));
                $placeholders = implode(', ', array_fill(0, count($colValues), '?'));

                DB::insert("INSERT INTO emi_details ($colNames) VALUES ($placeholders)", $colValues);
            }
            Log::info('EMI data processed successfully.');

        } catch (\Exception $e) {
            Log::error('EMI Processing Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getEmiData()
    {
        return DB::table('emi_details')->get();
    }

    public function getEmiColumns()
    {
        $columns = DB::select("SHOW COLUMNS FROM emi_details");
        return array_map(fn($col) => $col->Field, $columns);
    }
}



?>