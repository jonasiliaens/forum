<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;

class NotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        //In elke test heb ik een authenticated user
        $this->signIn();
    }

    /** @test */
    public function a_notification_is_prepared_when_a_subscribed_thread_receives_a_new_reply_that_is_not_by_the_current_user()
    {
        //En een thread waarop de user zich subscribed
        $thread = create('App\Thread')->subscribe();

        //zijn er geen notificaties
        $this->assertCount(0, auth()->user()->notifications);

        //Als de authenticated user een reply geeft op die thread...
        $thread->addReply([
            'user_id' => auth()->id(),
            'body' => 'Some reply here'
        ]);

        //mag de user geen notificatie krijgen van zijn eigen reply.
        $this->assertCount(0, auth()->user()->fresh()->notifications);

        //Maar als een andere user een reply geeft op de thread...
        $thread->addReply([
            'user_id' => create('App\User')->id,
            'body' => 'Some reply here'
        ]);

        //moet de user wel een notificatie krijgen.
        $this->assertCount(1, auth()->user()->fresh()->notifications);
    }

    /** @test */
    public function a_user_can_fetch_their_unread_notifications()
    {
        //Er is een authenticated user (zie parent::setup)
        //En deze heeft een notificatie
        create(DatabaseNotification::class);

        //Als de user dan zijn notificaties opvraagt, moet er 1 weergegeven worden
        $this->assertCount(1, $this->getJson('/profiles/' . auth()->user()->name . '/notifications')->json());
    }

    /** @test */
    public function a_user_can_mark_a_notification_as_read()
    {
        //Er is een authenticated user (zie parent::setup)
        //En deze heeft een notificatie
        create(DatabaseNotification::class);

        $user = auth()->user();

        //Dan moet er tenminste 1 ongelezen notoficatie zijn
        $this->assertCount(1, $user->unreadNotifications);

        //En als de notificatie dan als gelezen wordt gemarkeerd
        $this->delete("/profiles/{$user->name}/notifications/" . $user->unreadNotifications->first()->id);

        //Mag deze niet meer voorkomen in de ongelezen notificaties
        $this->assertCount(0, $user->fresh()->unreadNotifications);
    }
}
