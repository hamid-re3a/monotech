<?php

namespace User\database\seeders;

use User\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

/**
 * Class AuthTableSeeder.
 */
class AuthTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (USER_ROLES as $role) {
            Role::query()->firstOrCreate(['name' => $role]);
        }
        if (User::query()->count() > 0)
            return;
        if (!User::query()->where('email', 'admin@yopmail.com')->exists()) {
            $admin = User::whereUsername('admin')->first();
            if (!$admin) {
                $admin = User::factory()->create([
                    'username' => 'admin',
                ]);
                $admin->password = 'PA$$W0RD';
                $admin->member_id = 1000;
                $admin->transaction_password = 'PA$$W0RD';
                $admin->first_name = 'Doctor';
                $admin->last_name = 'Johny';
                $admin->email_verified_at = now();
            }

            $admin->email = 'admin@yopmail.com';
            $admin->save();

            $admin->assignRole([USER_ROLE_SUPER_ADMIN,USER_ROLE_CLIENT]);
        }
        if (!in_array(app()->environment(), ['production'])) {

            if (!User::query()->where('email', 'companystaging@gmail.com')->exists()) {
                $global = User::whereUsername('johny')->first();
                if (!$global) {
                    $global = User::factory()->create([
                        'username' => 'johny',
                    ]);
                    $global->update([
                        'member_id' => '2000',
                        'email' => 'companystaging@gmail.com',
                        'password' => 'password',
                        'transaction_password' => 'password',
                        'first_name' => 'John',
                        'last_name' => 'Due',
                        'sponsor_id' => 1,
                        'username' => 'johny',
                        'email_verified_at' => now()
                    ]);
                }

                $global->assignRole([USER_ROLE_SUPER_ADMIN,USER_ROLE_CLIENT]);
            }
            if (!User::query()->where('email', 'customer@yopmail.com')->exists()) {
                $global = User::whereUsername('george')->first();
                if (!$global) {
                    $global = User::factory()->create([
                        'username' => 'george',
                    ]);
                    $global->update([
                        'member_id' => '3000',
                        'email' => 'customer@yopmail.com',

                        'password' => 'password',
                        'transaction_password' => 'password',
                        'first_name' => 'George',
                        'last_name' => 'W Father',
                        'sponsor_id' => 1,
                        'username' => 'george',
                        'email_verified_at' => now()
                    ]);
                }

                $global->assignRole([USER_ROLE_CLIENT]);
            }

            for ($i = 0; $i < 20; $i++)
                if (!User::query()->where('email', "companystaging$i@gmail.com")->exists()) {
                    $global = User::whereUsername("johny$i")->first();
                    if (!$global) {
                        $global = User::factory()->create([
                            'username' => "johny$i",
                        ]);
                        $global->update([
                            'member_id' => "2000$i",
                            "email" => "companystaging$i@gmail.com",
                            "password" => "password",
                            "transaction_password" => "password",
                            "first_name" => "John$i",
                            "last_name" => "Due",
                            "sponsor_id" => $i > 10 ? 2 : 1,
                            "username" => "johny$i",
                            'email_verified_at' => now()
                        ]);
                    }

                    $global->assignRole([USER_ROLE_CLIENT]);
                }
        }

    }
}
