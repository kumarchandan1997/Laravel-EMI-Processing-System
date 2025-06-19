@extends('layouts.app')

@section('content')
<div class="container">
    {{-- Header and Button --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
        <h2 class="mb-2 mb-md-0">Loan Details</h2>
        <a href="{{ route('emi.process') }}" class="btn btn-primary">Process Data</a>
    </div>

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Responsive Table --}}
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th scope="col">Client ID</th>
                    <th scope="col">Num of Payments</th>
                    <th scope="col">First Payment</th>
                    <th scope="col">Last Payment</th>
                    <th scope="col">Loan Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($loans as $loan)
                    <tr>
                        <td>{{ $loan->clientid }}</td>
                        <td>{{ $loan->num_of_payment }}</td>
                        <td>{{ \Carbon\Carbon::parse($loan->first_payment_date)->format('Y-m-d') }}</td>
                        <td>{{ \Carbon\Carbon::parse($loan->last_payment_date)->format('Y-m-d') }}</td>
                        <td>₹{{ number_format($loan->loan_amount, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No loan records available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
