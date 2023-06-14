<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use App\Models\Quiz;
use App\Models\Result;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ResultTest extends TestCase
{
    use RefreshDatabase;

    public function test_model_works()
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
    }

    public function test_result_relation_with_quiz_works()
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

        $this->assertModelExists($quiz);
        $this->assertModelExists($result->quiz);
        $this->assertEquals($result->quiz->name, $quiz->name);
        $this->assertEquals($result->quiz->id, $quiz->id);
    }

    public function test_result_relation_with_user_works()
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

        $this->assertModelExists($user);
        $this->assertModelExists($result->user);
        $this->assertEquals($result->user->name, $user->name);
        $this->assertEquals($result->user->id, $user->id);
    }
}
