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
        $this->assertNull($friends1->find($this->user1_id));
        $this->assertNull($friends1->find($this->user3_id));

        $user2 = $users->find($this->user2_id);
        $friends2 = $user2->friends;
        $this->assertCount(3, $friends2);
        $this->assertNotNull($friends2->find($this->user1_id), "The Friends of User 2 doesn't has the User 1");
        $this->assertNotNull($friends2->find($this->user3_id), "The Friends of User 2 doesn't has the User 3");
        $this->assertNotNull($friends2->find($this->user4_id), "The Friends of User 2 doesn't has the User 4");
        $this->assertNull($friends2->find($this->user2_id));

        $user3 = $users->find($this->user3_id);
        $friends3 = $user3->friends;
        $this->assertCount(2, $friends3);
        $this->assertNotNull($friends3->find($this->user2_id), "The Friends of User 3 doesn't has the User 2");
        $this->assertNotNull($friends3->find($this->user4_id), "The Friends of User 3 doesn't has the User 4");
        $this->assertNull($friends3->find($this->user1_id));
        $this->assertNull($friends3->find($this->user3_id));

        $user4 = $users->find($this->user4_id);
        $friends4 = $user4->friends;
        $this->assertCount(3, $friends4);
        $this->assertNotNull($friends4->find($this->user1_id), "The Friends of User 4 doesn't has the User 1");
        $this->assertNotNull($friends4->find($this->user2_id), "The Friends of User 4 doesn't has the User 2");
        $this->assertNotNull($friends4->find($this->user3_id), "The Friends of User 4 doesn't has the User 3");
        $this->assertNull($friends4->find($this->user4_id));
    }

    /** @test */
    public function eager_loading_can_be_done_with_only_few_selected_columns()
    {
        $users = ModelStub::with('friends:id,name,birth_at')->get();

        $user1 = $users->find($this->user1_id);
        $friends1 = $user1->friends;
        $this->assertCount(2, $friends1);
        $this->assertNotNull($friends1->find($this->user2_id), "The Friends of User 1 doesn't has the User 2");
        $this->assertEquals("User 2", $friends1->find($this->user2_id)->name);
        $this->assertEquals(Carbon::create(1988,8,7, 18, 14), $friends1->find($this->user2_id)->birth_at);
        $this->assertNull($friends1->find($this->user2_id)->age);
        $this->assertNull($friends1->find($this->user2_id)->email);
        $this->assertNotNull($friends1->find($this->user4_id), "The Friends of User 1 doesn't has the User 4");
        $this->assertEquals("User 4", $friends1->find($this->user4_id)->name);
        $this->assertNull($friends1->find($this->user4_id)->birth_at);
        $this->assertNull($friends1->find($this->user4_id)->age);
        $this->assertNull($friends1->find($this->user4_id)->email);

        $user2 = $users->find($this->user2_id);
        $friends2 = $user2->friends;
        $this->assertCount(3, $friends2);
        $this->assertNotNull($friends2->find($this->user1_id), "The Friends of User 2 doesn't has the User 1");
        $this->assertEquals("User 1", $friends2->find($this->user1_id)->name);
        $this->assertEquals(Carbon::create(1994,3,21, 4, 36), $friends2->find($this->user1_id)->birth_at);
        $this->assertNull($friends2->find($this->user1_id)->age);
        $this->assertNull($friends2->find($this->user1_id)->email);
        $this->assertNotNull($friends2->find($this->user3_id), "The Friends of User 2 doesn't has the User 3");
        $this->assertEquals("User 3", $friends2->find($this->user3_id)->name);
        $this->assertEquals(Carbon::create(1998,2,13, 9, 2), $friends2->find($this->user3_id)->birth_at);
        $this->assertNull($friends2->find($this->user3_id)->age);
        $this->assertNull($friends2->find($this->user3_id)->email);
        $this->assertNotNull($friends2->find($this->user4_id), "The Friends of User 2 doesn't has the User 4");
        $this->assertEquals("User 4", $friends2->find($this->user4_id)->name);
        $this->assertNull($friends2->find($this->user4_id)->birth_at);
        $this->assertNull($friends2->find($this->user4_id)->age);
        $this->assertNull($friends2->find($this->user4_id)->email);

        $user3 = $users->find($this->user3_id);
        $friends3 = $user3->friends;
        $this->assertCount(2, $friends3);
        $this->assertNotNull($friends3->find($this->user2_id), "The Friends of User 3 doesn't has the User 2");
        $this->assertEquals("User 2", $friends3->find($this->user2_id)->name);
        $this->assertEquals(Carbon::create(1988,8,7, 18, 14), $friends3->find($this->user2_id)->birth_at);
        $this->assertNull($friends3->find($this->user2_id)->age);
        $this->assertNull($friends3->find($this->user2_id)->email);
        $this->assertNotNull($friends3->find($this->user4_id), "The Friends of User 3 doesn't has the User 4");
        $this->assertEquals("User 4", $friends3->find($this->user4_id)->name);
        $this->assertNull($friends3->find($this->user4_id)->birth_at);
        $this->assertNull($friends3->find($this->user4_id)->age);
        $this->assertNull($friends3->find($this->user4_id)->email);

        $user4 = $users->find($this->user4_id);
        $friends4 = $user4->friends;
        $this->assertCount(3, $friends4);
        $this->assertNotNull($friends4->find($this->user1_id), "The Friends of User 4 doesn't has the User 1");
        $this->assertEquals("User 1", $friends4->find($this->user1_id)->name);
        $this->assertEquals(Carbon::create(1994,3,21, 4, 36), $friends4->find($this->user1_id)->birth_at);
        $this->assertNull($friends4->find($this->user1_id)->age);
        $this->assertNull($friends4->find($this->user1_id)->email);
        $this->assertNotNull($friends4->find($this->user2_id), "The Friends of User 4 doesn't has the User 2");
        $this->assertEquals("User 2", $friends4->find($this->user2_id)->name);
        $this->assertEquals(Carbon::create(1988,8,7, 18, 14), $friends4->find($this->user2_id)->birth_at);
        $this->assertNull($friends4->find($this->user2_id)->age);
        $this->assertNull($friends4->find($this->user2_id)->email);
        $this->assertNotNull($friends4->find($this->user3_id), "The Friends of User 4 doesn't has the User 3");
        $this->assertEquals("User 3", $friends4->find($this->user3_id)->name);
        $this->assertEquals(Carbon::create(1998,2,13, 9, 2), $friends4->find($this->user3_id)->birth_at);
        $this->assertNull($friends4->find($this->user3_id)->age);
        $this->assertNull($friends4->find($this->user3_id)->email);
    }

    /** @test */
    public function nested_eager_loading_can_be_done_with_only_few_selected_columns()
    {
        $user = ModelStub::with('friends.friends:id,name,birth_at')->find($this->user1_id);

        $user2 = $user->friends->find($this->user2_id);
        $friends2 = $user2->friends;
        $this->assertCount(3, $friends2);
        $this->assertNotNull($friends2->find($this->user1_id), "The Friends of User 2 doesn't has the User 1");
        $this->assertEquals("User 1", $friends2->find($this->user1_id)->name);
        $this->assertEquals(Carbon::create(1994,3,21, 4, 36), $friends2->find($this->user1_id)->birth_at);
        $this->assertNull($friends2->find($this->user1_id)->age);
        $this->assertNull($friends2->find($this->user1_id)->email);
        $this->assertNotNull($friends2->find($this->user3_id), "The Friends of User 2 doesn't has the User 3");
        $this->assertEquals("User 3", $friends2->find($this->user3_id)->name);
        $this->assertEquals(Carbon::create(1998,2,13, 9, 2), $friends2->find($this->user3_id)->birth_at);
        $this->assertNull($friends2->find($this->user3_id)->age);
        $this->assertNull($friends2->find($this->user3_id)->email);
        $this->assertNotNull($friends2->find($this->user4_id), "The Friends of User 2 doesn't has the User 4");
        $this->assertEquals("User 4", $friends2->find($this->user4_id)->name);
        $this->assertNull($friends2->find($this->user4_id)->birth_at);
        $this->assertNull($friends2->find($this->user4_id)->age);
        $this->assertNull($friends2->find($this->user4_id)->email);

        $user4 = $user->friends->find($this->user4_id);
        $friends4 = $user4->friends;
        $this->assertCount(3, $friends4);
        $this->assertNotNull($friends4->find($this->user1_id), "The Friends of User 4 doesn't has the User 1");
        $this->assertEquals("User 1", $friends4->find($this->user1_id)->name);
        $this->assertEquals(Carbon::create(1994,3,21, 4, 36), $friends4->find($this->user1_id)->birth_at);
        $this->assertNull($friends4->find($this->user1_id)->age);
        $this->assertNull($friends4->find($this->user1_id)->email);
        $this->assertNotNull($friends4->find($this->user2_id), "The Friends of User 4 doesn't has the User 2");
        $this->assertEquals("User 2", $friends4->find($this->user2_id)->name);
        $this->assertEquals(Carbon::create(1988,8,7, 18, 14), $friends4->find($this->user2_id)->birth_at);
        $this->assertNull($friends4->find($this->user2_id)->age);
        $this->assertNull($friends4->find($this->user2_id)->email);
        $this->assertNotNull($friends4->find($this->user3_id), "The Friends of User 4 doesn't has the User 3");
        $this->assertEquals("User 3", $friends4->find($this->user3_id)->name);
        $this->assertEquals(Carbon::create(1998,2,13, 9, 2), $friends4->find($this->user3_id)->birth_at);
        $this->assertNull($friends4->find($this->user3_id)->age);
        $this->assertNull($friends4->find($this->user3_id)->email);
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
    public function it_can_load_other_aggregates_for_the_relation()
    {
        [$major, $minor, $patch] = laravel_version();
        if ($major < 8 || ($major == 8 && $minor <= 12))
            $this->markTestSkipped("The Aggregate functions are not available on the Laravel version {$major}.{$minor}.{$patch}");

        $users = ModelStub::query()
            ->withMax('friends', 'age')
            ->withMin('friends', 'age')
            ->withSum('friends', 'age')
            ->withAvg('friends', 'age')
            ->get();

        $this->assertEquals(8, $users->find($this->user1_id)->friends_min_age);
        $this->assertEquals(24, $users->find($this->user1_id)->friends_max_age);
        $this->assertEquals(32, $users->find($this->user1_id)->friends_sum_age);
        $this->assertEquals(16, $users->find($this->user1_id)->friends_avg_age);

        $this->assertEquals(8, $users->find($this->user2_id)->friends_min_age);
        $this->assertEquals(18, $users->find($this->user2_id)->friends_max_age);
        $this->assertEquals(40, $users->find($this->user2_id)->friends_sum_age);
        if ($this->getDatabaseDriver() == 'sqlsrv') // SQL Server returning average as an whole integer than float
            $this->assertEquals(13, $users->find($this->user2_id)->friends_avg_age, 3);
        else
            $this->assertEquals(13.333, round($users->find($this->user2_id)->friends_avg_age, 3));

        $this->assertEquals(8, $users->find($this->user3_id)->friends_min_age);
        $this->assertEquals(24, $users->find($this->user3_id)->friends_max_age);
        $this->assertEquals(32, $users->find($this->user3_id)->friends_sum_age);
        $this->assertEquals(16, $users->find($this->user3_id)->friends_avg_age);

        $this->assertEquals(14, $users->find($this->user4_id)->friends_min_age);
        $this->assertEquals(24, $users->find($this->user4_id)->friends_max_age);
        $this->assertEquals(56, $users->find($this->user4_id)->friends_sum_age);
        if ($this->getDatabaseDriver() == 'sqlsrv') // SQL Server returning average as an whole integer than float
            $this->assertEquals(18, $users->find($this->user4_id)->friends_avg_age, 3);
        else
            $this->assertEquals(18.667, round($users->find($this->user4_id)->friends_avg_age, 3));
    }

    /** @test */
    public function it_can_load_the_count_for_the_relation()
    {
        $users = ModelStub::withCount('friends')->get();

        $this->assertEquals(2, $users->find($this->user1_id)->friends_count);
        $this->assertEquals(3, $users->find($this->user2_id)->friends_count);
        $this->assertEquals(2, $users->find($this->user3_id)->friends_count);
        $this->assertEquals(3, $users->find($this->user4_id)->friends_count);
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

        $this->assertCount(3, $friends2);
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

    /** @test */
    public function it_can_have_nested_repeated_where_has_eloquent_query()
    {
        $user5 = ModelStub::create([
            'name' => 'User 5',
            'age' => 40
        ]);
        ModelStub::find($this->user1_id)->friends()->attach($user5);

        $users = ModelStub::whereHas('friends', function (Builder $query) {
            return $query->whereHas('friends', function (Builder $innerQuery) {
                return $innerQuery->where('age', 40); // newly Created User 5
            });
        })->get();

        $this->assertCount(3, $users);
        $this->assertNotNull($users->find($user5->id));
        $this->assertNotNull($users->find($this->user2_id));
        $this->assertNotNull($users->find($this->user4_id));
        $this->assertNull($users->find($this->user1_id));
        $this->assertNull($users->find($this->user3_id));
    }

    protected function createDatabaseForManyToManySelf()
    {
        Schema::dropIfExists('friends');
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table) {
            $table->unsignedBigInteger('id', true);
            $table->string('name');
            $table->integer('age')->default(0);
            $table->timestamp('birth_at')->nullable();
            $table->string('email')->nullable();
        });

        Schema::create('friends', function (Blueprint $table) {
            $table->unsignedBigInteger('id', true);
            $table->unsignedBigInteger('user1');
            $table->foreign('user1')
                ->references('id')->on('users');
            $table->unsignedBigInteger('user2');
            $table->foreign('user2')
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
