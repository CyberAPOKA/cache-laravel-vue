<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $faker = Faker::create();

        User::factory()->create([
            'name' => 'Christian André Steffens',
            'email' => 'oficialsteffens@hotmail.com',
        ]);

        User::factory(100)->create();

        $userIds = DB::table('users')->pluck('id')->toArray();

        for ($i = 0; $i < 50000; $i++) {
            Post::create([
                'user_id' =>  $faker->randomElement($userIds),
                'title' => $faker->name,
                'description' => $faker->text,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
