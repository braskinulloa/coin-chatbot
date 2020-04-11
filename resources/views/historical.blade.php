@extends('welcome')
@section('title', 'Historical')
@section('content')
<ul>
@forelse ($historical as $h)
    <li>{{ $h }}</li>
@empty
    <li>No previous transactions</li>
@endforelse
</ul> 
@endsection
