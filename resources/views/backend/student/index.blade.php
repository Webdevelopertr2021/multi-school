@extends('backend.layouts.master')

@section('header')
    @include("backend.student.partials.header")
@endsection

@section("sidebar")
    @include('backend.student.partials.sidebar')
@endsection

@section("content")
    <router-view :key="$route.fullPath"></router-view>
@endsection