@extends('layouts.master')

@section('content')


    <a href="{{ url('logout')  }}">LogOut</a>

    @foreach($Posts as $post)
    <div class="panel-body">
        {{ $post['story'] }}
    </div>
    @endforeach
@endsection