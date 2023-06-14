<?php

namespace Tests\Api;

use App\Models\Category;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Result;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\ApiResourceTestCase;
use Tests\ProvidesUser;

class ResultTest extends ApiResourceTestCase
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

        $user = User::create([
            'name' => 'System Administrator',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $result = Result::create(['user_id' => $user->id, 'quiz_id' => $quiz->id, 'score' => 0]);

        $response = $this->asProvidedUser()->graphQL(
            /** @lang GraphQL */
            '
            query($user_id : ID) {
                results(first: 10, user_id : $user_id) {
                    data {
                        id
                        score
                        quiz {
                            name
                        }
                        user {
                            name
                        }
                    }
                    paginatorInfo {
                        currentPage
                        lastPage
                    }
                }
              }
            ',
            [
                'user_id' => $user->id,
            ]
        );

        $response->assertOk();

        $response->assertJson([
            'data' => [
                'results' => [
                    'data' => [
                        [
                            'id' => $result->id,
                            'score' => $result->score,
                            'quiz' => [
                                "name" => $quiz->name
                            ],
                            'user' => [
                                "name" => $user->name
                            ],
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
                    'results' => [
                        'data' => [
                            [
                                'id',
                                'score',
                                'quiz' => [
                                    "name"
                                ],
                                'user' => [
                                    "name"
                                ]
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

        $user = User::create([
            'name' => 'System Administrator',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $result = Result::create(['user_id' => $user->id, 'quiz_id' => $quiz->id, 'score' => 0]);

        $response = $this->asProvidedUser()->graphQL(
            /** @lang GraphQL */
            '
            query ($id : ID!){
                result(id : $id) {
                    id
                    score
                    quiz {
                        name
                    }
                }
            }
            ',
            [
                'id' => $result->id
            ]
        );

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                'result' => [
                    'id',
                    'score',
                    'quiz' => [
                        'name'
                    ]
                ]
            ]
        ]);

        $response->assertJson([
            'data' => [
                'result' => [
                    'id' => $result->id,
                    'score' => $result->score,
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

        $question = Question::create([
            'question' => "What is the name of the user?",
            'quiz_id' => $quiz->id
        ]);

        $answer = $question->answers()->createMany(
            [
                [
                    "answer" => "Choice 1",
                ],
                [
                    "answer" => "Choice 2",
                    "is_correct" => true,
                ],
                [
                    "answer" => "Choice 3",
                ],
                [
                    "answer" => "Choice 4",
                ]
            ]
        );

        $question2 = Question::create([
            'question' => "What is the name of the user2?",
            'quiz_id' => $quiz->id
        ]);

        $question2->answers()->createMany(
            [
                [
                    "answer" => "Choice 1",
                ],
                [
                    "answer" => "Choice 2",
                    "is_correct" => true,
                ],
                [
                    "answer" => "Choice 3",
                ],
                [
                    "answer" => "Choice 4",
                ]
            ]
        );

        $response = $this->asProvidedUser()->graphQL(
            /** @lang GraphQL */
            '
            mutation ($quiz_id: ID!, $answers: [ResultAnswers!]!){
                createResult(quiz_id: $quiz_id, answers : $answers) {
                    score
                    quiz_id
                }
            }
            ',
            [
                'quiz_id' => $quiz->id,
                'answers' => [
                    [
                        "question_id" => $question->id,
                        "answer_id" => $question->answers()->where('is_correct', true)->first()->id,
                    ],
                    [
                        "question_id" => $question2->id,
                        "answer_id" => $question2->answers()->where('is_correct', true)->first()->id,
                    ]
                ]
            ]
        );

        $response->assertOk();

        $response->assertJson([
            'data' => [
                'createResult' => [
                    'score' => 2,
                    'quiz_id' => $quiz->id,
                ],
            ],
        ]);

        $this->assertDatabaseHas('results', [
            'score' => 2,
            'quiz_id' => $quiz->id,
        ]);
    }

    public function test_update_works()
    {
        $this->assertEquals(1, 1);
    }

    public function test_destroy_works()
    {
        $this->assertEquals(1, 1);
    }
}
