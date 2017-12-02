<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotificationsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_notification_is_prepared_when_a_subscribed_thread_receives_a_new_reply_that_is_not_by_the_current_user()
    {
        //Als ik een authenticated gerbuiker heb
        $this->signIn();

        //En een thread waarop de gebruiker zich subscribed
        $thread = create('App\Thread')->subscribe();

        //zijn er geen notificaties
        $this->assertCount(0, auth()->user()->notifications);

        //Als de authenticated user een reply geeft op die thread...
        $thread->addReply([
            'user_id' => auth()->id(),
            'body' => 'Some reply here'
        ]);

        //mag de gebruiker geen notificatie krijgen van zijn eigen reply.
        $this->assertCount(0, auth()->user()->fresh()->notifications);

        //Maar als een andere user een reply geeft op de thread...
        $thread->addReply([
            'user_id' => create('App\User')->id,
            'body' => 'Some reply here'
        ]);

        //moet de gebruiker wel een notificatie krijgen.
        $this->assertCount(1, auth()->user()->fresh()->notifications);
    }
}
