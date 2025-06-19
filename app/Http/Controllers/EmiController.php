<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\EmiService;
use App\Repositories\LoanRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class EmiController extends Controller
{
    protected EmiService $emiService;
    protected LoanRepository $loanRepo;

    public function __construct(EmiService $emiService, LoanRepository $loanRepo)
    {
        $this->middleware('auth');
        $this->emiService = $emiService;
        $this->loanRepo = $loanRepo;
    }

   
    public function loanDetails(): View
    {
        $loans = $this->loanRepo->getAll();
        return view('loan-details', compact('loans'));
    }

   
    public function processEmi(): RedirectResponse
    {
        try {
            $this->emiService->processEmiData();
            return redirect()->route('emi.details')->with('success', 'EMI Processed Successfully.');
        } catch (\Throwable $e) {
            Log::error('EMI Processing Failed', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to process EMI. Please check logs.');
        }
    }

    public function showEmiDetails(): View
    {
        $data = $this->emiService->getEmiData();
        $columns = $this->emiService->getEmiColumns();
        return view('emi-details', compact('data', 'columns'));
    }

    public function checkLoanData(): JsonResponse
   {
        $hasData = $this->loanRepo->getAll()->isNotEmpty();
        return response()->json(['exists' => $hasData]);
   }
}
