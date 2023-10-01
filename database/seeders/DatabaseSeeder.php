<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::factory(9)->create()->each(function ($user) {
            // @INFO: Hydrate UserInformation for this $user
            \App\Models\UserInformation::factory()->create([
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            // @INFO: Hydrate UserStore for this $user
            \App\Models\UserStore::factory()->create([
                'user_id' =>$user->id
            ]);
        });
    }
}
