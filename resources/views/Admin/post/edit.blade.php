@extends('Admin.layout')
@section('page')
Edit Post
@endsection
@section('content')
<editpost :id="{{ $id }}"></editpost>
@endsection