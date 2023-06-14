<?php

namespace Tests\Api;

use App\Models\Category;
use App\Models\Quiz;
use Tests\ApiResourceTestCase;
use Tests\ProvidesUser;

class QuizTest extends ApiResourceTestCase
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

        $noDataResponse = $this->asProvidedUser()->graphQL(
            /** @lang GraphQL */
            '
            query ($name: String){
                quizzes(first: 10, name: $name) {
                  data {
                    id
                    name
                    category_id
                  }
                  paginatorInfo {
                    currentPage
                    lastPage
                  }
                }
              }
            ',
            [
                'name' => 'test'
            ]
        );

        $noDataResponse->assertJsonStructure(
            [
                'data' => [
                    'quizzes' => [
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
                'quizzes' => [
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
            query ($name: String){
                quizzes(first: 10, name: $name) {
                  data {
                    id
                    name
                    category_id
                  }
                  paginatorInfo {
                    currentPage
                    lastPage
                  }
                }
              }
            ',
            [
                'name' => $quiz->name
            ]
        );

        $response->assertOk();

        $response->assertJson([
            'data' => [
                'quizzes' => [
                    'data' => [
                        [
                            'id' => $quiz->id,
                            'name' => $quiz->name,
                            'category_id' => $category->id,
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
                    'quizzes' => [
                        'data' => [
                            [
                                'id',
                                'name',
                                'category_id'
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

        $response = $this->asProvidedUser()->graphQL(
            /** @lang GraphQL */
            '
            query ($id : ID!){
                quiz(id : $id) {
                    id
                    name
                    category {
                        name
                    }
                }
            }
            ',
            [
                'id' => $quiz->id
            ]
        );

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                'quiz' => [
                    'id',
                    'name',
                    'category' => [
                        'name'
                    ]
                ]
            ]
        ]);

        $response->assertJson([
            'data' => [
                'quiz' => [
                    'id' => $quiz->id,
                    'name' => $quiz->name,
                    'category' => [
                        'name' => $category->name
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

        $quiz = [
            "category_id" => $category->id,
            "name" => "Quiz #1",
            "description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book."
        ];

        $response = $this->asProvidedUser()->graphQL(
            /** @lang GraphQL */
            '
            mutation ($category_id: ID!, $name: String!, $description: String!){
                createQuiz(category_id: $category_id, name : $name, description: $description) {
                    name
                    description
                    category_id
                }
            }
            ',
            $quiz
        );

        $response->assertOk();

        $response->assertJson([
            'data' => [
                'createQuiz' => [
                    'name' => $quiz['name'],
                    'description' => $quiz['description'],
                    'category_id' => $category->id
                ],
            ],
        ]);

        $this->assertDatabaseHas('quizzes', $quiz);
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

        $updateQuiz = [
            "id" => $quiz->id,
            "name" => "Proverbs",
            "category_id" => $category->id,
            "description" => "This quiz is for the Proverbs"
        ];

        $response = $this->asProvidedUser()->graphQL(
            /** @lang GraphQL */
            '
            mutation ($id: ID!, $name: String!, $description: String!, $category_id: ID!){
                updateQuiz(id: $id, name : $name, description: $description, category_id: $category_id) {
                    id
                    name
                    description
                    category {
                        name
                    }
                }
            }
            ',
            $updateQuiz
        );

        $response->assertOk();

        $response->assertJson([
            'data' => [
                'updateQuiz' => [
                    'id' => $quiz->id,
                    'name' => $updateQuiz['name'],
                    'description' => $updateQuiz['description'],
                    'category' => [
                        'name' => $category->name
                    ]
                ],
            ],
        ]);

        $this->assertDatabaseHas('quizzes', $updateQuiz);
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

        $response = $this->asProvidedUser()->graphQL(
            /** @lang GraphQL */
            '
            mutation ($id: ID!){
                deleteQuiz(id: $id) {
                    name
                    description
                }
            }
            ',
            [
                "id" => $quiz->id
            ]
        );

        $response->assertOk();

        $response->assertJson([
            'data' => [
                'deleteQuiz' => [
                    'name' => $quiz->name,
                    'description' => $quiz->description,
                ],
            ],
        ]);

        $this->assertDatabaseMissing('quizzes', [
            "name" => "Quiz #1",
            "description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book."
        ]);
    }
}
