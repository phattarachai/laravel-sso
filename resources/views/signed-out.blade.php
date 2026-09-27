@extends('sso::layout')

@section('title', 'Signed out')

@section('content')
    <h1>You're signed out</h1>
    <p>{{ config('app.name') }} has ended this session.</p>
    <a class="button" href="{{ route('login') }}">Sign in again</a>
@endsection
