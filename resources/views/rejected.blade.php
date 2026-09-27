@extends('sso::layout')

@section('title', 'Sign-in refused')

@section('content')
    <h1>This account can't sign in here</h1>
    @if ($email)
        <p>You're signed in to Google as <strong>{{ $email }}</strong>, which isn't allowed for {{ config('app.name') }}.</p>
    @else
        <p>The sign-in couldn't be completed. Try again, or pick a different account.</p>
    @endif
    <a class="button" href="{{ route('login', ['select' => 1]) }}">Use another account</a>
@endsection
