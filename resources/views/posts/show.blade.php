@extends('layouts.app')

@section('content')
    <div class="container">
        <a href="javascript:history.back()" class="btn btn-default">Go Back</a>
    </div><br/>
    <h1>{{$post->title}}</h1>
    <img style="width:100%" src="/storage/cover_image/{{$post->cover_image}}"><br/>
    <div class="card card-body bg-light">
        {!!$post->body!!}
    </div>
    <hr>
    <small>Written on {{$post->created_at}} by {{$post->user->name}}</small>
    <hr>
    @if(!Auth::guest())
        @if(Auth::user()->id == $post->user_id)
            <a href="/posts/{{$post->id}}/edit" class="btn btn-info">Edit</a><br /><br />
            <form action="/posts" method="POST">
                {{ csrf_field() }}
                {{ method_field('DELETE')}}
                <input type="hidden" id="id" name="id" value="{{$post->id}}" />
                <input class="btn btn-danger" type="submit" value="Delete" />
            </form>
        @endif
    @endif
@endsection