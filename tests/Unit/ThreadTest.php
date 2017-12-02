<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ThreadTest extends TestCase
{
    use RefreshDatabase;

    protected $thread;

    public function setUp()
    {
        parent::setUp();
    
        $this->thread = factory('App\Thread')->create();
    }

    /** @test */
    public function a_thread_can_make_a_string_path()
    {
        $thread = create('App\Thread');

        $this->assertEquals('/threads/' . $thread->channel->slug . '/' . $thread->id, $thread->path());
        $this->assertEquals(
            "/threads/{$thread->channel->slug}/{$thread->id}",
            $thread->path()
        );
    }

    /** @test */
    public function a_thread_has_a_creator()
    {
        $this->assertInstanceOf('App\User', $this->thread->creator);
    }

    /** @test */
    public function a_thread_has_replies()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->thread->replies);
    }

    /** @test */
    public function a_thread_can_add_a_reply()
    {
        $this->thread->addReply([
            'body' => 'Foobar',
            'user_id' => 1
        ]);

        $this->assertCount(1, $this->thread->replies);
    }

    /** @test */
    public function a_thread_belongs_to_a_channel()
    {
        $thread = create('App\Thread');

        $this->assertInstanceOf('App\Channel', $thread->channel);
    }

    /** @test */
    public function a_thread_can_be_subscribed_to()
    {
        //Given we have a thread
        $thread = create('App\Thread');

        //When the user subscribes to the tread
        $thread->subscribe($userId = 1);

        //Then we shoeld be able to fetch al threads that the user has subscribed to
        $this->assertEquals(1, $thread->subscriptions()->where('user_id', $userId)->count());
    }

    /** @test */
    public function a_thread_can_be_unsubscribed_from()
    {
        //Given we have a thread
        $thread = create('App\Thread');

        //And a user who is subscribed to the tread
        $thread->subscribe($userId = 1);

        //When the user than unsubscribes
        $thread->unsubscribe($userId);

        //That should be represented in the database
        $this->assertCount(0, $thread->subscriptions);
    }

    /** @test */
    public function it_knows_if_the_authenticated_user_is_subscribed_to_it()
    {
        //Given we have a thread
        $thread = create('App\Thread');

        //The usser should not yet be subscribed to it
        $this->assertFalse($thread->isSubscribedTo);

        //But when an authenticated user subscribes to the tread
        $this->signIn();

        $thread->subscribe();

        //We can confirm that is subscribed to it
        $this->assertTrue($thread->isSubscribedTo);
    }
}
