<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\User;
use App\Services\UserService;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Mockery;

class UserServiceTest extends TestCase
{
    private UserService $service;
    private UserRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(UserRepositoryInterface::class);
        $this->service = new UserService($this->repository);
    }

    public function test_create_user_hashes_password()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'role' => User::ROLE_STUDENT
        ];

        $user = new User($userData);
        $user->id = 1;

        $this->repository
            ->expects('create')
            ->with(Mockery::on(function ($data) {
                return $data['name'] === 'John Doe' &&
                    $data['email'] === 'john@example.com' &&
                    Hash::check('password123', $data['password']) &&
                    $data['role'] === User::ROLE_STUDENT;
            }))
            ->andReturn($user);

        $result = $this->service->createUser($userData);

        $this->assertEquals(1, $result->id);
        $this->assertEquals('John Doe', $result->name);
    }

    public function test_update_profile_excludes_password_and_email()
    {
        $userData = [
            'name' => 'Updated Name',
            'phone' => '123456789',
            'password' => 'should_be_ignored',
            'email' => 'should_be_ignored@example.com'
        ];

        $this->repository
            ->expects('findOrFail')
            ->with(1)
            ->andReturn(new User(['id' => 1]));

        $this->repository
            ->expects('update')
            ->with(1, ['name' => 'Updated Name', 'phone' => '123456789'])
            ->andReturn(true);

        $result = $this->service->updateProfile(1, $userData);

        $this->assertTrue($result);
    }

    public function test_change_password_validates_current_password()
    {
        $user = new User(['password' => Hash::make('current_password')]);
        $user->id = 1;

        $this->repository
            ->expects('findOrFail')
            ->with(1)
            ->andReturn($user);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Current password is incorrect');

        $this->service->changePassword(1, 'wrong_password', 'new_password');
    }

    public function test_change_password_updates_with_hashed_password()
    {
        $user = new User(['password' => Hash::make('current_password')]);
        $user->id = 1;

        $this->repository
            ->expects('findOrFail')
            ->with(1)
            ->andReturn($user);

        $this->repository
            ->expects('update')
            ->with(1, Mockery::on(function ($data) {
                return isset($data['password']) && Hash::check('new_password', $data['password']);
            }))
            ->andReturn(true);

        $result = $this->service->changePassword(1, 'current_password', 'new_password');

        $this->assertTrue($result);
    }

    public function test_find_by_email_delegates_to_repository()
    {
        $user = new User(['email' => 'test@example.com']);

        $this->repository
            ->expects('findByEmail')
            ->with('test@example.com')
            ->andReturn($user);

        $result = $this->service->findByEmail('test@example.com');

        $this->assertEquals($user, $result);
    }

    public function test_get_users_by_role_delegates_to_repository()
    {
        $users = User::factory(2)->make(['role' => User::ROLE_STUDENT]);

        $this->repository
            ->expects('getByRole')
            ->with(User::ROLE_STUDENT)
            ->andReturn($users);

        $result = $this->service->getUsersByRole(User::ROLE_STUDENT);

        $this->assertEquals($users, $result);
    }

    public function test_is_email_available_returns_opposite_of_exists()
    {
        $this->repository
            ->expects('emailExists')
            ->with('test@example.com', null)
            ->andReturn(true);

        $result = $this->service->isEmailAvailable('test@example.com');

        $this->assertFalse($result);
    }

    public function test_update_method_with_password_hashing()
    {
        $updateData = [
            'name' => 'Updated Name',
            'password' => 'new_password'
        ];

        $user = new User(['id' => 1]);

        $this->repository
            ->expects('findOrFail')
            ->with(1)
            ->andReturn($user);

        $this->repository
            ->expects('update')
            ->with(1, Mockery::on(function ($data) {
                return $data['name'] === 'Updated Name' &&
                    isset($data['password']) &&
                    Hash::check('new_password', $data['password']);
            }))
            ->andReturn(true);

        $result = $this->service->update(1, $updateData);

        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
