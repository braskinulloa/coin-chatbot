@extends('welcome')
@section('title', 'Historical')
@section('content')

<table class="table">
    <thead>
      <tr>
        <th scope="col">Type</th>
        <th scope="col">Amount</th>
        <th scope="col">Currency</th>
        <th scope="col">Balance</th>
        <th scope="col">Date</th>
      </tr>
    </thead>
    <tbody>
@forelse (Auth::user()->transactions as $h)
    <tr>
        <th>{{ $h->type }}</th>
        <td>{{ $h->amount }}</td>
        <td>{{ $h->currency }}</td>
        <td>{{ $h->balance }}</td>
        <td>{{ $h->created_at }}</td>
    </tr>
@empty
    <tr><td class="text-center" colspan="5">No previous transactions</td></tr>
@endforelse
@endsection
    </tbody>
</table>
