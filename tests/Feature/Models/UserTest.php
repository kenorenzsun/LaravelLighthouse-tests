<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\Result;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_factory_works()
    {
        $model = User::factory()->create();

        $this->assertModelExists($model);
    }

    public function test_user_works()
    {
        $admin = User::firstOrCreate([
            'name' => 'System Administrator',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $this->assertModelExists($admin);
    }

    public function test_user_relation_with_results_works()
    {
        $category = Category::create([
            "name" => "Category #1",
            "description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book."
        ]);

        $quiz = Quiz::create([
            "name" => "Quiz #1",
            "description" => "Quiz #1 lorem ipsum dolor sit amet",
            "category_id" => $category->id
        ]);

        $user = User::create([
            'name' => 'System Administrator',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $result = Result::create(['user_id' => $user->id, 'quiz_id' => $quiz->id, 'score' => 0]);

        $this->assertModelExists($result);
        $this->assertModelExists($user->results()->first());
        $this->assertEquals($user->results()->first()->quiz_id, $quiz->id);
        $this->assertEquals($user->results()->first()->user_id, $user->id);
    }
}
