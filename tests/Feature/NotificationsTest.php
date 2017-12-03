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
        //Als ik een authenticated gebruiker heb
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

    /** @test */
    public function a_user_can_fetch_their_unread_notifications()
    {
        //Als ik een authenticated gebruiker heb
        $this->signIn();

        //Die zich subscribed op een thread
        $thread = create('App\Thread')->subscribe();

        //En als een andere gebruiker een reply geeft op de thread...
        $thread->addReply([
            'user_id' => create('App\User')->id,
            'body' => 'Some reply here'
        ]);

        $user = auth()->user();

        //En de gebruiker zijn notificaties opvraagt
        $response = $this->getJson("/profiles/{$user->name}/notifications")->json();

        //Dan moet er 1 instaan
        $this->assertCount(1, $response);
    }

    /** @test */
    public function a_user_can_mark_a_notification_as_read()
    {
        //Als ik een authenticated gebruiker heb
        $this->signIn();

        //Die zich subscribed op een thread
        $thread = create('App\Thread')->subscribe();

        //En als een gebruiker een reply geeft op de thread...
        $thread->addReply([
            'user_id' => create('App\User')->id,
            'body' => 'Some reply here'
        ]);

        $user = auth()->user();

        //Dan moet er tenminste 1 ongelezen notoficatie zijn
        $this->assertCount(1, $user->unreadNotifications);

        $notificationId = $user->unreadNotifications->first()->id;

        //En als de notificatie dan als gelezen wordt gemarkeerd
        $this->delete("/profiles/{$user->name}/notifications/{$notificationId}");

        //Mag deze niet meer voorkomen in de ongelezen notificaties
        $this->assertCount(0, $user->fresh()->unreadNotifications);
    }
}
