<?php

namespace Database\Factories;

use App\Enums\TodoStatusEnum;
use App\Models\Todo;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Str;

class TodoFactory extends Factory
{
    protected $model = Todo::class;

    public function definition(): array
    {
        return [
            'title' => Str::title($this->faker->word()),
            'description' => $this->faker->text(maxNbChars: 150),
            'status' => $this->faker->randomElement(TodoStatusEnum::getValues()),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
