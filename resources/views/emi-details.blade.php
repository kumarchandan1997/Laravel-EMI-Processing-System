@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
        <h2 class="mb-2 mb-md-0">EMI Details</h2>
        <a href="{{ route('emi.process') }}" class="btn btn-warning">Reprocess EMI</a>
    </div>

    {{-- Table Responsive Wrapper --}}
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    @foreach($columns as $col)
                        <th scope="col">{{ $col }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($data as $row)
                    <tr>
                        @foreach($columns as $col)
                            <td>{{ number_format((float)($row->$col ?? 0), 2, '.', '') }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
