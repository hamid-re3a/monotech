<?php


namespace User\tests\Feature;


use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use User\Mail\User\EmailVerifyOtp;
use User\Mail\User\ForgetPasswordOtpEmail;
use User\Mail\User\TooManyLoginAttemptPermanentBlockedEmail;
use User\Mail\User\TooManyLoginAttemptTemporaryBlockedEmail;
use User\Mail\User\WelcomeEmail;
use User\Models\LoginAttempt as LoginAttemptModel;
use User\Models\Otp;
use User\Models\User;

class UserTest extends \User\tests\UserTest
{



    /**
     * @test
     */
    public function register_new_user_green()
    {

        Mail::fake();
        $response = $this->post(route('auth.register'), [
            "first_name" => 'hamid',
            "last_name" => 'noruzi',
            "email" => 'hamidrezanoruzinejad@gmail.com',
            "username" => 'hamid',
            "password" => '123456789!Q',
            "password_confirmation" => '123456789!Q',
        ]);
        $response->assertOk();
        Mail::assertSent(WelcomeEmail::class);
    }

    /**
     * @test
     */
    public function register_new_user_not_enough_strong_password()
    {
        Mail::fake();
        $response = $this->post(route('auth.register'), [
            "first_name" => 'hamid',
            "last_name" => 'noruzi',
            "email" => 'hamidrezanoruzinejad@gmail.com',
            "username" => 'hamid',
            "password" => '123',
            "password_confirmation" => '123',
        ]);
        $response->assertStatus(422);
        Mail::assertNotSent(WelcomeEmail::class);
    }


    /**
     * @test
     */
    public function login_when_user_has_not_activate_its_account()
    {
        $user = User::factory()->create([
            "email" => 'hamidrezanoruzinejad@gmail.com',
            "password" => 'password'
        ]);
        $response = $this->post(route('auth.login'), [
            "email" => 'hamidrezanoruzinejad@gmail.com',
            "password" => 'password',
        ]);
        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function login_user_green()
    {
        $user = User::factory()->create([
            "email" => 'hamidrezanoruzinejad@gmail.com',
            "password" => 'password'
        ]);
        $user->email_verified_at = now();
        $user->save();
        $response = $this->post(route('auth.login'), [
            "email" => 'hamidrezanoruzinejad@gmail.com',
            "password" => 'password',
        ]);
        $response->assertOk();
    }

    /**
     * @test
     */
    public function login_user_block()
    {
        Mail::fake();
        $user = User::factory()->create([
            "email" => 'hamidrezanoruzinejad@gmail.com',
            "password" => 'password'
        ]);
        $user->email_verified_at = now();
        $user->save();
        list($intervals, $tries) = getLoginAttemptSetting();
        foreach ($intervals as $key => $interval) {
            for ($i = 0; $i <= $tries[$key]; $i++) {

                $response = $this->post(route('auth.login'), [
                    "email" => 'hamidrezanoruzinejad@gmail.com',
                    "password" => 'incorrect password',
                ]);
            }
            $blocked_layer = LoginAttemptModel::query()
                ->where('user_id', $user->id)
                ->whereBetween('created_at', [now()->subDays(1)->format('Y-m-d H:i:s'), now()->format('Y-m-d H:i:s')])
                ->get()
                ->max('blocked_tier');
            $this->assertEquals($key,$blocked_layer);
            Carbon::setTestNow(now()->addSeconds($interval+2));
            Mail::assertSent(TooManyLoginAttemptTemporaryBlockedEmail::class);

        }

        Mail::assertSent(TooManyLoginAttemptPermanentBlockedEmail::class);

        $user->refresh();
        $this->assertEquals(USER_BLOCK_TYPE_AUTOMATIC, $user->block_type);
    }

    /**
     * @test
     */
    public function login_user_block_temporary_scenario()
    {
        Mail::fake();
        $user = User::factory()->create([
            "email" => 'hamidrezanoruzinejad@gmail.com',
            "password" => 'password'
        ]);
        $user->email_verified_at = now();
        $user->save();
        list($intervals, $tries) = getLoginAttemptSetting();
        foreach ($intervals as $key => $interval) {
            for ($i = 0; $i <= $tries[$key]; $i++) {

                $response = $this->post(route('auth.login'), [
                    "email" => 'hamidrezanoruzinejad@gmail.com',
                    "password" => 'incorrect password',
                ]);
            }


            Mail::assertSent(TooManyLoginAttemptTemporaryBlockedEmail::class);
            $response = $this->post(route('auth.login'), [
                "email" => 'hamidrezanoruzinejad@gmail.com',
                "password" => 'incorrect password',
            ]);
            $this->assertEquals(429, $response->status());
            break;
        }
    }

    /**
     * @test
     */
    public function user_forgot_password_green()
    {
        Mail::fake();
        $user = User::factory()->create([
            "email" => 'hamidrezanoruzinejad@gmail.com',
            "password" => 'password'
        ]);
        $user->email_verified_at = now();
        $user->save();
        $response = $this->post(route('auth.forgot-password'), [
            "email" => 'hamidrezanoruzinejad@gmail.com',
        ]);
        Mail::assertSent(ForgetPasswordOtpEmail::class);
    }

    /**
     * @test
     */
    public function user_forgot_password_failed()
    {
        Mail::fake();
        $user = User::factory()->create([
            "email" => 'hamidrezanoruzinejad@gmail.com',
            "password" => 'password'
        ]);
        $user->email_verified_at = now();
        $user->save();
        $response = $this->post(route('auth.forgot-password'), [
            "email" => 'hamidrezanoruzinejad@gmail.com',
        ]);
        $response = $this->post(route('auth.forgot-password'), [
            "email" => 'hamidrezanoruzinejad@gmail.com',
        ]);
        $this->assertEquals(429, $response->status());
    }


    /**
     * @test
     */
    public function user_forgot_password_not_valid_reset()
    {
        Mail::fake();
        $user = User::factory()->create([
            "email" => 'hamidrezanoruzinejad@gmail.com',
            "password" => 'password'
        ]);
        $user->email_verified_at = now();
        $user->save();
        $response = $this->post(route('auth.forgot-password'), [
            "email" => 'hamidrezanoruzinejad@gmail.com',
        ]);
        Carbon::setTestNow(now()->addSeconds(USER_FORGOT_PASSWORD_OTP_DURATION));
        Carbon::setTestNow(now()->addSeconds(USER_FORGOT_PASSWORD_OTP_DURATION));
        $response = $this->post(route('auth.reset-forgot-password'), [
            "email" => 'hamidrezanoruzinejad@gmail.com',
            "otp" => Otp::query()->first()->otp,
            "password" => "123456789!Q",
            "password_confirmation" => "123456789!Q"
        ],[
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ]);
        $this->assertEquals(422, $response->status());
    }


    /**
     * @test
     */
    public function user_reset_forget_password_green()
    {
        Mail::fake();
        $user = User::factory()->create([
            "email" => 'hamidrezanoruzinejad@gmail.com',
            "password" => 'password'
        ]);
        $user->email_verified_at = now();
        $user->save();
        $response = $this->post(route('auth.forgot-password'), [
            "email" => 'hamidrezanoruzinejad@gmail.com',
        ]);
        $response = $this->postJson(route('auth.reset-forgot-password'), [
            "email" => 'hamidrezanoruzinejad@gmail.com',
            "otp" => Otp::query()->first()->otp,
            "password" => "123456789!QaA",
            "password_confirmation" => "123456789!QaA"
        ]);
        $this->assertEquals(200, $response->status());
    }


    /**
     * @test
     */
    public function user_reset_forget_password_repetitious_fail()
    {
        Mail::fake();
        $user = User::factory()->create([
            "email" => 'hamidrezanoruzinejad@gmail.com',
            "password" => '123456789!Q55'
        ]);
        $user->email_verified_at = now();
        $user->save();
        $response = $this->postJson(route('auth.forgot-password'), [
            "email" => 'hamidrezanoruzinejad@gmail.com',
        ]);

        $response = $this->postJson(route('auth.reset-forgot-password'), [
            "email" => 'hamidrezanoruzinejad@gmail.com',
            "otp" => Otp::query()->first()->otp,
            "password" => "123456789!Q",
            "password_confirmation" => "123456789!Q"
        ]);

        Carbon::setTestNow(now()->addMinutes(2));
        $response = $this->postJson(route('auth.forgot-password'), [
            "email" => 'hamidrezanoruzinejad@gmail.com',
        ]);

        $response = $this->postJson(route('auth.reset-forgot-password'), [
            "email" => 'hamidrezanoruzinejad@gmail.com',
            "otp" => Otp::query()->latest()->first()->otp,
            "password" => "123456789!Q55",
            "password_confirmation" => "123456789!Q55"
        ]);

        $this->assertEquals(200, $response->status());
    }

    /**
     * @test
     */
    public function user_reset_forget_password_reset_more_than_one_time()
    {
        Mail::fake();
        $user = User::factory()->create([
            "email" => 'hamidrezanoruzinejad@gmail.com',
            "password" => 'password'
        ]);
        $user->email_verified_at = now();
        $user->save();
        $response = $this->postJson(route('auth.forgot-password'), [
            "email" => 'hamidrezanoruzinejad@gmail.com',
        ]);

        $response = $this->postJson(route('auth.reset-forgot-password'), [
            "email" => 'hamidrezanoruzinejad@gmail.com',
            "otp" => Otp::query()->first()->otp,
            "password" => "123456789!Q",
            "password_confirmation" => "123456789!Q"
        ]);
//        $this->assertEquals(200, $response->status());


        $response = $this->postJson(route('auth.reset-forgot-password'), [
            "email" => 'hamidrezanoruzinejad@gmail.com',
            "otp" => Otp::query()->first()->otp,
            "password" => "123456789!Q",
            "password_confirmation" => "123456789!Q"
        ]);
        $this->assertEquals(422, $response->status());
    }

    /**
     * @test
     */
    public function user_email_verify_green()
    {
        Mail::fake();
        $user = User::factory()->create([
            "email" => 'hamidrezanoruzinejad@gmail.com',
            "password" => 'password',
            "email_verified_at" => null
        ]);
        $response = $this->post(route('auth.ask-for-email-otp'), [
            "email" => 'hamidrezanoruzinejad@gmail.com',
        ]);
        Mail::assertSent(EmailVerifyOtp::class);
    }

    /**
     * @test
     */
    public function user_email_verify_failed()
    {
        Mail::fake();
        $user = User::factory()->create([
            "email" => 'hamidrezanoruzinejad@gmail.com',
            "password" => 'password'
        ]);
        $response = $this->post(route('auth.ask-for-email-otp'), [
            "email" => 'hamidrezanoruzinejad@gmail.com',
        ]);
        $response = $this->post(route('auth.ask-for-email-otp'), [
            "email" => 'hamidrezanoruzinejad@gmail.com',
        ]);
        $this->assertEquals(429, $response->status());
    }

    /**
     * @test
     */
    public function login_user_block_failed()
    {
        Mail::fake();

        $user = User::factory()->create([
            "email" => 'hamidrezanoruzinejad@gmail.com',
            "password" => 'password'
        ]);

        $user->email_verified_at = now();
        $user->save();

        list($intervals, $tries) = getLoginAttemptSetting();

        for ($i = 0; $i <= $tries[0] + 3; $i++) {
            $response = $this->post(route('auth.login'), [
                "email" => 'hamidrezanoruzinejad@gmail.com',
                "password" => 'incorrect password',
            ]);
        }
        Mail::assertSent(TooManyLoginAttemptTemporaryBlockedEmail::class);
        $this->assertEquals(trans('user.responses.max-attempts-exceeded'), $response->json()['message']);

        $user->refresh();
        $this->assertNotEquals(USER_BLOCK_TYPE_AUTOMATIC, $user->block_type);
        $this->assertEquals(null, $user->block_type);
    }

    /**
     * @test
     */
    public function ask_for_email_otp_green()
    {
        Mail::fake();
        $user = User::factory()->create([
            "email" => 'hamidrezanoruzinejad@gmail.com',
            "password" => 'password'
        ]);
        $response = $this->post(route('auth.ask-for-email-otp'), [
            "email" => 'hamidrezanoruzinejad@gmail.com',
        ]);
        $response->assertOk();
        Mail::assertSent(EmailVerifyOtp::class);
    }

}
