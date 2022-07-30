@extends("backend.layouts.master")
@section("title")
Dashboard
@endsection

@section('header')
    @include("backend.admin.partials.header")
@endsection

@section('sidebar')
    @include("backend.admin.partials.sidebar")
@endsection

@section('content')
    <router-view :key="$route.fullPath"></router-view>
@endsection