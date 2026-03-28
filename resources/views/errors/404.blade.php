@extends('errors.layout')

@section('title', __('pages.errors.404_title'))

@section('content')
    <p class="code">404</p>
    <p class="message">{{ __('pages.errors.404_message') }}</p>
    <a href="{{ url('/') }}">{{ __('pages.errors.back_home') }}</a>
@endsection
