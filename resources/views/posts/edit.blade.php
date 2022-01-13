@extends('layouts.app')

@section('content')
    <h1>Edit Post</h1>
    <form action="/posts" method="POST" enctype="multipart/form-data">
        {{ csrf_field() }}
        {{ method_field('PUT')}}
        <input type="hidden" id="id" name="id" value="{{$post->id}}">
        <label>Title</label><br>
        <input class="form-control" type="text" id="title" name="title" value="{{$post->title}}" placeholder="Title"><br><br>
        <label>Body</label><br>
        <textarea class="form-control" id="body" name="body" rows="4" cols="50" placeholder="Body Text">{{$post->body}}</textarea><br>
        <script>
            CKEDITOR.replace('body');
        </script>
        <input type="file" name="cover_image" id="cover_image"><br><br>
        <input class="btn btn-primary" type="submit" value="Submit">
    </form>
@endsection