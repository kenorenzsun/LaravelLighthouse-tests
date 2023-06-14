<?php

namespace Tests\Api;

use App\Models\Category;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Answer;
use Tests\ApiResourceTestCase;
use Tests\ProvidesUser;

class AnswerTest extends ApiResourceTestCase
{
    use ProvidesUser;

    public function test_index_works()
    {
        $category = Category::create([
            "name" => "Category #1",
            "description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book."
        ]);

        $quiz = Quiz::create([
            "name" => "Quiz #1",
            "description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.",
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

        $response = $this->asProvidedUser()->graphQL(
            /** @lang GraphQL */
            '
            query {
                answers {
                    id
                    answer
                    question_id
                    is_correct
                }
              }
            ',
        );

        $response->assertOk();

        $response->assertJsonStructure(
            [
                'data' => [
                    'answers' => [
                        [
                            'id',
                            'answer',
                            'question_id',
                            'is_correct'
                        ]
                    ]
                ]
            ]
        );

        $response->assertJson([
            'data' => [
                'answers' => [
                    [
                        'id' => $answer->id,
                        'answer' => $answer->answer,
                        'question_id' => $answer->question_id,
                        'is_correct' => $answer->is_correct
                    ]
                ],
            ],
        ]);
    }

    public function test_show_works()
    {
        $category = Category::create([
            "name" => "Category #1",
            "description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book."
        ]);

        $quiz = Quiz::create([
            "name" => "Quiz #1",
            "description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.",
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

        $response = $this->asProvidedUser()->graphQL(
            /** @lang GraphQL */
            '
            query ($id : ID!){
                answer(id : $id) {
                    id
                    answer
                    question {
                        question
                    }
                }
            }
            ',
            [
                'id' => $answer->id
            ]
        );

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                'answer' => [
                    'id',
                    'answer',
                    'question' => [
                        'question'
                    ]
                ]
            ]
        ]);

        $response->assertJson([
            'data' => [
                'answer' => [
                    'id' => $answer->id,
                    'answer' => $answer->answer,
                    'question' => [
                        'question' => $question->question
                    ]
                ],
            ],
        ]);
    }

    public function test_store_works()
    {
        $category = Category::create([
            "name" => "Category #1",
            "description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book."
        ]);

        $quiz = Quiz::create([
            "name" => "Quiz #1",
            "description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.",
            "category_id" => $category->id
        ]);

        $question = Question::create([
            'question' => "What is the name of the user?",
            'quiz_id' => $quiz->id
        ]);

        $answer = [
            "answer" => "Choice 1",
            "question_id" => $question->id
        ];

        $response = $this->asProvidedUser()->graphQL(
            /** @lang GraphQL */
            '
            mutation ($question_id: ID!, $answer: String!){
                createAnswer(question_id: $question_id, answer : $answer) {
                    answer
                    is_correct
                    question_id
                }
            }
            ',
            $answer
        );

        $response->assertOk();

        $response->assertJson([
            'data' => [
                'createAnswer' => [
                    'answer' => $answer['answer'],
                    'is_correct' => false,
                    'question_id' => $answer['question_id']
                ],
            ],
        ]);

        $this->assertDatabaseHas('answers', $answer);
    }

    public function test_update_works()
    {
        $category = Category::create([
            "name" => "Category #1",
            "description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book."
        ]);

        $quiz = Quiz::create([
            "name" => "Quiz #1",
            "description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.",
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

        $updateAnswer = [
            "question_id" => $question->id,
            "id" => $answer->id,
            "answer" => "Choocse2",
        ];

        $response = $this->asProvidedUser()->graphQL(
            /** @lang GraphQL */
            '
            mutation ($id: ID!, $answer: String! $question_id: ID!){
                updateAnswer(id: $id, answer : $answer, question_id: $question_id) {
                    id
                    answer
                    question {
                        question
                    }
                }
            }
            ',
            $updateAnswer
        );

        $response->assertOk();

        $response->assertJson([
            'data' => [
                'updateAnswer' => [
                    'id' => $answer->id,
                    'answer' => $updateAnswer['answer'],
                    'question' => [
                        'question' => $question->question
                    ]
                ],
            ],
        ]);

        $this->assertDatabaseHas('answers', $updateAnswer);
    }

    public function test_destroy_works()
    {
        $category = Category::create([
            "name" => "Category #1",
            "description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book."
        ]);

        $quiz = Quiz::create([
            "name" => "Quiz #1",
            "description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.",
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

        $response = $this->asProvidedUser()->graphQL(
            /** @lang GraphQL */
            '
            mutation ($id: ID!){
                deleteAnswer(id: $id) {
                    answer
                }
            }
            ',
            [
                "id" => $answer->id
            ]
        );

        $response->assertOk();

        $response->assertJson([
            'data' => [
                'deleteAnswer' => [
                    'answer' => $answer->answer,
                ],
            ],
        ]);

        $this->assertDatabaseMissing('answers', [
            "answer" => "Choice 1",
            "question_id" => $question->id
        ]);
    }
}
