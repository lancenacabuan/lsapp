@extends('layouts.app')

@section('content')
    <h1>Posts</h1>
    @if(count($posts) > 0)
        @foreach ($posts as $post)    
            <div class="card card-body bg-light">
                <div class="row">
                    <div class="col-md-4 col-sm-4">
                        <img style="width:100%" src="/storage/cover_image/{{$post->cover_image}}"><br/>
                    </div>
                    <div class="col-md-8 col-sm-8">
                        <h3><a href="/posts/{{$post->id}}" class="btn btn-lg btn-default" style="font-size: 30px;">{{$post->title}}</a></h3>
                        <small>Written on {{$post->created_at}} by {{$post->user_name}}</small>
                    </div>
                </div>
            </div>
            <hr>
        @endforeach    
    @else
        <p>No posts found</p>
    @endif
@endsection