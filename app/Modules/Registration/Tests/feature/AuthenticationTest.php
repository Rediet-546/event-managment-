<?php

namespace App\Modules\Registration\Tests\Feature;

use Tests\TestCase;
use App\Modules\Registration\Models\User;
use App\Modules\Registration\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Events\Registered;
use App\Modules\Registration\Notifications\WelcomeEmail;
use App\Modules\Registration\Notifications\CreatorPendingApproval;
use Carbon\Carbon;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $admin;
    protected $creator;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test users
        $this->user = User::factory()->create([
            'email' => 'user@test.com',
            'password' => Hash::make('password123'),
            'user_type' => 'attendee',
            'is_active' => true,
            'email_verified_at' => now()
        ]);

        $this->admin = User::factory()->create([
            'email' => 'admin@test.com',
            'user_type' => 'admin',
            'is_active' => true,
            'email_verified_at' => now()
        ]);
        $this->admin->assignRole('admin');

        $this->creator = User::factory()->create([
            'email' => 'creator@test.com',
            'user_type' => 'event_creator',
            'is_approved' => true,
            'is_active' => true,
            'email_verified_at' => now()
        ]);
        $this->creator->assignRole('event-creator');
    }

    /** @test */
    public function user_can_view_login_page()
    {
        $response = $this->get(route('login.form'));

        $response->assertStatus(200);
        $response->assertViewIs('registration::login');
        $response->assertSee('Login');
        $response->assertSee('Email or Username');
        $response->assertSee('Password');
    }

    /** @test */
    public function user_can_login_with_email()
    {
        $response = $this->post(route('login.submit'), [
            'login' => 'user@test.com',
            'password' => 'password123'
        ]);

        $response->assertRedirect(route('attendee.dashboard'));
        $this->assertAuthenticatedAs($this->user);
        $response->assertSessionHas('success');
    }

    /** @test */
    public function user_can_login_with_username()
    {
        $response = $this->post(route('login.submit'), [
            'login' => $this->user->username,
            'password' => 'password123'
        ]);

        $response->assertRedirect(route('attendee.dashboard'));
        $this->assertAuthenticatedAs($this->user);
    }

    /** @test */
    public function user_cannot_login_with_invalid_password()
    {
        $response = $this->post(route('login.submit'), [
            'login' => 'user@test.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    /** @test */
    public function user_cannot_login_with_nonexistent_email()
    {
        $response = $this->post(route('login.submit'), [
            'login' => 'nonexistent@test.com',
            'password' => 'password123'
        ]);

        $response->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    /** @test */
    public function inactive_user_cannot_login()
    {
        $this->user->update(['is_active' => false]);

        $response = $this->post(route('login.submit'), [
            'login' => 'user@test.com',
            'password' => 'password123'
        ]);

        $response->assertSessionHasErrors('login');
        $this->assertGuest();
        $response->assertSessionHasErrors(['login' => 'Your account has been deactivated']);
    }

    /** @test */
    public function unverified_user_cannot_login()
    {
        $this->user->update(['email_verified_at' => null]);

        $response = $this->post(route('login.submit'), [
            'login' => 'user@test.com',
            'password' => 'password123'
        ]);

        $response->assertSessionHasErrors('login');
        $this->assertGuest();
        $response->assertSessionHasErrors(['login' => 'Please verify your email address']);
    }

    /** @test */
    public function unapproved_creator_cannot_login()
    {
        $pendingCreator = User::factory()->create([
            'email' => 'pending@test.com',
            'user_type' => 'event_creator',
            'is_approved' => false,
            'is_active' => true,
            'email_verified_at' => now()
        ]);

        $response = $this->post(route('login.submit'), [
            'login' => 'pending@test.com',
            'password' => 'password'
        ]);

        $response->assertSessionHasErrors('login');
        $this->assertGuest();
        $response->assertSessionHasErrors(['login' => 'pending approval']);
    }

    /** @test */
    public function login_is_throttled_after_five_attempts()
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login.submit'), [
                'login' => 'user@test.com',
                'password' => 'wrongpassword'
            ]);
        }

        $response = $this->post(route('login.submit'), [
            'login' => 'user@test.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertSessionHasErrors('login');
        $this->assertStringContainsString('Too many login attempts', session('errors')->first('login'));
    }

    /** @test */
    public function user_can_logout()
    {
        $this->actingAs($this->user);

        $response = $this->post(route('logout'));

        $response->assertRedirect('/');
        $this->assertGuest();
        $response->assertSessionHas('success', 'You have been logged out successfully.');
    }

    /** @test */
    public function admin_redirected_to_admin_dashboard_after_login()
    {
        $response = $this->post(route('login.submit'), [
            'login' => 'admin@test.com',
            'password' => 'password'
        ]);

        $response->assertRedirect(route('admin.dashboard'));
    }

    /** @test */
    public function creator_redirected_to_creator_dashboard_after_login()
    {
        $response = $this->post(route('login.submit'), [
            'login' => 'creator@test.com',
            'password' => 'password'
        ]);

        $response->assertRedirect(route('creator.dashboard'));
    }

    /** @test */
    public function last_login_is_updated_on_successful_login()
    {
        $this->assertNull($this->user->last_login_at);

        $this->post(route('login.submit'), [
            'login' => 'user@test.com',
            'password' => 'password123'
        ]);

        $this->user->refresh();
        $this->assertNotNull($this->user->last_login_at);
        $this->assertEquals(request()->ip(), $this->user->last_login_ip);
    }

    /** @test */
    public function user_can_request_password_reset_link()
    {
        Notification::fake();

        $response = $this->post(route('password.email'), [
            'email' => 'user@test.com'
        ]);

        $response->assertSessionHas('status', 'We have emailed your password reset link!');
        
        Notification::assertSentTo(
            $this->user,
            \Illuminate\Auth\Notifications\ResetPassword::class
        );
    }

    /** @test */
    public function user_cannot_request_reset_for_nonexistent_email()
    {
        $response = $this->post(route('password.email'), [
            'email' => 'nonexistent@test.com'
        ]);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function user_can_reset_password_with_valid_token()
    {
        $token = Password::broker()->createToken($this->user);

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'user@test.com',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!'
        ]);

        $response->assertSessionHas('status', 'Your password has been reset successfully!');
        
        $this->user->refresh();
        $this->assertTrue(Hash::check('NewPassword123!', $this->user->password));
    }

    /** @test */
    public function user_cannot_reset_password_with_invalid_token()
    {
        $response = $this->post(route('password.update'), [
            'token' => 'invalid-token',
            'email' => 'user@test.com',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!'
        ]);

        $response->assertSessionHasErrors('email');
    }
}