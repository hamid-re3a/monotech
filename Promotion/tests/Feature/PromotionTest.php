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
    public function assign_promotion_code()
    {
        $user = User::factory()->create();
        $this->be($user);
        PromotionCode::query()->create([
            'start_date' => now(),
            'end_date' => now(),
            'amount' => 500,
            'quota' => 1,
            'code' => 'simple_code'
        ]);

        $response = $this->post(route('promotion.assign'), [
            'code' => 'simple_code',
        ]);
        $response->assertOk();
        $response->assertJsonStructure([
            "status",
            "message",
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
}
