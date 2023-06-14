<?php

namespace Tests\Api;

use App\Models\Category;
use App\Models\Quiz;
use App\Models\Question;
use Tests\ApiResourceTestCase;
use Tests\ProvidesUser;

class QuestionTest extends ApiResourceTestCase
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

        $noDataResponse = $this->asProvidedUser()->graphQL(
            /** @lang GraphQL */
            '
            query ($question: String){
                questions(first: 10, question: $question) {
                  data {
                    id
                    question
                    quiz_id
                  }
                  paginatorInfo {
                    currentPage
                    lastPage
                  }
                }
              }
            ',
            [
                'question' => 'test'
            ]
        );

        $noDataResponse->assertJsonStructure(
            [
                'data' => [
                    'questions' => [
                        'data',
                        'paginatorInfo' => [
                            'currentPage',
                            'lastPage',
                        ],

                    ]
                ]
            ]
        );

        $noDataResponse->assertJson([
            'data' => [
                'questions' => [
                    'data' => [],
                    'paginatorInfo' => [
                        'currentPage' => 1,
                        'lastPage' => 1,
                    ],
                ],
            ],
        ]);

        $response = $this->asProvidedUser()->graphQL(
            /** @lang GraphQL */
            '
            query ($question: String){
                questions(first: 10, question: $question) {
                  data {
                    id
                    question
                    quiz_id
                  }
                  paginatorInfo {
                    currentPage
                    lastPage
                  }
                }
              }
            ',
            [
                'question' => $question->question
            ]
        );

        $response->assertOk();

        $response->assertJson([
            'data' => [
                'questions' => [
                    'data' => [
                        [
                            'id' => $question->id,
                            'question' => $question->question,
                            'quiz_id' => $quiz->id,
                        ],
                    ],
                    'paginatorInfo' => [
                        'currentPage' => 1,
                        'lastPage' => 1,
                    ],
                ],
            ],
        ]);

        $response->assertJsonStructure(
            [
                'data' => [
                    'questions' => [
                        'data' => [
                            [
                                'id',
                                'question',
                                'quiz_id'
                            ],
                        ],
                        'paginatorInfo' => [
                            'currentPage',
                            'lastPage',
                        ],

                    ]
                ]
            ]
        );
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

        $response = $this->asProvidedUser()->graphQL(
            /** @lang GraphQL */
            '
            query ($id : ID!){
                question(id : $id) {
                    id
                    question
                    quiz {
                        name
                    }
                }
            }
            ',
            [
                'id' => $question->id
            ]
        );

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                'question' => [
                    'id',
                    'question',
                    'quiz' => [
                        'name'
                    ]
                ]
            ]
        ]);

        $response->assertJson([
            'data' => [
                'question' => [
                    'id' => $question->id,
                    'question' => $question->question,
                    'quiz' => [
                        'name' => $quiz->name
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

        $question = [
            'question' => "What is the name of the user?",
            'quiz_id' => $quiz->id
        ];

        $response = $this->asProvidedUser()->graphQL(
            /** @lang GraphQL */
            '
            mutation ($quiz_id: ID!, $question: String!){
                createQuestion(quiz_id: $quiz_id, question : $question) {
                    question
                    quiz_id
                }
            }
            ',
            $question
        );

        $response->assertOk();

        $response->assertJson([
            'data' => [
                'createQuestion' => [
                    'question' => $question['question'],
                    'quiz_id' => $quiz->id
                ],
            ],
        ]);

        $this->assertDatabaseHas('questions', $question);
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

        $updateQuestion = [
            "quiz_id" => $quiz->id,
            "id" => $question->id,
            "question" => "What is Me",
        ];

        $response = $this->asProvidedUser()->graphQL(
            /** @lang GraphQL */
            '
            mutation ($id: ID!, $question: String! $quiz_id: ID!){
                updateQuestion(id: $id, question : $question, quiz_id: $quiz_id) {
                    id
                    question
                    quiz {
                        name
                    }
                }
            }
            ',
            $updateQuestion
        );

        $response->assertOk();

        $response->assertJson([
            'data' => [
                'updateQuestion' => [
                    'id' => $question->id,
                    'question' => $updateQuestion['question'],
                    'quiz' => [
                        'name' => $quiz->name
                    ]
                ],
            ],
        ]);

        $this->assertDatabaseHas('questions', $updateQuestion);
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

        $response = $this->asProvidedUser()->graphQL(
            /** @lang GraphQL */
            '
            mutation ($id: ID!){
                deleteQuestion(id: $id) {
                    question
                }
            }
            ',
            [
                "id" => $question->id
            ]
        );

        $response->assertOk();

        $response->assertJson([
            'data' => [
                'deleteQuestion' => [
                    'question' => $question->question,
                ],
            ],
        ]);

        $this->assertDatabaseMissing('questions', [
            'question' => "What is the name of the user?",
            'quiz_id' => $quiz->id
        ]);
    }
}
