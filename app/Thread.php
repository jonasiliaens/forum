<?php

namespace App;

use App\Notifications\ThreadWasUpdated;
use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    use RecordsActivity;

    protected $guarded = [];

    protected $with = ['creator', 'channel'];

    protected $appends = ['isSubscribedTo'];

    protected static function boot()
    {
        parent::boot();

        // static::addGlobalScope('replyCount', function ($builder) {
        //     $builder->withCount('replies');
        // });
        //de bedoeling van deze globale querie scope is om bij elke sql querie voor een thread, een attribuut mee te geven dat het aantal replies telt, in dit voorbeeld kan in de view {{ $thread->replies()->count() }} (wat opnieuw een querie uitvoert) vervangen worden door: {{ $thread->replies_count }} waarbij géén nieuwe querie dient te gebeuren.
        //
        //In episode 39 echter wordt deze global querie verwijderd en vervangen door een kolom in de database

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

    /**
     * Add a reply tot the thread.
     *
     * @param array $reply
     * @return $reply
     */
    public function addReply($reply)
    {
        $reply = $this->replies()->create($reply);

        //Prepare notifications for all subscribers
        foreach ($this->subscriptions as $subscription) {
            if ($subscription->user_id != $reply->user_id) {
                $subscription->user->notify(new ThreadWasUpdated($this, $reply));
            }
        }

        return $reply;
    }

    public function scopeFilter($query, $filters)
    {
        return $filters->apply($query);
    }

    public function subscribe($userId = null)
    {
        $this->subscriptions()->create([
            'user_id' => $userId ?: auth()->id()
        ]);

        return $this;
    }

    public function unsubscribe($userId = null)
    {
        $this->subscriptions()
            ->where('user_id', $userId ?: auth()->id())
            ->delete();
    }

    public function subscriptions()
    {
        return $this->hasMany(ThreadSubscription::class);
    }

    public function getIsSubscribedToAttribute()
    {
        return $this->subscriptions()
            ->where('user_id', auth()->id())
            ->exists();
    }
}
