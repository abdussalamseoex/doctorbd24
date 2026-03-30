@extends('admin.layouts.app')
@section('title', isset($doctor) ? 'Edit Doctor: ' . $doctor->name : 'Add Doctor')
@section('content')

@include('admin.doctors._form')
@endsection
