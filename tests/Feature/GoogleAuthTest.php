<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_redirect_to_google_when_configured()
    {
        config([
            'services.google.client_id' => 'test-client-id',
            'services.google.client_secret' => 'test-client-secret',
            'services.google.redirect' => 'http://localhost/auth/google/callback',
        ]);

        $response = $this->get(route('auth.google'));
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('accounts.google.com', (string) $response->headers->get('Location'));
    }

    public function test_redirect_to_google_fails_when_not_configured()
    {
        config([
            'services.google.client_id' => null,
            'services.google.client_secret' => null,
        ]);

        $response = $this->get(route('auth.google'));
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
    }

    public function test_google_callback_with_error_query()
    {
        $response = $this->get(route('auth.google.callback', ['error' => 'access_denied']));
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
    }

    public function test_google_callback_creates_new_user()
    {
        $email = 'newuser_' . time() . '@example.com';
        $googleId = 'google_id_' . time();

        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn($googleId);
        $socialiteUser->shouldReceive('getEmail')->andReturn($email);
        $socialiteUser->shouldReceive('getName')->andReturn('Google Test User');
        $socialiteUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/avatar.jpg');

        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('user')->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('home'));
        $this->assertTrue(Auth::check());
        $this->assertEquals($email, Auth::user()->email);
        $this->assertEquals($googleId, Auth::user()->google_id);

        // Cleanup
        User::where('email', $email)->delete();
    }

    public function test_google_callback_links_existing_user()
    {
        $email = 'existing_' . time() . '@example.com';
        $googleId = 'google_id_' . time();

        $existingUser = User::create([
            'name' => 'Existing User',
            'email' => $email,
            'password' => bcrypt('password123'),
            'role' => 'user',
            'IsActive' => 1,
            'phoneNumber' => '0987654321',
        ]);

        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn($googleId);
        $socialiteUser->shouldReceive('getEmail')->andReturn($email);
        $socialiteUser->shouldReceive('getName')->andReturn('Existing User Name');
        $socialiteUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/avatar2.jpg');

        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('user')->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('home'));
        $this->assertTrue(Auth::check());
        $this->assertEquals($existingUser->id, Auth::id());
        $this->assertEquals($googleId, Auth::user()->google_id);

        // Cleanup
        $existingUser->delete();
    }

    public function test_google_callback_redirects_admin_to_admin_dashboard()
    {
        $email = 'admin_' . time() . '@example.com';
        $googleId = 'google_id_' . time();

        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => $email,
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'IsActive' => 1,
            'phoneNumber' => '0987654321',
            'google_id' => $googleId,
        ]);

        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn($googleId);
        $socialiteUser->shouldReceive('getEmail')->andReturn($email);
        $socialiteUser->shouldReceive('getName')->andReturn('Admin User');
        $socialiteUser->shouldReceive('getAvatar')->andReturn(null);

        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('user')->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertTrue(Auth::check());
        $this->assertEquals('admin', Auth::user()->role);

        // Cleanup
        $adminUser->delete();
    }
}
