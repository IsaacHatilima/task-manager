<?php

namespace Database\Factories;

use App\Models\Todo;
use App\Models\TodoAccess;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TodoAccessFactory extends Factory
{
    protected $model = TodoAccess::class;

    public function definition(): array
    {
        return [
            'is_owner' => $this->faker->boolean(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'user_id' => User::factory(),
            'todo_id' => Todo::factory(),
        ];
    }
}
