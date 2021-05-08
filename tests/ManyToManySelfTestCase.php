<?php

namespace Kingmaker\Illuminate\Eloquent\Relations\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait ManyToManySelfTestCase
{
    abstract protected function getDatabaseDriver(): string;

    /**
     * @var ModelStub
     */
    protected $user1, $user2, $user3, $user4;

    /** @test */
    public function retrieves_the_related_model_using_relationship()
    {
        $user1_id = $this->user1->id;
        $user2_id = $this->user2->id;
        $user3_id = $this->user3->id;
        $user4_id = $this->user4->id;

        $user1 = ModelStub::find($user1_id);
        $friends1 = $user1->friends;
        $this->assertCount(2, $friends1);
        $this->assertNotNull($friends1->find($user2_id), "The Friends of User 1 doesn't has the User 2");
        $this->assertNotNull($friends1->find($user4_id), "The Friends of User 1 doesn't has the User 4");

        $user2 = ModelStub::find($user2_id);
        $friends2 = $user2->friends;
        $this->assertCount(3, $friends2);
        $this->assertNotNull($friends2->find($user1_id), "The Friends of User 2 doesn't has the User 1");
        $this->assertNotNull($friends2->find($user3_id), "The Friends of User 2 doesn't has the User 3");
        $this->assertNotNull($friends2->find($user4_id), "The Friends of User 2 doesn't has the User 4");

        $user3 = ModelStub::find($user3_id);
        $friends3 = $user3->friends;
        $this->assertCount(2, $friends3);
        $this->assertNotNull($friends3->find($user2_id), "The Friends of User 3 doesn't has the User 2");
        $this->assertNotNull($friends3->find($user4_id), "The Friends of User 3 doesn't has the User 4");

        $user4 = ModelStub::find($user4_id);
        $friends4 = $user4->friends;
        $this->assertCount(3, $friends4);
        $this->assertNotNull($friends4->find($user1_id), "The Friends of User 4 doesn't has the User 1");
        $this->assertNotNull($friends4->find($user2_id), "The Friends of User 4 doesn't has the User 2");
        $this->assertNotNull($friends4->find($user3_id), "The Friends of User 4 doesn't has the User 3");
    }

    /** @test */
    public function relation_can_be_eager_loaded()
    {
        $user1_id = $this->user1->id;
        $user2_id = $this->user2->id;
        $user3_id = $this->user3->id;
        $user4_id = $this->user4->id;

        $users = ModelStub::with('friends')->get();

        $user1 = $users->find($user1_id);
        $friends1 = $user1->friends;
        $this->assertCount(2, $friends1);
        $this->assertNotNull($friends1->find($user2_id), "The Friends of User 1 doesn't has the User 2");
        $this->assertNotNull($friends1->find($user4_id), "The Friends of User 1 doesn't has the User 4");

        $user2 = $users->find($user2_id);
        $friends2 = $user2->friends;
        $this->assertCount(3, $friends2);
        $this->assertNotNull($friends2->find($user1_id), "The Friends of User 2 doesn't has the User 1");
        $this->assertNotNull($friends2->find($user3_id), "The Friends of User 2 doesn't has the User 3");
        $this->assertNotNull($friends2->find($user4_id), "The Friends of User 2 doesn't has the User 4");

        $user3 = $users->find($user3_id);
        $friends3 = $user3->friends;
        $this->assertCount(2, $friends3);
        $this->assertNotNull($friends3->find($user2_id), "The Friends of User 3 doesn't has the User 2");
        $this->assertNotNull($friends3->find($user4_id), "The Friends of User 3 doesn't has the User 4");

        $user4 = $users->find($user4_id);
        $friends4 = $user4->friends;
        $this->assertCount(3, $friends4);
        $this->assertNotNull($friends4->find($user1_id), "The Friends of User 4 doesn't has the User 1");
        $this->assertNotNull($friends4->find($user2_id), "The Friends of User 4 doesn't has the User 2");
        $this->assertNotNull($friends4->find($user3_id), "The Friends of User 4 doesn't has the User 3");
    }

    protected function createDatabaseForManyToManySelf()
    {
        Schema::dropIfExists('friends');
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        Schema::create('friends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user1')
                ->references('id')->on('users');
            $table->foreignId('user2')
                ->references('id')->on('users');
        });

        $this->user1 = ModelStub::create(['name' => 'User 1']);
        $this->user2 = ModelStub::create(['name' => 'User 2']);
        $this->user3 = ModelStub::create(['name' => 'User 3']);
        $this->user4 = ModelStub::create(['name' => 'User 4']);

        DB::table('friends')->insert([
            ['user1' => $this->user1->id, 'user2' => $this->user4->id],
            ['user1' => $this->user2->id, 'user2' => $this->user1->id],
            ['user1' => $this->user2->id, 'user2' => $this->user3->id],
            ['user1' => $this->user3->id, 'user2' => $this->user4->id],
            ['user1' => $this->user4->id, 'user2' => $this->user2->id],
        ]);
    }
}
