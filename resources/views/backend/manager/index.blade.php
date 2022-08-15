@extends('backend.layouts.master')

@section('header')
    @include("backend.manager.partials.header")
@endsection

@section("sidebar")
    @include('backend.manager.partials.sidebar')
@endsection

@section("content")
    <router-view :key="$route.fullPath"></router-view>
@endsection