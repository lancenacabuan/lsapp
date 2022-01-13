@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <a href="/posts/create" class="btn btn-primary">Create Post</a><br><br>
                    <h3>Your Blog Posts</h3>
                    @if(count($posts) > 0)
                        <table class="table table-striped">
                            <tr>
                                <th>Title</th>
                                <th></th>
                                <th></th>
                            </tr>
                            @foreach($posts as $post)
                                <tr>
                                    <td><a href="/posts/{{$post->id}}" class="btn btn-default"><strong>{{$post->title}}</strong></a></td>
                                    <td style="text-align:right"><a href="/posts/{{$post->id}}/edit" class="btn btn-info">Edit</a></td>
                                    <td style="text-align:right">
                                        <form action="/posts" method="POST">
                                            {{ csrf_field() }}
                                            {{ method_field('DELETE')}}
                                            <input type="hidden" id="id" name="id" value="{{$post->id}}" />
                                            <input class="btn btn-danger" type="submit" value="Delete" />
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    @else
                        <p>You have no posts.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
