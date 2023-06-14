<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use App\Models\Quiz;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_model_works()
    {
        $category = Category::create([
            "name" => "Category #1",
            "description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book."
        ]);

        $this->assertModelExists($category);
    }

    public function test_category_relation_with_quiz_works()
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
        $this->assertModelExists($category->quizzes()->first());
        $this->assertEquals($category->quizzes()->first()->name, $quiz->name);
        $this->assertEquals($category->quizzes()->first()->id, $quiz->id);
    }
}
