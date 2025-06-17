@extends('layouts.app')

@section('content')
<div class="container">
    <h2>EMI Details</h2>
    <a href="{{ route('emi.process') }}" class="btn btn-warning mb-3">Reprocess EMI</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                @foreach($columns as $col)
                    <th>{{ $col }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
            <tr>
                @foreach($columns as $col)
                    <td>{{ $row->$col ?? '0.00' }}</td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
