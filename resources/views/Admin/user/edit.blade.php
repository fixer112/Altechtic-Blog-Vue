@extends('Admin.layout')
@section('page')
Edit User
@endsection
@section('content')
<edituser :id="{{ $id }}" {{-- :user="{{json_encode($user)}}" :roles="{{json_encode($roles)}} --}}></edituser>
@endsection