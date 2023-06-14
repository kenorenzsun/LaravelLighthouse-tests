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

class QuizTest extends TestCase
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

        $this->assertModelExists($quiz);
    }

    public function test_quiz_relation_with_category_works()
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

        $this->assertModelExists($category);
        $this->assertModelExists($quiz->category);
        $this->assertEquals($quiz->category->name, $category->name);
        $this->assertEquals($quiz->category->id, $category->id);
    }

    public function test_quiz_relation_with_questions_works()
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

        $question = Question::create([
            'question' => "What is the name of the user?",
            'quiz_id' => $quiz->id
        ]);

        $this->assertModelExists($question);
        $this->assertModelExists($quiz->questions->first());
        $this->assertEquals($quiz->questions->first()->question, $question->question);
        $this->assertEquals($quiz->questions->first()->id, $question->id);
    }

    public function test_quiz_relation_with_results_works()
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
        $this->assertModelExists($quiz->results()->first());
        $this->assertEquals($quiz->results()->first()->quiz_id, $result->quiz_id);
        $this->assertEquals($quiz->results()->first()->id, $result->id);
    }
}
