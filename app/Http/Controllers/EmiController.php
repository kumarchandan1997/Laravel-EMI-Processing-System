<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\EmiService;
use App\Repositories\LoanRepository;

class EmiController extends Controller
{
    protected $emiService, $loanRepo;

    public function __construct(EmiService $emiService, LoanRepository $loanRepo)
    {
        $this->middleware('auth');
        $this->emiService = $emiService;
        $this->loanRepo = $loanRepo;
    }

    public function loanDetails()
    {
        $loans = $this->loanRepo->getAll();
        return view('loan-details', compact('loans'));
    }

    public function processEmi()
    {
        $this->emiService->processEmiData();
        return redirect()->route('emi.details');
    }

    public function showEmiDetails()
    {
        $data = $this->emiService->getEmiData();
        $columns = $this->emiService->getEmiColumns();
        return view('emi-details', compact('data', 'columns'));
    }
}
