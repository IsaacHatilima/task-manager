<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('Password1#'),
        ]);

        Profile::factory()->create(['user_id' => $user->id]);
        Todo::factory(30)->create(['user_id' => $user->id]);
    }
}
