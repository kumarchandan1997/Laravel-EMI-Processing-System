@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Loan Details</h2>
    <a href="{{ route('emi.process') }}" class="btn btn-primary mb-3">Process Data</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Client ID</th>
                <th>Num of Payments</th>
                <th>First Payment</th>
                <th>Last Payment</th>
                <th>Loan Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($loans as $loan)
            <tr>
                <td>{{ $loan->clientid }}</td>
                <td>{{ $loan->num_of_payment }}</td>
                <td>{{ $loan->first_payment_date }}</td>
                <td>{{ $loan->last_payment_date }}</td>
                <td>{{ $loan->loan_amount }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
