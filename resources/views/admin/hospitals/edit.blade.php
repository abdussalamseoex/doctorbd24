@extends('admin.layouts.app')
@section('title', 'Edit Hospital: ' . $hospital->name)
@section('content')
    @include('admin.hospitals._form')
@endsection
