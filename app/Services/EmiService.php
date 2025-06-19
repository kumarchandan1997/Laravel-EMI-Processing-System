<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\Repositories\LoanRepository;
use Exception;

class EmiService
{
    protected LoanRepository $loanRepo;

    public function __construct(LoanRepository $loanRepo)
    {
        $this->loanRepo = $loanRepo;
    }

    public function processEmiData(): void
    {
        try {
            $columns = $this->generateMonthColumns();

            if (empty($columns)) {
                Log::warning('No columns generated. Aborting EMI data processing.');
                return;
            }

            $this->rebuildEmiDetailsTable($columns);
            $this->insertEmiRecords($columns);

            Cache::forget('emi_details.all');

            Log::info('EMI data processed successfully.');

        } catch (Exception $e) {
            Log::error('EMI Processing Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    public function getEmiData(): \Illuminate\Support\Collection
    {
        try {
            return DB::table('emi_details')->get();
        } catch (Exception $e) {
            Log::error('Failed to retrieve EMI data', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return collect();
        }
    }


    public function getEmiColumns(): array
    {
        try {
            $columns = DB::select("SHOW COLUMNS FROM emi_details");
            return array_map(fn($col) => $col->Field, $columns);
        } catch (Exception $e) {
            Log::error('Failed to fetch EMI columns', [
                'message' => $e->getMessage(),
            ]);
            return [];
        }
    }

    private function generateMonthColumns(): array
    {
        $start = $this->loanRepo->getMinStartDate();
        $end = $this->loanRepo->getMaxEndDate();

        if (!$start || !$end) {
            Log::warning('Missing loan data: cannot determine date range.');
            return [];
        }

        $start = Carbon::parse($start)->startOfMonth();
        $end = Carbon::parse($end)->startOfMonth();

        $columns = [];

        while ($start <= $end) {
            $columns[] = $start->format('Y_M');
            $start->addMonth();
        }

        return $columns;
    }

    /**
     * Drops and recreates the emi_details table with dynamic month columns.
     */
    private function rebuildEmiDetailsTable(array $columns): void
    {
        DB::statement('DROP TABLE IF EXISTS emi_details');

        $baseColumns = [
            "`id` INT AUTO_INCREMENT PRIMARY KEY",
            "`clientid` INT NOT NULL"
        ];

        $monthColumns = array_map(fn($col) => "`$col` DECIMAL(10,2) NOT NULL DEFAULT 0.00", $columns);

        $sql = implode(", ", array_merge($baseColumns, $monthColumns));

        DB::statement("CREATE TABLE emi_details ($sql) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    /**
     * Calculate EMI values and bulk insert into emi_details table.
     */
    private function insertEmiRecords(array $columns): void
    {
        $columnsList = '`clientid`, ' . implode(', ', array_map(fn($col) => "`$col`", $columns));
        $placeholders = implode(', ', array_fill(0, count($columns) + 1, '?'));

        $this->loanRepo->chunkForEmiProcessing(500, function ($loans) use ($columns, $columnsList, $placeholders) {
            foreach ($loans as $loan) {
                $emiValues = $this->calculateEmiDistribution($loan, $columns);

                $values = array_merge([$loan->clientid], array_values($emiValues));

                // Safe, sanitized insert
                DB::insert("INSERT INTO emi_details ($columnsList) VALUES ($placeholders)", $values);
            }
        });
    }


    /**
     * Split EMI amount into monthly buckets based on payment schedule.
     */
    private function calculateEmiDistribution(object $loan, array $allColumns): array
    {
        $emi = round($loan->loan_amount / $loan->num_of_payment, 2);
        $totalBeforeLast = $emi * ($loan->num_of_payment - 1);
        $lastEmi = round($loan->loan_amount - $totalBeforeLast, 2);

        $distribution = array_fill_keys($allColumns, 0.00);
        $current = Carbon::parse($loan->first_payment_date)->startOfMonth();

        for ($i = 0; $i < $loan->num_of_payment; $i++) {
            $column = $current->format('Y_M');

            if (!isset($distribution[$column])) {
                Log::warning("Missing column $column for client ID {$loan->clientid}");
            } else {
                $distribution[$column] = ($i === $loan->num_of_payment - 1) ? $lastEmi : $emi;
            }

            $current->addMonth();
        }

        return $distribution;
    }
}
