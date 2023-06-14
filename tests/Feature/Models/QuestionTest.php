<?php

namespace Tests\Feature\Models;

use App\Models\Answer;
use App\Models\Category;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class QuestionTest extends TestCase
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

        $question = Question::create([
            'question' => "What is the name of the user?",
            'quiz_id' => $quiz->id
        ]);

        $this->assertModelExists($question);
    }

    public function test_question_relation_with_quiz_works()
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

        $this->assertModelExists($quiz);
        $this->assertModelExists($question->quiz);
        $this->assertEquals($question->quiz->name, $quiz->name);
        $this->assertEquals($question->quiz->id, $quiz->id);
    }

    public function test_question_relation_with_answers_works()
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

        $answer = Answer::create([
            "answer" => "Choice 1",
            "question_id" => $question->id
        ]);

        $this->assertModelExists($answer);
        $this->assertModelExists($question->answers()->first());
        $this->assertEquals($question->answers()->first()->answer, $answer->answer);
        $this->assertEquals($question->answers()->first()->id, $answer->id);
    }
}
