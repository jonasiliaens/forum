@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="level">
                        <span class="flex">
                            <a href="{{ route('profile', $thread->creator) }}">{{ $thread->creator->name }}</a> posted:
                            {{ $thread->title }}
                        </span>

                        @can ('update', $thread)
                            <form method="POST" action="{{ $thread->path() }}">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}

                                <button type="submit" class="btn btn-link">Delete Thread</button>
                            </form>
                        @endcan
                    </div>
                </div>

                <div class="panel-body">
                    {{ $thread->body }}
                </div>
            </div>

            @foreach ($replies as $reply)
                @include ('threads.reply')
            @endforeach 

            {{ $replies->links() }}

            @auth
                <form method="POST" action="{{ $thread->path() . '/replies' }}">
                    {{ csrf_field() }}
                    
                        <div class="form-group">
                            <textarea type="text" name="body" id="body" class="form-control" placeholder="Have something to say?" rows="5">{{ old('body') }}</textarea>
                        </div>
                
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Verzenden</button>
                    </div>
                </form>
            @endauth

            @guest
                <div class="row">
                    <div class="text-center">
                        <p>Please <a href="{{ route('login') }}">sign in</a> to post a reply.</p>
                    </div>
                </div>
            @endguest
        </div>

        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-body">
                    <p>
                        This thread was published {{ $thread->created_at->diffForHumans() }} by <a href="#">{{ $thread->creator->name }}</a> and has {{ $thread->replies_count }} {{ str_plural('reply', $thread->replies_count) }}.
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
