@extends('backend.layouts.master')

@section('header')
    @include("backend.supervisor.partials.header")
@endsection

@section("sidebar")
    @include('backend.supervisor.partials.sidebar')
@endsection

@section("content")
    <router-view :key="$route.fullPath"></router-view>
@endsection