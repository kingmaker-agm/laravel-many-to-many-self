<?php

namespace Kingmaker\Illuminate\Eloquent\Relations\Tests;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait ManyToManySelfTestCase
{
    abstract protected function getDatabaseDriver(): string;

    /**
     * @var int
     */
    protected $user1_id, $user2_id, $user3_id, $user4_id;

    /** @test */
    public function related_model_can_be_retrieved_using_relationship()
    {
        $user1 = ModelStub::find($this->user1_id);
        $friends1 = $user1->friends;
        $this->assertCount(2, $friends1);
        $this->assertNotNull($friends1->find($this->user2_id), "The Friends of User 1 doesn't has the User 2");
        $this->assertNotNull($friends1->find($this->user4_id), "The Friends of User 1 doesn't has the User 4");

        $user2 = ModelStub::find($this->user2_id);
        $friends2 = $user2->friends;
        $this->assertCount(3, $friends2);
        $this->assertNotNull($friends2->find($this->user1_id), "The Friends of User 2 doesn't has the User 1");
        $this->assertNotNull($friends2->find($this->user3_id), "The Friends of User 2 doesn't has the User 3");
        $this->assertNotNull($friends2->find($this->user4_id), "The Friends of User 2 doesn't has the User 4");

        $user3 = ModelStub::find($this->user3_id);
        $friends3 = $user3->friends;
        $this->assertCount(2, $friends3);
        $this->assertNotNull($friends3->find($this->user2_id), "The Friends of User 3 doesn't has the User 2");
        $this->assertNotNull($friends3->find($this->user4_id), "The Friends of User 3 doesn't has the User 4");

        $user4 = ModelStub::find($this->user4_id);
        $friends4 = $user4->friends;
        $this->assertCount(3, $friends4);
        $this->assertNotNull($friends4->find($this->user1_id), "The Friends of User 4 doesn't has the User 1");
        $this->assertNotNull($friends4->find($this->user2_id), "The Friends of User 4 doesn't has the User 2");
        $this->assertNotNull($friends4->find($this->user3_id), "The Friends of User 4 doesn't has the User 3");
    }

    /** @test */
    public function relation_can_be_eager_loaded()
    {
        $users = ModelStub::with('friends')->get();

        $user1 = $users->find($this->user1_id);
        $friends1 = $user1->friends;
        $this->assertCount(2, $friends1);
        $this->assertNotNull($friends1->find($this->user2_id), "The Friends of User 1 doesn't has the User 2");
        $this->assertNotNull($friends1->find($this->user4_id), "The Friends of User 1 doesn't has the User 4");

        $user2 = $users->find($this->user2_id);
        $friends2 = $user2->friends;
        $this->assertCount(3, $friends2);
        $this->assertNotNull($friends2->find($this->user1_id), "The Friends of User 2 doesn't has the User 1");
        $this->assertNotNull($friends2->find($this->user3_id), "The Friends of User 2 doesn't has the User 3");
        $this->assertNotNull($friends2->find($this->user4_id), "The Friends of User 2 doesn't has the User 4");

        $user3 = $users->find($this->user3_id);
        $friends3 = $user3->friends;
        $this->assertCount(2, $friends3);
        $this->assertNotNull($friends3->find($this->user2_id), "The Friends of User 3 doesn't has the User 2");
        $this->assertNotNull($friends3->find($this->user4_id), "The Friends of User 3 doesn't has the User 4");

        $user4 = $users->find($this->user4_id);
        $friends4 = $user4->friends;
        $this->assertCount(3, $friends4);
        $this->assertNotNull($friends4->find($this->user1_id), "The Friends of User 4 doesn't has the User 1");
        $this->assertNotNull($friends4->find($this->user2_id), "The Friends of User 4 doesn't has the User 2");
        $this->assertNotNull($friends4->find($this->user3_id), "The Friends of User 4 doesn't has the User 3");
    }

    /** @test */
    public function nested_relations_can_be_eager_loaded()
    {
        $users = ModelStub::with('friends', 'friends.friends')->get();

        // User 1
        $user1 = $users->find($this->user1_id);
        $friends1 = $user1->friends;
        $this->assertCount(2, $friends1);
        $this->assertNotNull($friends1->find($this->user2_id), "The Friends of User 1 doesn't has the User 2");
        $this->assertNotNull($friends1->find($this->user4_id), "The Friends of User 1 doesn't has the User 4");
        // User 2 / Friend of User 1
        $friends2_of_user1 = $friends1->find($this->user2_id)->friends;
        $this->assertCount(3, $friends2_of_user1);
        $this->assertNotNull($friends2_of_user1->find($this->user1_id), "The Friends of User 2 doesn't has the User 1");
        $this->assertNotNull($friends2_of_user1->find($this->user3_id), "The Friends of User 2 doesn't has the User 3");
        $this->assertNotNull($friends2_of_user1->find($this->user4_id), "The Friends of User 2 doesn't has the User 4");
        // User 4 / Friend of User 1
        $friends4_of_user1 = $friends1->find($this->user4_id)->friends;
        $this->assertCount(3, $friends4_of_user1);
        $this->assertNotNull($friends4_of_user1->find($this->user1_id), "The Friends of User 4 doesn't has the User 1");
        $this->assertNotNull($friends4_of_user1->find($this->user2_id), "The Friends of User 4 doesn't has the User 2");
        $this->assertNotNull($friends4_of_user1->find($this->user3_id), "The Friends of User 4 doesn't has the User 3");

        // User 2
        $user2 = $users->find($this->user2_id);
        $friends2 = $user2->friends;
        $this->assertCount(3, $friends2);
        $this->assertNotNull($friends2->find($this->user1_id), "The Friends of User 2 doesn't has the User 1");
        $this->assertNotNull($friends2->find($this->user3_id), "The Friends of User 2 doesn't has the User 3");
        $this->assertNotNull($friends2->find($this->user4_id), "The Friends of User 2 doesn't has the User 4");
        // User 1 / Friend of User 2
        $friends1_of_user2 = $friends2->find($this->user1_id)->friends;
        $this->assertCount(2, $friends1_of_user2);
        $this->assertNotNull($friends1_of_user2->find($this->user2_id), "The Friends of User 1 doesn't has the User 2");
        $this->assertNotNull($friends1_of_user2->find($this->user4_id), "The Friends of User 1 doesn't has the User 4");
        // User 3 / Friend of User 2
        $friends3_of_user2 = $friends2->find($this->user3_id)->friends;
        $this->assertCount(2, $friends3_of_user2);
        $this->assertNotNull($friends3_of_user2->find($this->user2_id), "The Friends of User 3 doesn't has the User 2");
        $this->assertNotNull($friends3_of_user2->find($this->user4_id), "The Friends of User 3 doesn't has the User 4");
        // User 4 / Friend of User 2
        $friends4_of_user2 = $friends2->find($this->user4_id)->friends;
        $this->assertCount(3, $friends4_of_user2);
        $this->assertNotNull($friends4_of_user2->find($this->user1_id), "The Friends of User 4 doesn't has the User 1");
        $this->assertNotNull($friends4_of_user2->find($this->user2_id), "The Friends of User 4 doesn't has the User 2");
        $this->assertNotNull($friends4_of_user2->find($this->user3_id), "The Friends of User 4 doesn't has the User 3");

        // User 3
        $user3 = $users->find($this->user3_id);
        $friends3 = $user3->friends;
        $this->assertCount(2, $friends3);
        $this->assertNotNull($friends3->find($this->user2_id), "The Friends of User 3 doesn't has the User 2");
        $this->assertNotNull($friends3->find($this->user4_id), "The Friends of User 3 doesn't has the User 4");
        // User 2 / Friend of User 3
        $friends2_of_user3 = $friends3->find($this->user2_id)->friends;
        $this->assertCount(3, $friends2_of_user3);
        $this->assertNotNull($friends2_of_user3->find($this->user1_id), "The Friends of User 2 doesn't has the User 1");
        $this->assertNotNull($friends2_of_user3->find($this->user3_id), "The Friends of User 2 doesn't has the User 3");
        $this->assertNotNull($friends2_of_user3->find($this->user4_id), "The Friends of User 2 doesn't has the User 4");
        // User 4 / Friend of User 3
        $friends4_of_user3 = $friends3->find($this->user4_id)->friends;
        $this->assertCount(3, $friends4_of_user3);
        $this->assertNotNull($friends4_of_user3->find($this->user1_id), "The Friends of User 4 doesn't has the User 1");
        $this->assertNotNull($friends4_of_user3->find($this->user2_id), "The Friends of User 4 doesn't has the User 2");
        $this->assertNotNull($friends4_of_user3->find($this->user3_id), "The Friends of User 4 doesn't has the User 3");

        // User 4
        $user4 = $users->find($this->user4_id);
        $friends4 = $user4->friends;
        $this->assertCount(3, $friends4);
        $this->assertNotNull($friends4->find($this->user1_id), "The Friends of User 4 doesn't has the User 1");
        $this->assertNotNull($friends4->find($this->user2_id), "The Friends of User 4 doesn't has the User 2");
        $this->assertNotNull($friends4->find($this->user3_id), "The Friends of User 4 doesn't has the User 3");
        // User 1 / Friend of User 4
        $friends1_of_user4 = $friends4->find($this->user1_id)->friends;
        $this->assertCount(2, $friends1_of_user4);
        $this->assertNotNull($friends1_of_user4->find($this->user2_id), "The Friends of User 1 doesn't has the User 2");
        $this->assertNotNull($friends1_of_user4->find($this->user4_id), "The Friends of User 1 doesn't has the User 4");
        // User 2 / Friend of User 4
        $friends2_of_user4 = $friends4->find($this->user2_id)->friends;
        $this->assertCount(3, $friends2_of_user4);
        $this->assertNotNull($friends2_of_user4->find($this->user1_id), "The Friends of User 2 doesn't has the User 1");
        $this->assertNotNull($friends2_of_user4->find($this->user3_id), "The Friends of User 2 doesn't has the User 3");
        $this->assertNotNull($friends2_of_user4->find($this->user4_id), "The Friends of User 2 doesn't has the User 4");
        // User 3 / Friend of User 4
        $friends3_of_user4 = $friends4->find($this->user3_id)->friends;
        $this->assertCount(2, $friends3_of_user4);
        $this->assertNotNull($friends3_of_user4->find($this->user2_id), "The Friends of User 3 doesn't has the User 2");
        $this->assertNotNull($friends3_of_user4->find($this->user4_id), "The Friends of User 3 doesn't has the User 4");
    }

    /** @test */
    public function relation_can_be_paired_with_where_conditions()
    {
        $user1 = ModelStub::find($this->user1_id);
        $friends1 = $user1->friends()->where('age', '>', 20)->get();

        $this->assertCount(1, $friends1);
        $this->assertNotNull($friends1->find($this->user2_id));

        $user2 = ModelStub::find($this->user2_id);
        $friends2 = $user2->friends()
            ->whereNotNull('email')
            ->whereBetween('age', [16, 28])
            ->orWhereNull('email')
            ->get();

        $this->assertCount(2, $friends2);
        $this->assertNotNull($friends2->find($this->user1_id));
        $this->assertNotNull($friends2->find($this->user4_id));
    }

    /** @test */
    public function relation_can_be_paired_with_the_order_by_clauses()
    {
        $user1 = ModelStub::find($this->user1_id);
        $friends1 = $user1->friends()
            ->orderBy('age')
            ->get();

        $this->assertCount(2, $friends1);
        $this->assertEquals($this->user4_id, $friends1->get(0)->id);
        $this->assertEquals($this->user2_id, $friends1->get(1)->id);

        $user2 = ModelStub::find($this->user2_id);
        $friends2 = $user2->friends()
            ->orderByDesc('age')
            ->get();

        $this->assertCOunt(3, $friends2);
        $this->assertEquals($this->user1_id, $friends2->get(0)->id);
        $this->assertEquals($this->user3_id, $friends2->get(1)->id);
        $this->assertEquals($this->user4_id, $friends2->get(2)->id);
    }

    /** @test */
    public function it_can_be_used_in_where_has_relationship_eloquent_query()
    {
        $users_friend_with_user3 = ModelStub::whereHas('friends', function (Builder $friendQuery) {
            return $friendQuery->where('age', 14); // The Age of User3 is 14
        })->get();

        $this->assertCount(2, $users_friend_with_user3);
        $this->assertNotNull($users_friend_with_user3->find($this->user2_id));
        $this->assertNotNull($users_friend_with_user3->find($this->user4_id));

        $users_friend_with_user1_and_user4 = ModelStub::whereHas('friends', function (Builder $friendQuery) {
            return $friendQuery->where('age', '>', 15)
                ->whereNotNull('email') // making sure these conditions restrict to user1 (age: 18 & email not null)
                ->orWhereNull('birth_at'); // also including user 4 (whose birth_at is null)
        })->get();

        $this->assertCount(4, $users_friend_with_user1_and_user4);
        $this->assertNotNull($users_friend_with_user1_and_user4->find($this->user1_id));
        $this->assertNotNull($users_friend_with_user1_and_user4->find($this->user2_id));
        $this->assertNotNull($users_friend_with_user1_and_user4->find($this->user3_id));
        $this->assertNotNull($users_friend_with_user1_and_user4->find($this->user4_id));
    }

    /** @test */
    public function it_can_be_used_in_has_relationship_count_eloquent_query()
    {
        $user_with_more_than_2friends = ModelStub::has('friends', '>', 2)->get();

        $this->assertNotEmpty($user_with_more_than_2friends);
        $this->assertCount(2, $user_with_more_than_2friends);
        $this->assertNotNull($user_with_more_than_2friends->find($this->user2_id));
        $this->assertNotNull($user_with_more_than_2friends->find($this->user4_id));

        $callback = function ($query) {
            /** @var \Illuminate\Database\Eloquent\Builder $query */
            return $query->whereAge(14)
                ->orWhere('age', 24); // User 2 & 3
        };
        $user_with_sophisticated_friend = ModelStub::has('friends', '>=', 1, 'and', $callback)->get();
        $this->assertCount(4, $user_with_sophisticated_friend);
        $this->assertNotNull($user_with_sophisticated_friend->find($this->user1_id));
        $this->assertNotNull($user_with_sophisticated_friend->find($this->user2_id));
        $this->assertNotNull($user_with_sophisticated_friend->find($this->user3_id));
        $this->assertNotNull($user_with_sophisticated_friend->find($this->user4_id));

        $user_with_more_sophisticated_friend = ModelStub::has('friends', '>', 1, 'and', $callback)->get();
        $this->assertCount(1, $user_with_more_sophisticated_friend);
        $this->assertNotNull($user_with_more_sophisticated_friend->find($this->user4_id));
    }

    protected function createDatabaseForManyToManySelf()
    {
        Schema::dropIfExists('friends');
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('age')->default(0);
            $table->timestamp('birth_at')->nullable();
            $table->string('email')->nullable();
        });

        Schema::create('friends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user1')
                ->references('id')->on('users');
            $table->foreignId('user2')
                ->references('id')->on('users');
        });

        $this->user1_id = ModelStub::create([
            'name' => 'User 1',
            'age' => 18,
            'birth_at' => Carbon::create(1994, 3, 21, 4, 36),
            'email' => 'user1@example.com'
        ])->id;
        $this->user2_id = ModelStub::create([
            'name' => 'User 2',
            'age' => 24,
            'birth_at' => Carbon::create(1988, 8, 7, 18, 14)
        ])->id;
        $this->user3_id = ModelStub::create([
            'name' => 'User 3',
            'age' => 14,
            'birth_at' => Carbon::create(1998, 2, 13, 9, 2),
            'email' => 'user3@w3c.org'
        ])->id;
        $this->user4_id = ModelStub::create([
            'name' => 'User 4',
            'age' => 8
        ])->id;

        DB::table('friends')->insert([
            ['user1' => $this->user1_id, 'user2' => $this->user4_id],
            ['user1' => $this->user2_id, 'user2' => $this->user1_id],
            ['user1' => $this->user2_id, 'user2' => $this->user3_id],
            ['user1' => $this->user3_id, 'user2' => $this->user4_id],
            ['user1' => $this->user4_id, 'user2' => $this->user2_id],
        ]);
    }
}
