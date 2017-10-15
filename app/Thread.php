<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    use RecordsActivity;

    protected $guarded = [];

    protected $with = ['creator', 'channel'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('replyCount', function ($builder) {
            $builder->withCount('replies');
        });
        //de bedoeling van deze globale querie scope is om bij elke sql querie voor een thread, een attribuut mee te geven dat het aantal replies telt, in dit voorbeeld kan in de view {{ $thread->replies()->count() }} (wat opnieuw een querie uitvoert) vervangen worden door: {{ $thread->replies_count }} waarbij géén nieuwe querie dient te gebeuren.

        static::deleting(function ($thread) {
            $thread->replies->each->delete();
        });
    }
    
    public function path()
    {
        return "/threads/{$this->channel->slug}/{$this->id}";
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
        //Omdat de method creator noemt ipv user moeten we specifiek benadrukken dat user_id de pirmary key is waarmee de relatie gelinkt wordt.
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function addReply($reply)
    {
        $this->replies()->create($reply);
    }

    public function scopeFilter($query, $filters)
    {
        return $filters->apply($query);
    }
}
