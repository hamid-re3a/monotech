<?php


namespace Promotion\tests\Feature;


use Promotion\Models\PromotionCode;
use User\Models\User;

class PromotionTest extends \Promotion\tests\PromotionTest
{

    /**
     * @test
     */
    public function create_new_promotion_code()
    {
        $user = User::factory()->create();
        $user->assignRole(USER_ROLE_SUPER_ADMIN);
        $this->be($user);
        $response = $this->post(route('backoffice.create'), [
            'start_date' => now(),
            'end_date' => now(),
            'amount' => 500,
            'quota' => 5,
        ]);
        $response->assertStatus(201);
        $response->assertJsonStructure([
            "success",
            "data" => [
                "id",
                "code",
                "start_date",
                "end_date",
                "amount",
                "quota"
            ]]);
        $this->assertDatabaseHas('promotion_codes', ['amount' => 500]);
    }

    /**
     * @test
     */
    public function show_promotion_code()
    {
        $user = User::factory()->create();
        $user->assignRole(USER_ROLE_SUPER_ADMIN);
        $this->be($user);
        $promotion = PromotionCode::query()->create([
            'start_date' => now(),
            'end_date' => now(),
            'amount' => 500,
            'quota' => 1,
            'code' => 'simple_code'
        ]);
        $promotion->assignee()->attach($user->id);
        $user->deposit($promotion->amount);
        $response = $this->get(route('backoffice.show',$promotion));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            "success",
            "data"
        ]);
    }
    /**
     * @test
     */
    public function index_promotion_code()
    {
        $user = User::factory()->create();
        $user->assignRole(USER_ROLE_SUPER_ADMIN);
        $this->be($user);
        $promotion = PromotionCode::query()->create([
            'start_date' => now(),
            'end_date' => now(),
            'amount' => 500,
            'quota' => 1,
            'code' => 'simple_code'
        ]);
        $promotion->assignee()->attach($user->id);
        $user->deposit($promotion->amount);
        $response = $this->get(route('backoffice.index'));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            "success",
            "data"
        ]);
    }

    /**
     * @test
     */
    public function assign_promotion_code()
    {
        $user = User::factory()->create(['email_verified_at'=>now()]);
        $this->be($user);
        $promotion = PromotionCode::query()->create([
            'start_date' => now(),
            'end_date' => now(),
            'amount' => 500,
            'quota' => 1,
            'code' => 'simple_code'
        ]);
        $response = $this->post(route('customer.assign'), [
            'code' => 'simple_code',
        ]);
        $response->assertOk();
        $response->assertJsonStructure([
            "success",
        ]);
        $this->assertDatabaseHas('user_promotion_codes', ['promotion_code_id' => $promotion->id]);
        $this->assertEquals($promotion->amount,$user->balance);
    }



    /**
     * @test
     */
    public function assign_promotion_code_more_than_regular()
    {
        $user = User::factory()->create(['email_verified_at'=>now()]);
        $this->be($user);
        $promotion = PromotionCode::query()->create([
            'start_date' => now(),
            'end_date' => now(),
            'amount' => 500,
            'quota' => 1,
            'code' => 'simple_code'
        ]);
        $response = $this->post(route('customer.assign'), [
            'code' => 'simple_code',
        ]);
        $response->assertOk();
        $response->assertJsonStructure([
            "success",
        ]);

        $response = $this->post(route('customer.assign'), [
            'code' => 'simple_code',
        ]);
        $response->assertStatus(400);
    }
    /**
     * @test
     */
    public function same_user_can_not_get_promotion_code_more_than_once()
    {
        $user = User::factory()->create(['email_verified_at'=>now()]);
        $this->be($user);
        $promotion = PromotionCode::query()->create([
            'start_date' => now(),
            'end_date' => now(),
            'amount' => 500,
            'quota' => 2,
            'code' => 'simple_code'
        ]);
        $response = $this->post(route('customer.assign'), [
            'code' => 'simple_code',
        ]);
        $response->assertOk();
        $response->assertJsonStructure([
            "success",
        ]);

        $response = $this->post(route('customer.assign'), [
            'code' => 'simple_code',
        ]);
        $response->assertStatus(400);
    }
}
