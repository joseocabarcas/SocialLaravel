@extends('layouts.master')

@section('content')



    <a href="{{ url('logout')  }}">LogOut</a>
    
    <h4>{{ Auth::user()->name  }}</h4>
    <img src="{{ Auth::user()->avatar }}" height="200" width="200">


@endsection