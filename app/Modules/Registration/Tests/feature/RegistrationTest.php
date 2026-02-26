<?php

namespace App\Modules\Registration\Tests\Feature;

use Tests\TestCase;
use App\Modules\Registration\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Events\Registered;
use App\Modules\Registration\Notifications\WelcomeEmail;
use App\Modules\Registration\Notifications\CreatorPendingApproval;

class RegistrationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        Event::fake();
        Notification::fake();
    }

    /** @test */
    public function user_can_view_registration_page()
    {
        $response = $this->get(route('register.form'));

        $response->assertStatus(200);
        $response->assertViewIs('registration::register');
        $response->assertSee('Choose your account type');
        $response->assertSee('Event Attendee');
        $response->assertSee('Event Creator');
    }

    /** @test */
    public function attendee_can_register_with_valid_data()
    {
        $userData = [
            'user_type' => 'attendee',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'age' => 25,
            'terms' => true
        ];

        $response = $this->post(route('register.submit'), $userData);

        $response->assertRedirect(route('attendee.dashboard'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'user_type' => 'attendee',
            'is_approved' => true
        ]);

        $user = User::where('email', 'john@example.com')->first();
        $this->assertTrue(Hash::check('Password123!', $user->password));
        
        // Check profile created
        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $user->id
        ]);

        // Check role assigned
        $this->assertTrue($user->hasRole('attendee'));

        // Check event fired
        Event::assertDispatched(Registered::class);

        // Check notification sent
        Notification::assertSentTo($user, WelcomeEmail::class);
    }

    /** @test */
    public function creator_can_register_with_valid_data()
    {
        $userData = [
            'user_type' => 'event_creator',
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'phone' => '+1234567890',
            'organization_name' => 'Jane Events Inc',
            'tax_id' => 'TAX123456',
            'terms' => true
        ];

        $response = $this->post(route('register.submit'), $userData);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success', 'pending approval');

        $this->assertDatabaseHas('users', [
            'email' => 'jane@example.com',
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'user_type' => 'event_creator',
            'organization_name' => 'Jane Events Inc',
            'phone' => '+1234567890',
            'tax_id' => 'TAX123456',
            'is_approved' => false
        ]);

        $user = User::where('email', 'jane@example.com')->first();
        
        // No role assigned yet (pending approval)
        $this->assertFalse($user->hasRole('event-creator'));

        // Check notification sent
        Notification::assertSentTo($user, CreatorPendingApproval::class);
    }

    /** @test */
    public function registration_fails_when_email_already_exists()
    {
        // Create existing user
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->post(route('register.submit'), [
            'user_type' => 'attendee',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'existing@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'age' => 25,
            'terms' => true
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertCount(1, User::where('email', 'existing@example.com')->get());
    }

    /** @test */
    public function attendee_registration_fails_when_age_below_18()
    {
        $response = $this->post(route('register.submit'), [
            'user_type' => 'attendee',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'age' => 17,
            'terms' => true
        ]);

        $response->assertSessionHasErrors('age');
        $this->assertDatabaseMissing('users', ['email' => 'john@example.com']);
    }

    /** @test */
    public function creator_registration_fails_without_organization()
    {
        $response = $this->post(route('register.submit'), [
            'user_type' => 'event_creator',
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'phone' => '+1234567890',
            'terms' => true
        ]);

        $response->assertSessionHasErrors(['organization_name']);
    }

    /** @test */
    public function registration_fails_when_password_too_weak()
    {
        $response = $this->post(route('register.submit'), [
            'user_type' => 'attendee',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'weak',
            'password_confirmation' => 'weak',
            'age' => 25,
            'terms' => true
        ]);

        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function registration_fails_when_passwords_dont_match()
    {
        $response = $this->post(route('register.submit'), [
            'user_type' => 'attendee',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Different123!',
            'age' => 25,
            'terms' => true
        ]);

        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function registration_fails_without_terms_acceptance()
    {
        $response = $this->post(route('register.submit'), [
            'user_type' => 'attendee',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'age' => 25,
            'terms' => false
        ]);

        $response->assertSessionHasErrors('terms');
    }

    /** @test */
    public function username_is_auto_generated_if_not_provided()
    {
        $response = $this->post(route('register.submit'), [
            'user_type' => 'attendee',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'age' => 25,
            'terms' => true
        ]);

        $user = User::where('email', 'john@example.com')->first();
        $this->assertEquals('john.doe', $user->username);
    }

    /** @test */
    public function username_is_made_unique_if_duplicate()
    {
        // Create first user
        User::factory()->create(['username' => 'john.doe']);

        $response = $this->post(route('register.submit'), [
            'user_type' => 'attendee',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john2@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'age' => 25,
            'terms' => true
        ]);

        $user = User::where('email', 'john2@example.com')->first();
        $this->assertEquals('john.doe1', $user->username);
    }

    /** @test */
    public function registration_is_rate_limited()
    {
        for ($i = 0; $i < 3; $i++) {
            $response = $this->post(route('register.submit'), [
                'user_type' => 'attendee',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => "john{$i}@example.com",
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
                'age' => 25,
                'terms' => true
            ]);
        }

        // 4th attempt should be rate limited
        $response = $this->post(route('register.submit'), [
            'user_type' => 'attendee',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john4@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'age' => 25,
            'terms' => true
        ]);

        $response->assertSessionHasErrors(); // Rate limited
    }

    /** @test */
    public function profile_is_automatically_created_after_registration()
    {
        $response = $this->post(route('register.submit'), [
            'user_type' => 'attendee',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'age' => 25,
            'terms' => true
        ]);

        $user = User::where('email', 'john@example.com')->first();
        
        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $user->id
        ]);
    }
}