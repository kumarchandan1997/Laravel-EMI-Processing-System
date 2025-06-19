<?php

namespace App\Repositories;

use App\Models\LoanDetail;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class LoanRepository
{
    // Cache keys and TTL in seconds
    private const CACHE_KEY_ALL_LOANS = 'loan_details.all';
    private const CACHE_KEY_MIN_START_DATE = 'loan_details.min_start_date';
    private const CACHE_KEY_MAX_END_DATE = 'loan_details.max_end_date';
    private const CACHE_TTL = 3600;

    public function getAll(): Collection
    {
        try {
            $data = LoanDetail::all();
            return $data->isNotEmpty() ? $data : collect();
        } catch (\Throwable $e) {
            Log::error('Failed to retrieve all loan details', ['error' => $e->getMessage()]);
            return collect();
        }
    }


    /**
     * Get the minimum start date of all loans.
     */
    public function getMinStartDate(): ?string
    {
        try {
            return Cache::remember(self::CACHE_KEY_MIN_START_DATE, self::CACHE_TTL, function () {
                return LoanDetail::min('first_payment_date') ?: null;
            });
        } catch (Throwable $e) {
            Log::error('Failed to retrieve min start date', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Get the maximum end date of all loans.
     */
    public function getMaxEndDate(): ?string
    {
        try {
            return Cache::remember(self::CACHE_KEY_MAX_END_DATE, self::CACHE_TTL, function () {
                return LoanDetail::max('last_payment_date') ?: null;
            });
        } catch (Throwable $e) {
            Log::error('Failed to retrieve max end date', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Process large datasets in chunks for memory efficiency.
     */
    public function chunkForEmiProcessing(int $chunkSize, callable $callback): void
    {
        try {
            LoanDetail::orderBy('id')->chunk($chunkSize, $callback);
        } catch (Throwable $e) {
            Log::error('Error while chunking EMI processing data', [
                'message' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Clear all loan-related caches (can be used after insert/update/delete).
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY_ALL_LOANS);
        Cache::forget(self::CACHE_KEY_MIN_START_DATE);
        Cache::forget(self::CACHE_KEY_MAX_END_DATE);
    }
}
