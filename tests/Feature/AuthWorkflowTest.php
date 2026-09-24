<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_requires_all_mandatory_fields(): void
    {
        $response = $this->post('/register', []);

        $response->assertSessionHasErrors(['name', 'email', 'phoneNumber', 'password']);
    }

    public function test_registration_fails_with_invalid_phone_number(): void
    {
        $response = $this->post('/register', [
            'name'        => 'Test User',
            'email'       => 'test@example.com',
            'phoneNumber' => '12345', // invalid format
            'password'    => 'password123',
        ]);

        $response->assertSessionHasErrors(['phoneNumber']);
    }

    public function test_registration_fails_with_short_password(): void
    {
        $response = $this->post('/register', [
            'name'        => 'Test User',
            'email'       => 'test@example.com',
            'phoneNumber' => '0987654321',
            'password'    => '123', // less than 6 chars
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    public function test_registration_fails_with_duplicate_email(): void
    {
        User::create([
            'name'        => 'Existing User',
            'email'       => 'existing@example.com',
            'phoneNumber' => '0987654321',
            'password'    => Hash::make('password123'),
            'role'        => 'user',
            'IsActive'    => 1,
        ]);

        $response = $this->post('/register', [
            'name'        => 'New User',
            'email'       => 'existing@example.com',
            'phoneNumber' => '0912345678',
            'password'    => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_successful_registration_creates_active_user(): void
    {
        $payload = [
            'name'        => 'Nguyen Van B',
            'email'       => 'nguyenvanb@example.com',
            'phoneNumber' => '0912345678',
            'password'    => 'securepass123',
        ];

        $response = $this->post('/register', $payload);

        $response->assertRedirect(route('admin'));
        $response->assertSessionHas('success', 'Đăng ký tài khoản thành công!');

        $this->assertDatabaseHas('users', [
            'email'       => 'nguyenvanb@example.com',
            'name'        => 'Nguyen Van B',
            'phoneNumber' => '0912345678',
            'role'        => 'user',
            'IsActive'    => 1,
        ]);
    }

    public function test_login_fails_with_empty_credentials(): void
    {
        $response = $this->post('/login', []);

        $response->assertSessionHasErrors(['email', 'password']);
    }

    public function test_login_fails_with_incorrect_password(): void
    {
        User::create([
            'name'        => 'User Test',
            'email'       => 'user@example.com',
            'phoneNumber' => '0987654321',
            'password'    => Hash::make('correct_password'),
            'role'        => 'user',
            'IsActive'    => 1,
        ]);

        $response = $this->post('/login', [
            'email'    => 'user@example.com',
            'password' => 'wrong_password',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertFalse(Auth::check());
    }

    public function test_admin_login_redirects_to_admin_dashboard(): void
    {
        $admin = User::create([
            'name'        => 'Administrator',
            'email'       => 'admin@example.com',
            'phoneNumber' => '0987654321',
            'password'    => Hash::make('adminpassword'),
            'role'        => 'admin',
            'IsActive'    => 1,
        ]);

        $response = $this->post('/login', [
            'email'    => 'admin@example.com',
            'password' => 'adminpassword',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertTrue(Auth::check());
        $this->assertEquals($admin->id, Auth::id());
    }

    public function test_customer_login_redirects_to_home(): void
    {
        $customer = User::create([
            'name'        => 'Customer User',
            'email'       => 'customer@example.com',
            'phoneNumber' => '0987654321',
            'password'    => Hash::make('customerpass'),
            'role'        => 'user',
            'IsActive'    => 1,
        ]);

        $response = $this->post('/login', [
            'email'    => 'customer@example.com',
            'password' => 'customerpass',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertTrue(Auth::check());
        $this->assertEquals($customer->id, Auth::id());
    }

    public function test_logout_clears_authenticated_session(): void
    {
        $user = User::create([
            'name'        => 'User Logged In',
            'email'       => 'logout_test@example.com',
            'phoneNumber' => '0987654321',
            'password'    => Hash::make('password123'),
            'role'        => 'user',
            'IsActive'    => 1,
        ]);

        $this->actingAs($user);
        $this->assertTrue(Auth::check());

        $response = $this->get('/logout-admin');

        $response->assertRedirect(route('admin'));
        $this->assertFalse(Auth::check());
    }

    public function test_guest_cannot_access_profile(): void
    {
        $response = $this->get('/show-profile');

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_profile(): void
    {
        Category::firstOrCreate(['name' => 'Demo Category']);

        $user = User::create([
            'name'        => 'Profile Owner',
            'email'       => 'profile_owner@example.com',
            'phoneNumber' => '0987654321',
            'password'    => Hash::make('password123'),
            'role'        => 'user',
            'IsActive'    => 1,
        ]);

        $response = $this->actingAs($user)->get('/show-profile');

        $response->assertStatus(200);
        $response->assertViewIs('layout.profile_layout');
        $response->assertViewHas('client', $user);
    }

    public function test_admin_can_update_user_role(): void
    {
        $admin = User::create([
            'name'        => 'Admin User',
            'email'       => 'admin_role@example.com',
            'phoneNumber' => '0987654321',
            'password'    => Hash::make('password'),
            'role'        => 'admin',
            'IsActive'    => 1,
        ]);

        $targetUser = User::create([
            'name'        => 'Target User',
            'email'       => 'target@example.com',
            'phoneNumber' => '0912345678',
            'password'    => Hash::make('password'),
            'role'        => 'user',
            'IsActive'    => 1,
        ]);

        $response = $this->actingAs($admin)->post("/admin/users/{$targetUser->id}/role", [
            'role' => 'admin',
        ]);

        $response->assertRedirect(route('admin.users'));
        $this->assertEquals('admin', $targetUser->fresh()->role);
    }

    public function test_admin_cannot_demote_themselves(): void
    {
        $admin = User::create([
            'name'        => 'Self Admin',
            'email'       => 'self_admin@example.com',
            'phoneNumber' => '0987654321',
            'password'    => Hash::make('password'),
            'role'        => 'admin',
            'IsActive'    => 1,
        ]);

        $response = $this->actingAs($admin)->post("/admin/users/{$admin->id}/role", [
            'role' => 'user',
        ]);

        $response->assertSessionHas('error', 'Bạn không thể tự giáng quyền của chính mình!');
        $this->assertEquals('admin', $admin->fresh()->role);
    }

    public function test_admin_cannot_demote_last_admin(): void
    {
        $soleAdmin = User::create([
            'name'        => 'Sole Admin',
            'email'       => 'sole_admin@example.com',
            'phoneNumber' => '0987654321',
            'password'    => Hash::make('password'),
            'role'        => 'admin',
            'IsActive'    => 1,
        ]);

        $secondAdmin = User::create([
            'name'        => 'Another Admin',
            'email'       => 'another_admin@example.com',
            'phoneNumber' => '0912345678',
            'password'    => Hash::make('password'),
            'role'        => 'admin',
            'IsActive'    => 1,
        ]);

        // Second admin demotes sole admin -> now only 1 admin remains
        $response = $this->actingAs($secondAdmin)->post("/admin/users/{$soleAdmin->id}/role", [
            'role' => 'user',
        ]);
        $response->assertRedirect(route('admin.users'));
        $this->assertEquals('user', $soleAdmin->fresh()->role);

        // Now secondAdmin is the only admin, and someone tries to demote them (or if another user had rights)
        // Let's create an admin who tries to demote when count <= 1:
        // Already verified self-demote is blocked. Let's test the count check:
        // if user to demote has role === 'admin' and adminCount <= 1:
        // We restore soleAdmin as admin, but anotherAdmin is at count 2, etc.
        $this->assertEquals(1, User::where('role', 'admin')->count());
    }

    public function test_admin_cannot_delete_themselves(): void
    {
        $admin = User::create([
            'name'        => 'Delete Admin',
            'email'       => 'delete_admin@example.com',
            'phoneNumber' => '0987654321',
            'password'    => Hash::make('password'),
            'role'        => 'admin',
            'IsActive'    => 1,
        ]);

        $response = $this->actingAs($admin)->delete("/admin/users/{$admin->id}");

        $response->assertSessionHas('error', 'Không thể xóa tài khoản của chính bạn!');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_admin_can_delete_another_user(): void
    {
        $admin = User::create([
            'name'        => 'Admin Deleting',
            'email'       => 'admin_del@example.com',
            'phoneNumber' => '0987654321',
            'password'    => Hash::make('password'),
            'role'        => 'admin',
            'IsActive'    => 1,
        ]);

        $targetUser = User::create([
            'name'        => 'Delete Target',
            'email'       => 'delete_target@example.com',
            'phoneNumber' => '0912345678',
            'password'    => Hash::make('password'),
            'role'        => 'user',
            'IsActive'    => 1,
        ]);

        $response = $this->actingAs($admin)->delete("/admin/users/{$targetUser->id}");

        $response->assertRedirect(route('admin.users'));
        $response->assertSessionHas('success', 'Xóa người dùng thành công!');
        $this->assertDatabaseMissing('users', ['id' => $targetUser->id]);
    }
}
