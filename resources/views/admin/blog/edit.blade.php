@extends('admin.layouts.app')
@section('title', 'Edit Post: ' . $blogPost->title)
@section('content')

@include('admin.blog.create')

@endsection
