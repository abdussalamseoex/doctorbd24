@extends('admin.layouts.app')
@section('title', 'Edit Doctor: ' . $doctor->name)
@section('content')

@include('admin.doctors._form')
@endsection
