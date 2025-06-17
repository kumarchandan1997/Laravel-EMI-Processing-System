<?php

namespace App\Repositories;

use App\Models\LoanDetail;

class LoanRepository
{
    public function getAll()
    {
        return LoanDetail::all();
    }

    public function getMinStartDate()
    {
        return LoanDetail::min('first_payment_date');
    }

    public function getMaxEndDate()
    {
        return LoanDetail::max('last_payment_date');
    }

    public function getAllForEmiProcessing()
    {
        return LoanDetail::all();
    }
}


?>