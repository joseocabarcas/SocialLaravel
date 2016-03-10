@extends('layouts.master')

@section('content')
    <a href=" {{ $login_url }} " class="btn btn-primary">Login with Facebook (SDK)</a>

    <a class="btn btn-primary" href="{{ action('SocialController@redirectToProvider') }}"><span class="glyphicon glyphicon-thumbs-up"></span> Login with Facebook (Socialite)</a>
@endsection