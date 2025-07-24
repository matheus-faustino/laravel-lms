<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private UserRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new UserRepository(new User());
    }

    public function test_find_by_email_returns_user()
    {
        $user = User::factory()->create(['email' => 'test@example.com']);

        $found = $this->repository->findByEmail('test@example.com');

        $this->assertInstanceOf(User::class, $found);
        $this->assertEquals($user->id, $found->id);
    }

    public function test_find_by_email_returns_null_when_not_found()
    {
        $found = $this->repository->findByEmail('nonexistent@example.com');

        $this->assertNull($found);
    }

    public function test_get_by_role_returns_users_with_specific_role()
    {
        User::factory()->create(['role' => User::ROLE_ADMIN]);
        User::factory(2)->create(['role' => User::ROLE_STUDENT]);

        $students = $this->repository->getByRole(User::ROLE_STUDENT);
        $admins = $this->repository->getByRole(User::ROLE_ADMIN);

        $this->assertCount(2, $students);
        $this->assertCount(1, $admins);
        $this->assertTrue($students->every(fn($user) => $user->role === User::ROLE_STUDENT));
        $this->assertTrue($admins->every(fn($user) => $user->role === User::ROLE_ADMIN));
    }

    public function test_search_users_filters_by_search_term()
    {
        User::factory()->create(['name' => 'John Doe', 'email' => 'john@example.com']);
        User::factory()->create(['name' => 'Jane Smith', 'email' => 'jane@example.com']);

        $results = $this->repository->searchUsers(['search' => 'John']);

        $this->assertEquals(1, $results->total());
        $this->assertEquals('John Doe', $results->first()->name);
    }

    public function test_search_users_filters_by_role()
    {
        User::factory(2)->create(['role' => User::ROLE_ADMIN]);
        User::factory(3)->create(['role' => User::ROLE_STUDENT]);

        $results = $this->repository->searchUsers(['role' => User::ROLE_ADMIN]);

        $this->assertEquals(2, $results->total());
        $this->assertTrue($results->getCollection()->every(fn($user) => $user->role === User::ROLE_ADMIN));
    }

    public function test_email_exists_returns_true_when_email_exists()
    {
        User::factory()->create(['email' => 'test@example.com']);

        $exists = $this->repository->emailExists('test@example.com');

        $this->assertTrue($exists);
    }

    public function test_email_exists_returns_false_when_email_not_exists()
    {
        $exists = $this->repository->emailExists('nonexistent@example.com');

        $this->assertFalse($exists);
    }

    public function test_email_exists_excludes_user_id()
    {
        $user = User::factory()->create(['email' => 'test@example.com']);

        $exists = $this->repository->emailExists('test@example.com', $user->id);

        $this->assertFalse($exists);
    }
}
