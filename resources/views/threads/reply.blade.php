<div id="reply-{{ $reply->id }}" class="panel panel-default">
	<div class="panel-heading">
		<div class="level">
			<h5 class="flex">
				<a href="{{ route('profile', $reply->owner) }}">
					{{ $reply->owner->name }}
				</a> said {{ $reply->created_at->diffForHumans() }}...
			</h5>

			<div>
				<form method="POST" action="/replies/{{ $reply->id }}/favorites">
				    {{ csrf_field() }}
				    <div class="form-group">
				        <button type="submit" class="btn btn-primary" {{ $reply->isFavorited() ? 'disabled' : '' }}>
				        	{{ $reply->favorites_count }} {{ str_plural('Favorite', $reply->favorites_count) }}
				        </button>
				    </div>
				</form>
			</div>
		</div>
	</div>  
	<div class="panel-body">
		{{ $reply->body }}
	</div>

	@can ('update', $reply)
	    <div class="panel-footer">
			<form method="POST" action="/replies/{{ $reply->id }}">
			    {{ csrf_field() }}
			    {{ method_field('DELETE') }}
			    
			    <button type="submit" class="btn btn-danger btn-xs">Delete</button>
			</form>
		</div>
	@endcan
	
</div>