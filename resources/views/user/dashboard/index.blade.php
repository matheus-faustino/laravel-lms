@extends('layouts.user')

@section('title', __('user/dashboard.title'))

@section('breadcrumbs')
    <x-breadcrumb :items="[
        ['label' => __('user/dashboard.title')],
    ]" />
@endsection

@section('content')

@endsection
