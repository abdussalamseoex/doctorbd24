@extends('layouts.app')

@section('title', $type->name . ' Service')
@section('meta_description', 'Find the best ' . $type->name . ' services near you on DoctorBD24. Search by location, compare features, and get 24/7 emergency support.')

@section('content')
    @livewire('ambulance-list', ['fixedType' => $type])
@endsection
