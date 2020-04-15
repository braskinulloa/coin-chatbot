@extends('welcome')
@section('title', 'Historical')
@section('content')

<ul>
@forelse (Auth::user()->transactions as $h)
    <li>{{ $h->type }}</li>
@empty
    <li>No previous transactions</li>
@endforelse
</ul> 
@endsection
