@extends('errors.layout')

@section('title', __('pages.errors.500_title'))

@section('content')
    <p class="code">500</p>
    <p class="message">{{ __('pages.errors.500_message') }}</p>
    <a href="{{ url('/') }}">{{ __('pages.errors.back_home') }}</a>
@endsection
