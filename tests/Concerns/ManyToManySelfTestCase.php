<?php

namespace Kingmaker\Illuminate\Eloquent\Relations\Tests\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Kingmaker\Illuminate\Eloquent\Relations\Tests\Contracts\DatabaseSchemaRefreshable;
use Kingmaker\Illuminate\Eloquent\Relations\Tests\Models\ModelStub;
use PHPUnit\Framework\Attributes\Test;

trait ManyToManySelfTestCase
{
    abstract protected function getDatabaseDriver(): string;

    /**
     * @var int|string
     */
    protected $user1_id, $user2_id, $user3_id, $user4_id;

    /**
     * @var class-string<Model> The model class to use for testing
     */
    protected $modelClass = ModelStub::class;

    /** @var \Illuminate\Database\Eloquent\Model|null */
    protected $testUser1, $testUser2, $testUser3;

    /**
     * Set the model class to use for testing
     *
     * @param class-string<Model> $modelClass
     * @return void
     */
    protected function setModelClass(string $modelClass): void
    {
        $this->modelClass = $modelClass;
    }

    /**
     * Get the model class to use for testing
     *
     * @return class-string<Model>
     */
    protected function getModelClass(): string
    {
        return $this->modelClass;
    }

    /** @test */
    #[Test]
    public function related_model_can_be_retrieved_using_relation()
    {
        $user1 = $this->getModelClass()::find($this->user1_id);
        $friends1 = $user1->friends;
        $this->assertCount(2, $friends1);
        $this->assertNotNull($friends1->find($this->user2_id), "The Friends of User 1 doesn't has the User 2");
        $this->assertNotNull($friends1->find($this->user4_id), "The Friends of User 1 doesn't has the User 4");
        $this->assertNull($friends1->find($this->user1_id), "The Friends of User 1 has the User 1");
        $this->assertNull($friends1->find($this->user3_id), "The Friends of User 1 has the User 3");

        $user2 = $this->getModelClass()::find($this->user2_id);
        $friends2 = $user2->friends;
        $this->assertCount(3, $friends2);
        $this->assertNotNull($friends2->find($this->user1_id), "The Friends of User 2 doesn't has the User 1");
        $this->assertNotNull($friends2->find($this->user3_id), "The Friends of User 2 doesn't has the User 3");
        $this->assertNotNull($friends2->find($this->user4_id), "The Friends of User 2 doesn't has the User 4");
        $this->assertNull($friends2->find($this->user2_id), "The Friends of User 2 has the User 2");

        $user3 = $this->getModelClass()::find($this->user3_id);
        $friends3 = $user3->friends;
        $this->assertCount(2, $friends3);
        $this->assertNotNull($friends3->find($this->user2_id), "The Friends of User 3 doesn't has the User 2");
        $this->assertNotNull($friends3->find($this->user4_id), "The Friends of User 3 doesn't has the User 4");
        $this->assertNull($friends3->find($this->user1_id), "The Friends of User 3 has the User 1");
        $this->assertNull($friends3->find($this->user3_id), "The Friends of User 3 has the User 3");

        $user4 = $this->getModelClass()::find($this->user4_id);
        $friends4 = $user4->friends;
        $this->assertCount(3, $friends4);
        $this->assertNotNull($friends4->find($this->user1_id), "The Friends of User 4 doesn't has the User 1");
        $this->assertNotNull($friends4->find($this->user2_id), "The Friends of User 4 doesn't has the User 2");
        $this->assertNotNull($friends4->find($this->user3_id), "The Friends of User 4 doesn't has the User 3");
        $this->assertNull($friends4->find($this->user4_id), "The Friends of User 4 has the User 4");
    }

    /** @test */
    #[Test]
    public function related_model_can_be_retrieved_using_relationship_query_get()
    {
        $user1 = $this->getModelClass()::find($this->user1_id);
        $friends1 = $user1->friends()->get();
        $this->assertCount(2, $friends1);
        $this->assertNotNull($friends1->find($this->user2_id), "The Friends of User 1 doesn't has the User 2");
        $this->assertNotNull($friends1->find($this->user4_id), "The Friends of User 1 doesn't has the User 4");
        $this->assertNull($friends1->find($this->user1_id), "The Friends of User 1 has the User 1");
        $this->assertNull($friends1->find($this->user3_id), "The Friends of User 1 has the User 3");

        $user2 = $this->getModelClass()::find($this->user2_id);
        $friends2 = $user2->friends()->get();
        $this->assertCount(3, $friends2);
        $this->assertNotNull($friends2->find($this->user1_id), "The Friends of User 2 doesn't has the User 1");
        $this->assertNotNull($friends2->find($this->user3_id), "The Friends of User 2 doesn't has the User 3");
        $this->assertNotNull($friends2->find($this->user4_id), "The Friends of User 2 doesn't has the User 4");
        $this->assertNull($friends2->find($this->user2_id), "The Friends of User 2 has the User 2");

        $user3 = $this->getModelClass()::find($this->user3_id);
        $friends3 = $user3->friends()->get();
        $this->assertCount(2, $friends3);
        $this->assertNotNull($friends3->find($this->user2_id), "The Friends of User 3 doesn't has the User 2");
        $this->assertNotNull($friends3->find($this->user4_id), "The Friends of User 3 doesn't has the User 4");
        $this->assertNull($friends3->find($this->user1_id), "The Friends of User 3 has the User 1");
        $this->assertNull($friends3->find($this->user3_id), "The Friends of User 3 has the User 3");

        $user4 = $this->getModelClass()::find($this->user4_id);
        $friends4 = $user4->friends()->get();
        $this->assertCount(3, $friends4);
        $this->assertNotNull($friends4->find($this->user1_id), "The Friends of User 4 doesn't has the User 1");
        $this->assertNotNull($friends4->find($this->user2_id), "The Friends of User 4 doesn't has the User 2");
        $this->assertNotNull($friends4->find($this->user3_id), "The Friends of User 4 doesn't has the User 3");
        $this->assertNull($friends4->find($this->user4_id), "The Friends of User 4 has the User 4");
    }

    /** @test */
    #[Test]
    public function relation_can_be_eager_loaded()
    {
        $users = $this->getModelClass()::with('friends')->get();

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
    #[Test]
    public function eager_loading_can_be_done_with_only_few_selected_columns()
    {
        $users = $this->getModelClass()::with('friends:id,name,birth_at')->get();


        $user1 = $users->find($this->user1_id);
        $friends1 = $user1->friends;
        $this->assertCount(2, $friends1);
        $this->assertNull($friends1->find($this->user3_id), "The Friends of User 1 has the User 3");
        $this->assertNull($friends1->find($this->user1_id), "The Friends of User 1 has the User 1");


        $friends1_user2 = $friends1->find($this->user2_id);
        $this->assertNotNull($friends1_user2, "The Friends of User 1 doesn't has the User 2");
        $this->assertEquals($this->user2_id, $friends1_user2->id);
        $this->assertEquals("User 2", $friends1_user2->name);
        $this->assertEquals(Carbon::create(1988,8,7, 18, 14), $friends1_user2->birth_at);
        $this->assertNull($friends1_user2->age);
        $this->assertNull($friends1_user2->email);

        $friends1_user4 = $friends1->find($this->user4_id);
        $this->assertNotNull($friends1_user4, "The Friends of User 1 doesn't has the User 4");
        $this->assertEquals($this->user4_id, $friends1_user4->id);
        $this->assertEquals("User 4", $friends1_user4->name);
        $this->assertNull($friends1_user4->birth_at);
        $this->assertNull($friends1_user4->age);
        $this->assertNull($friends1_user4->email);


        $user2 = $users->find($this->user2_id);
        $friends2 = $user2->friends;
        $this->assertCount(3, $friends2);
        $this->assertNull($friends2->find($this->user2_id), "The Friends of User 2 has the User 2");

        $friends2_user1 = $friends2->find($this->user1_id);
        $this->assertNotNull($friends2_user1, "The Friends of User 2 doesn't has the User 1");
        $this->assertEquals($this->user1_id, $friends2_user1->id);
        $this->assertEquals("User 1", $friends2_user1->name);
        $this->assertEquals(Carbon::create(1994,3,21, 4, 36), $friends2_user1->birth_at);
        $this->assertNull($friends2_user1->age);
        $this->assertNull($friends2_user1->email);

        $friends2_user3 = $friends2->find($this->user3_id);
        $this->assertNotNull($friends2_user3, "The Friends of User 2 doesn't has the User 3");
        $this->assertEquals($this->user3_id, $friends2_user3->id);
        $this->assertEquals("User 3", $friends2_user3->name);
        $this->assertEquals(Carbon::create(1998,2,13, 9, 2), $friends2_user3->birth_at);
        $this->assertNull($friends2_user3->age);
        $this->assertNull($friends2_user3->email);

        $friends2_user4 = $friends2->find($this->user4_id);
        $this->assertNotNull($friends2_user4, "The Friends of User 2 doesn't has the User 4");
        $this->assertEquals($this->user4_id, $friends2_user4->id);
        $this->assertEquals("User 4", $friends2_user4->name);
        $this->assertNull($friends2_user4->birth_at);
        $this->assertNull($friends2_user4->age);
        $this->assertNull($friends2_user4->email);


        $user3 = $users->find($this->user3_id);
        $friends3 = $user3->friends;
        $this->assertCount(2, $friends3);
        $this->assertNull($friends3->find($this->user1_id), "The Friends of User 3 has the User 1");
        $this->assertNull($friends3->find($this->user3_id), "The Friends of User 3 has the User 3");

        $friends3_user2 = $friends3->find($this->user2_id);
        $this->assertNotNull($friends3_user2, "The Friends of User 3 doesn't has the User 2");
        $this->assertEquals($this->user2_id, $friends3_user2->id);
        $this->assertEquals("User 2", $friends3_user2->name);
        $this->assertEquals(Carbon::create(1988,8,7, 18, 14), $friends3_user2->birth_at);
        $this->assertNull($friends3_user2->age);
        $this->assertNull($friends3_user2->email);

        $friends3_user4 = $friends3->find($this->user4_id);
        $this->assertNotNull($friends3_user4, "The Friends of User 3 doesn't has the User 4");
        $this->assertEquals($this->user4_id, $friends3_user4->id);
        $this->assertEquals("User 4", $friends3_user4->name);
        $this->assertNull($friends3_user4->birth_at);
        $this->assertNull($friends3_user4->age);
        $this->assertNull($friends3_user4->email);


        $user4 = $users->find($this->user4_id);
        $friends4 = $user4->friends;
        $this->assertCount(3, $friends4);
        $this->assertNull($friends4->find($this->user4_id), "The Friends of User 4 has the User 4");

        $friends4_user1 = $friends4->find($this->user1_id);
        $this->assertNotNull($friends4_user1, "The Friends of User 4 doesn't has the User 1");
        $this->assertEquals($this->user1_id, $friends4_user1->id);
        $this->assertEquals("User 1", $friends4_user1->name);
        $this->assertEquals(Carbon::create(1994,3,21, 4, 36), $friends4_user1->birth_at);
        $this->assertNull($friends4_user1->age);
        $this->assertNull($friends4_user1->email);

        $friends4_user2 = $friends4->find($this->user2_id);
        $this->assertNotNull($friends4_user2, "The Friends of User 4 doesn't has the User 2");
        $this->assertEquals($this->user2_id, $friends4_user2->id);
        $this->assertEquals("User 2", $friends4_user2->name);
        $this->assertEquals(Carbon::create(1988,8,7, 18, 14), $friends4_user2->birth_at);
        $this->assertNull($friends4_user2->age);
        $this->assertNull($friends4_user2->email);

        $friends4_user3 = $friends4->find($this->user3_id);
        $this->assertNotNull($friends4_user3, "The Friends of User 4 doesn't has the User 3");
        $this->assertEquals($this->user3_id, $friends4_user3->id);
        $this->assertEquals("User 3", $friends4_user3->name);
        $this->assertEquals(Carbon::create(1998,2,13, 9, 2), $friends4_user3->birth_at);
        $this->assertNull($friends4_user3->age);
        $this->assertNull($friends4_user3->email);
    }

    /** @test */
    #[Test]
    public function nested_eager_loading_can_be_done_with_only_few_selected_columns()
    {
        $user = $this->getModelClass()::with('friends.friends:id,name,birth_at')->find($this->user1_id);

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
    #[Test]
    public function it_can_attach_properly() {
        $this->initiateFurtherTestUsers();
        $this->testUser1->friends()->attach($this->testUser2);
        $this->assertCount(1, $this->testUser1->friends()->get());
        $this->assertTrue($this->testUser1->friends()->get()->first()->is($this->testUser2));

        $this->initiateFurtherTestUsers();
        $this->testUser2->friends()->attach($this->testUser1);
        $this->testUser1->friends()->attach($this->testUser3);
        $this->assertCount(2, $this->testUser1->friends()->get());
        $this->assertTrue($this->testUser1->friends()->get()->contains($this->testUser2));
        $this->assertTrue($this->testUser1->friends()->get()->contains($this->testUser3));
    }

    /** @test */
    #[Test]
    public function it_can_attach_duplicate_pivots_when_attached_again()
    {
        // Same Attachment
        $this->initiateFurtherTestUsers();
        $this->testUser1->friends()->attach($this->testUser2);
        $this->testUser1->friends()->attach($this->testUser2);

        $this->assertCount(2, $this->testUser1->friends()->get());
        $this->assertTrue($this->testUser1->friends()->get()->contains($this->testUser2));
        $this->assertFalse($this->testUser1->friends()->get()->contains($this->testUser3));
        $this->assertCount(2, $this->testUser1->friends()->get()->where('id', $this->testUser2->id));

        // Different Attachment
        $this->initiateFurtherTestUsers();
        $this->testUser2->friends()->attach($this->testUser1);
        $this->testUser1->friends()->attach($this->testUser2);

        $this->assertCount(2, $this->testUser1->friends()->get());
        $this->assertTrue($this->testUser1->friends()->get()->contains($this->testUser2));
        $this->assertFalse($this->testUser1->friends()->get()->contains($this->testUser3));
        $this->assertCount(2, $this->testUser1->friends()->get()->where('id', $this->testUser2->id));
    }

    /** @test */
    #[Test]
    public function it_can_attach_duplicate_pivots_when_attached_with_pivots_again()
    {
        // Same Attachment
        $this->initiateFurtherTestUsers();
        $this->testUser1->friends()->attach($this->testUser2, ['percentage' => 10]);
        $this->testUser1->friends()->attach($this->testUser2, ['percentage' => 20]);

        $this->assertCount(2, $this->testUser1->friends()->get());
        $this->assertTrue($this->testUser1->friends()->get()->contains($this->testUser2));
        $this->assertFalse($this->testUser1->friends()->get()->contains($this->testUser3));
        $this->assertCount(2, $this->testUser1->friends()->get()->where('id', $this->testUser2->id));
        $this->testUser1->friends()->get()->each(function ($friend, int $index) {
            $this->assertEquals($index == 0 ? 10 : 20, $friend->pivot->percentage);
        });

        // Different Attachment
        $this->initiateFurtherTestUsers();
        $this->testUser2->friends()->attach($this->testUser1, ['percentage' => 30]);
        $this->testUser1->friends()->attach($this->testUser2, ['percentage' => 40]);

        $this->assertCount(2, $this->testUser1->friends()->get());
        $this->assertTrue($this->testUser1->friends()->get()->contains($this->testUser2));
        $this->assertFalse($this->testUser1->friends()->get()->contains($this->testUser3));
        $this->assertCount(2, $this->testUser1->friends()->get()->where('id', $this->testUser2->id));
        $this->testUser1->friends()->get()->each(function ($friend, int $index) {
            $this->assertEquals($index == 0 ? 30 : 40, $friend->pivot->percentage);
        });
    }

    /** @test  */
    #[Test]
    public function it_can_detach_properly()
    {
        $this->initiateFurtherTestUsers();
        $this->testUser1->friends()->attach($this->testUser2);
        $this->testUser1->friends()->attach($this->testUser3);
        $detached = $this->testUser1->friends()->detach($this->testUser2);

        $this->assertCount(1, $this->testUser1->friends()->get());
        $this->assertTrue($this->testUser1->friends()->get()->contains($this->testUser3));
        $this->assertFalse($this->testUser1->friends()->get()->contains($this->testUser2));
        $this->assertEquals(1, $detached);
    }

    /** @test  */
    #[Test]
    public function it_can_detach_properly_for_double_attachment()
    {
        // Direct Attchment
        $this->initiateFurtherTestUsers();
        $this->testUser1->friends()->attach($this->testUser2);
        $this->testUser1->friends()->attach($this->testUser2);
        $detached = $this->testUser1->friends()->detach($this->testUser2);

        $this->assertCount(0, $this->testUser1->friends()->get());
        $this->assertFalse($this->testUser1->friends()->get()->contains($this->testUser2));
        $this->assertEquals(2, $detached);


        // Mixed Attachment
        $this->initiateFurtherTestUsers();
        $this->testUser1->friends()->attach($this->testUser2);
        $this->testUser2->friends()->attach($this->testUser1);
        $detached = $this->testUser1->friends()->detach($this->testUser2);

        $this->assertCount(0, $this->testUser1->friends()->get());
        $this->assertFalse($this->testUser1->friends()->get()->contains($this->testUser2));
        $this->assertEquals(2, $detached);
    }

    /** @test  */
    #[Test]
    public function it_can_sync_two_way_with_element_ids()
    {
        // Direct Testing
        $this->initiateFurtherTestUsers();
        $this->testUser1->friends()->sync([$this->testUser2->id, $this->testUser3->id]);
        $syncResults = $this->testUser1->friends()->sync([$this->testUser3->id]);

        $testUser1Friends = $this->testUser1->friends()->get();
        $this->assertCount(1, $testUser1Friends);
        $this->assertTrue($testUser1Friends->first()->is($this->testUser3));
        $this->assertArrayHasKey('attached', $syncResults);
        $this->assertArrayHasKey('detached', $syncResults);
        $this->assertArrayHasKey('updated', $syncResults);
        $this->assertCount(0, $syncResults['attached']);
        $this->assertCount(0, $syncResults['updated']);
        $this->assertCount(1, $syncResults['detached']);


        // Inverse Testing
        $this->initiateFurtherTestUsers();
        $this->testUser3->friends()->sync([$this->testUser2->id, $this->testUser1->id]);
        $this->testUser2->friends()->attach($this->testUser1->id);
        $syncResults = $this->testUser1->friends()->sync([$this->testUser3->id]);

        $testUser1Friends = $this->testUser1->friends()->get();
        $this->assertCount(1, $testUser1Friends);
        $this->assertTrue($testUser1Friends->first()->is($this->testUser3));
        $this->assertArrayHasKey('attached', $syncResults);
        $this->assertArrayHasKey('detached', $syncResults);
        $this->assertArrayHasKey('updated', $syncResults);
        $this->assertCount(0, $syncResults['attached']);
        $this->assertCount(0, $syncResults['updated']);
        $this->assertCount(1, $syncResults['detached']);
    }

    /** @test  */
    #[Test]
    public function it_can_sync_two_way_with_elements()
    {
        // Direct Testing
        $this->initiateFurtherTestUsers();
        $this->testUser1->friends()->sync(
            EloquentCollection::make([$this->testUser2, $this->testUser3])
        );
        $syncResults = $this->testUser1->friends()->sync(
            EloquentCollection::make([$this->testUser3])
        );

        $testUser1Friends = $this->testUser1->friends()->get();
        $this->assertCount(1, $testUser1Friends);
        $this->assertTrue($testUser1Friends->first()->is($this->testUser3));
        $this->assertArrayHasKey('attached', $syncResults);
        $this->assertArrayHasKey('detached', $syncResults);
        $this->assertArrayHasKey('updated', $syncResults);
        $this->assertCount(0, $syncResults['attached']);
        $this->assertCount(0, $syncResults['updated']);
        $this->assertCount(1, $syncResults['detached']);


        // Inverse Testing
        $this->initiateFurtherTestUsers();
        $this->testUser3->friends()->sync(
            EloquentCollection::make([$this->testUser2, $this->testUser1])
        );
        $this->testUser2->friends()->attach($this->testUser1);
        $syncResults = $this->testUser1->friends()->sync(
            EloquentCollection::make([$this->testUser3])
        );

        $testUser1Friends = $this->testUser1->friends()->get();
        $this->assertCount(1, $testUser1Friends);
        $this->assertTrue($testUser1Friends->first()->is($this->testUser3));
        $this->assertArrayHasKey('attached', $syncResults);
        $this->assertArrayHasKey('detached', $syncResults);
        $this->assertArrayHasKey('updated', $syncResults);
        $this->assertCount(0, $syncResults['attached']);
        $this->assertCount(0, $syncResults['updated']);
        $this->assertCount(1, $syncResults['detached']);
    }

    /** @test  */
    #[Test]
    public function it_can_sync_two_way_without_detaching_using_element_ids()
    {
        // Direct Testing
        $this->initiateFurtherTestUsers();
        $this->testUser1->friends()->sync([$this->testUser2->id, $this->testUser3->id]);
        $syncResults = $this->testUser1->friends()->syncWithoutDetaching([$this->testUser3->id]);

        $testUser1Friends = $this->testUser1->friends()->get();
        $this->assertCount(2, $testUser1Friends);
        $this->assertTrue($testUser1Friends->find($this->testUser3)->is($this->testUser3));
        $this->assertTrue($testUser1Friends->find($this->testUser2)->is($this->testUser2));
        $this->assertArrayHasKey('attached', $syncResults);
        $this->assertArrayHasKey('detached', $syncResults);
        $this->assertArrayHasKey('updated', $syncResults);
        $this->assertCount(0, $syncResults['attached']);
        $this->assertCount(0, $syncResults['updated']);
        $this->assertCount(0, $syncResults['detached']);


        // Inverse Testing
        $this->initiateFurtherTestUsers();
        $this->testUser3->friends()->sync([$this->testUser2->id]);
        $this->testUser2->friends()->attach($this->testUser1->id);
        $syncResults = $this->testUser1->friends()->syncWithoutDetaching([$this->testUser3->id]);

        $testUser1Friends = $this->testUser1->friends()->get();
        $this->assertCount(2, $testUser1Friends);
        $this->assertTrue($testUser1Friends->find($this->testUser2)->is($this->testUser2));
        $this->assertTrue($testUser1Friends->find($this->testUser3)->is($this->testUser3));
        $this->assertArrayHasKey('attached', $syncResults);
        $this->assertArrayHasKey('detached', $syncResults);
        $this->assertArrayHasKey('updated', $syncResults);
        $this->assertCount(1, $syncResults['attached']);
        $this->assertCount(0, $syncResults['updated']);
        $this->assertCount(0, $syncResults['detached']);
    }

    /** @test  */
    #[Test]
    public function it_can_sync_two_way_without_detaching_using_elements()
    {
        // Direct Testing
        $this->initiateFurtherTestUsers();
        $this->testUser1->friends()->sync(EloquentCollection::make([$this->testUser2, $this->testUser3]));
        $syncResults = $this->testUser1->friends()->syncWithoutDetaching(EloquentCollection::make([$this->testUser3]));

        $testUser1Friends = $this->testUser1->friends()->get();
        $this->assertCount(2, $testUser1Friends);
        $this->assertTrue($testUser1Friends->find($this->testUser3)->is($this->testUser3));
        $this->assertTrue($testUser1Friends->find($this->testUser2)->is($this->testUser2));
        $this->assertArrayHasKey('attached', $syncResults);
        $this->assertArrayHasKey('detached', $syncResults);
        $this->assertArrayHasKey('updated', $syncResults);
        $this->assertCount(0, $syncResults['attached']);
        $this->assertCount(0, $syncResults['updated']);
        $this->assertCount(0, $syncResults['detached']);


        // Inverse Testing
        $this->initiateFurtherTestUsers();
        $this->testUser3->friends()->sync(EloquentCollection::make([$this->testUser2]));
        $this->testUser2->friends()->attach($this->testUser1);
        $syncResults = $this->testUser1->friends()->syncWithoutDetaching(EloquentCollection::make([$this->testUser3]));

        $testUser1Friends = $this->testUser1->friends()->get();
        $this->assertCount(2, $testUser1Friends);
        $this->assertTrue($testUser1Friends->find($this->testUser2)->is($this->testUser2));
        $this->assertTrue($testUser1Friends->find($this->testUser3)->is($this->testUser3));
        $this->assertArrayHasKey('attached', $syncResults);
        $this->assertArrayHasKey('detached', $syncResults);
        $this->assertArrayHasKey('updated', $syncResults);
        $this->assertCount(1, $syncResults['attached']);
        $this->assertCount(0, $syncResults['updated']);
        $this->assertCount(0, $syncResults['detached']);
    }

    /** @test  */
    #[Test]
    public function it_can_sync_two_way_with_element_ids_with_corresponding_pivot_values()
    {
        // Direct Testing
        $this->initiateFurtherTestUsers();
        $testUser4 = $this->getModelClass()::create([
            'name' => "test 4",
            'age' => 44
        ])->fresh();

        $this->testUser1->friends()->sync([
            $this->testUser2->id => [],
            $this->testUser3->id => []
        ]);
        $syncResults = $this->testUser1->friends()->sync([
            $this->testUser3->id => ['percentage' => 66],
            $testUser4->id => ['percentage' => 10]
        ]);

        $testUser1Friends = $this->testUser1->friends()->get();
        $this->assertCount(2, $testUser1Friends);
        $this->assertTrue($testUser1Friends->contains('id', $this->testUser3->id));
        $this->assertTrue($testUser1Friends->contains('id', $testUser4->id));
        $this->assertArrayHasKey('attached', $syncResults);
        $this->assertArrayHasKey('detached', $syncResults);
        $this->assertArrayHasKey('updated', $syncResults);
        $this->assertCount(1, $syncResults['attached']);
        $this->assertCount(1, $syncResults['updated']);
        $this->assertCount(1, $syncResults['detached']);


        // Inverse Testing
        $this->initiateFurtherTestUsers();
        $testUser5 = $this->getModelClass()::create([
            'name' => "test 5",
            'age' => 44
        ])->fresh();

        $this->testUser3->friends()->sync([
            $this->testUser2->id => [],
            $this->testUser1->id => []
        ]);
        $this->testUser2->friends()->attach($this->testUser1, []);
        $syncResults = $this->testUser1->friends()->sync([
            $this->testUser3->id => ['percentage' => 80],
            $testUser5->id => ['percentage' => 20]
        ]);

        $testUser1Friends = $this->testUser1->friends()->get();
        $this->assertCount(2, $testUser1Friends);
        $this->assertTrue($testUser1Friends->contains('id', $this->testUser3->id));
        $this->assertTrue($testUser1Friends->contains('id', $testUser5->id));
        $this->assertArrayHasKey('attached', $syncResults);
        $this->assertArrayHasKey('detached', $syncResults);
        $this->assertArrayHasKey('updated', $syncResults);
        $this->assertCount(1, $syncResults['attached']);
        $this->assertCount(1, $syncResults['updated']);
        $this->assertCount(1, $syncResults['detached']);
    }

    /** @test  */
    #[Test]
    public function it_can_toggle_properly()
    {
        $this->initiateFurtherTestUsers();

        $this->testUser1->friends()->attach($this->testUser3);
        $this->testUser2->friends()->attach($this->testUser1);
        $this->assertCount(2, $this->testUser1->friends()->get());

        $toggleResult = $this->testUser1->friends()->toggle($this->testUser2);
        $this->assertCount(1, $this->testUser1->friends()->get());
        $this->assertArrayHasKey('attached', $toggleResult);
        $this->assertArrayHasKey('detached', $toggleResult);
        $this->assertCount(0, $toggleResult['attached']);
        $this->assertCount(1, $toggleResult['detached']);
        $this->assertContains($this->testUser2->id, $toggleResult['detached']);

        $toggleResult = $this->testUser1->friends()->toggle(EloquentCollection::make([$this->testUser2, $this->testUser3]));
        $this->assertCount(1, $this->testUser1->friends()->get());
        $this->assertArrayHasKey('attached', $toggleResult);
        $this->assertArrayHasKey('detached', $toggleResult);
        $this->assertCount(1, $toggleResult['attached']);
        $this->assertCount(1, $toggleResult['detached']);
        $this->assertContains($this->testUser2->id, $toggleResult['attached']);
        $this->assertContains($this->testUser3->id, $toggleResult['detached']);
    }

    /** @test */
    #[Test]
    public function nested_relations_can_be_eager_loaded()
    {
        $users = $this->getModelClass()::with('friends', 'friends.friends')->get();

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
    #[Test]
    public function it_can_load_other_aggregates_for_the_relation()
    {
        [$major, $minor, $patch] = laravel_version();
        if ($major < 8 || ($major == 8 && $minor <= 12))
            $this->markTestSkipped("The Aggregate functions are not available on the Laravel version {$major}.{$minor}.{$patch}");

        $users = $this->getModelClass()::query()
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
    #[Test]
    public function it_can_load_the_count_for_the_relation()
    {
        $users = $this->getModelClass()::withCount('friends')->get();

        $this->assertEquals(2, $users->find($this->user1_id)->friends_count);
        $this->assertEquals(3, $users->find($this->user2_id)->friends_count);
        $this->assertEquals(2, $users->find($this->user3_id)->friends_count);
        $this->assertEquals(3, $users->find($this->user4_id)->friends_count);
    }

    /** @test */
    #[Test]
    public function relation_can_be_paired_with_where_conditions()
    {
        $user1 = $this->getModelClass()::find($this->user1_id);
        $friends1 = $user1->friends()->where('age', '>', 20)->get();

        $this->assertCount(1, $friends1);
        $this->assertNotNull($friends1->find($this->user2_id));

        $user2 = $this->getModelClass()::find($this->user2_id);
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
    #[Test]
    public function relation_can_be_paired_with_the_order_by_clauses()
    {
        $user1 = $this->getModelClass()::find($this->user1_id);
        $friends1 = $user1->friends()
            ->orderBy('age')
            ->get();

        $this->assertCount(2, $friends1);
        $this->assertEquals($this->user4_id, $friends1->get(0)->id);
        $this->assertEquals($this->user2_id, $friends1->get(1)->id);

        $user2 = $this->getModelClass()::find($this->user2_id);
        $friends2 = $user2->friends()
            ->orderByDesc('age')
            ->get();

        $this->assertCount(3, $friends2);
        $this->assertEquals($this->user1_id, $friends2->get(0)->id);
        $this->assertEquals($this->user3_id, $friends2->get(1)->id);
        $this->assertEquals($this->user4_id, $friends2->get(2)->id);
    }

    /** @test */
    #[Test]
    public function it_can_be_used_in_where_has_relationship_eloquent_query()
    {
        $users_friend_with_user3 = $this->getModelClass()::whereHas('friends', function (Builder $friendQuery) {
            return $friendQuery->where('age', 14); // The Age of User3 is 14
        })->get();

        $this->assertCount(2, $users_friend_with_user3);
        $this->assertNotNull($users_friend_with_user3->find($this->user2_id));
        $this->assertNotNull($users_friend_with_user3->find($this->user4_id));

        $users_friend_with_user1_and_user4 = $this->getModelClass()::whereHas('friends', function (Builder $friendQuery) {
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
    #[Test]
    public function it_can_be_used_in_has_relationship_count_eloquent_query()
    {
        $user_with_more_than_2friends = $this->getModelClass()::has('friends', '>', 2)->get();

        $this->assertNotEmpty($user_with_more_than_2friends);
        $this->assertCount(2, $user_with_more_than_2friends);
        $this->assertNotNull($user_with_more_than_2friends->find($this->user2_id));
        $this->assertNotNull($user_with_more_than_2friends->find($this->user4_id));

        $callback = function ($query) {
            /** @var \Illuminate\Database\Eloquent\Builder $query */
            return $query->whereAge(14)
                ->orWhere('age', 24); // User 2 & 3
        };
        $user_with_sophisticated_friend = $this->getModelClass()::has('friends', '>=', 1, 'and', $callback)->get();
        $this->assertCount(4, $user_with_sophisticated_friend);
        $this->assertNotNull($user_with_sophisticated_friend->find($this->user1_id));
        $this->assertNotNull($user_with_sophisticated_friend->find($this->user2_id));
        $this->assertNotNull($user_with_sophisticated_friend->find($this->user3_id));
        $this->assertNotNull($user_with_sophisticated_friend->find($this->user4_id));

        $user_with_more_sophisticated_friend = $this->getModelClass()::has('friends', '>', 1, 'and', $callback)->get();
        $this->assertCount(1, $user_with_more_sophisticated_friend);
        $this->assertNotNull($user_with_more_sophisticated_friend->find($this->user4_id));
    }

    /** @test */
    #[Test]
    public function it_can_have_nested_repeated_where_has_eloquent_query()
    {
        $user5 = $this->getModelClass()::create([
            'name' => 'User 5',
            'age' => 40
        ])->fresh();
        $this->getModelClass()::find($this->user1_id)->friends()->attach($user5);

        $users = $this->getModelClass()::whereHas('friends', function (Builder $query) {
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


    protected function createDatabaseForManyToManySelf(): void
    {
        $modelClass = $this->getModelClass();
        if (is_subclass_of($modelClass, DatabaseSchemaRefreshable::class)) {
            $modelClass::refreshDatabaseSchema();
        } else {
            throw new \RuntimeException("Model class {$modelClass} must implement DatabaseSchemaRefreshable interface");
        }
    }

    protected function seedDataForManyToManySelf(): void
    {
        $this->initiateInitialTestUsers();
    }

    /**
     * Initiate Initial Test Users
     *
     * Initiate the Initial Users for the base Test cases.
     * @return void
     */
    protected function initiateInitialTestUsers(): void
    {
        $this->user1_id = $this->getModelClass()::create([
            'name' => 'User 1',
            'age' => 18,
            'birth_at' => Carbon::create(1994, 3, 21, 4, 36),
            'email' => 'user1@example.com'
        ])->fresh()->id;
        $this->user2_id = $this->getModelClass()::create([
            'name' => 'User 2',
            'age' => 24,
            'birth_at' => Carbon::create(1988, 8, 7, 18, 14)
        ])->fresh()->id;
        $this->user3_id = $this->getModelClass()::create([
            'name' => 'User 3',
            'age' => 14,
            'birth_at' => Carbon::create(1998, 2, 13, 9, 2),
            'email' => 'user3@w3c.org'
        ])->fresh()->id;
        $this->user4_id = $this->getModelClass()::create([
            'name' => 'User 4',
            'age' => 8
        ])->fresh()->id;

        DB::table('friends')->insert([
            ['user1' => $this->user1_id, 'user2' => $this->user4_id],
            ['user1' => $this->user2_id, 'user2' => $this->user1_id],
            ['user1' => $this->user2_id, 'user2' => $this->user3_id],
            ['user1' => $this->user3_id, 'user2' => $this->user4_id],
            ['user1' => $this->user4_id, 'user2' => $this->user2_id],
        ]);
    }

    /**
     * Initiate the Test Users for further Testing
     *
     * Initiate the Users for further Test cases.
     */
    protected function initiateFurtherTestUsers()
    {
        // Clearing away the existing Test Users
        foreach ([$this->testUser1, $this->testUser2, $this->testUser3] as $testUser) {
            if ($testUser !== null) {
                /** @var \Illuminate\Database\Eloquent\Model $testUser */
                DB::table($testUser->friends()->getTable())
                    ->where('user1', $testUser->id)
                    ->orWhere('user2', $testUser->id)
                    ->delete();

                $testUser->delete();
            }
        }

        $this->testUser1 = $this->getModelClass()::create([
            'name' => 'test 1',
            'age' => 18
        ])->fresh();
        $this->testUser2 = $this->getModelClass()::create([
            'name' => 'test 2',
            'age' => 22
        ])->fresh();
        $this->testUser3 = $this->getModelClass()::create([
            'name' => 'test 3',
            'age' => 33
        ])->fresh();
    }
}
