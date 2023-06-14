<?php

namespace Tests\Api;

use App\Models\Category;
use Tests\ApiResourceTestCase;
use Tests\ProvidesUser;

class CategoryTest extends ApiResourceTestCase
{
    use ProvidesUser;

    public function test_index_works()
    {
        $category = Category::create([
            "name" => "Category #1",
            "description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book."
        ]);

        $noDataResponse = $this->asProvidedUser()->graphQL(
            /** @lang GraphQL */
            '
            query ($name: String){
                categories(first: 10, name: $name) {
                  data {
                    id
                    name
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
                    'categories' => [
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
                'categories' => [
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
                categories(first: 10, name: $name) {
                  data {
                    id
                    name
                  }
                  paginatorInfo {
                    currentPage
                    lastPage
                  }
                }
              }
            ',
            [
                'name' => $category->name
            ]
        );

        $response->assertOk();

        $response->assertJson([
            'data' => [
                'categories' => [
                    'data' => [
                        [
                            'id' => $category->id,
                            'name' => $category->name,
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
                    'categories' => [
                        'data' => [
                            [
                                'id',
                                'name',
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

        $response = $this->asProvidedUser()->graphQL(
            /** @lang GraphQL */
            '
            query ($id : ID!){
                category(id : $id) {
                    id
                    name
                }
            }
            ',
            [
                'id' => $category->id
            ]
        );

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                'category' => [
                    'id',
                    'name',
                ]
            ]
        ]);

        $response->assertJson([
            'data' => [
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                ],
            ],
        ]);
    }

    public function test_store_works()
    {
        $category = [
            "name" => "Category #1",
            "description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book."
        ];

        $response = $this->asProvidedUser()->graphQL(
            /** @lang GraphQL */
            '
            mutation ($name: String!, $description: String!){
                createCategory(name : $name, description: $description) {
                    name
                    description
                }
            }
            ',
            $category
        );

        $response->assertOk();

        $response->assertJson([
            'data' => [
                'createCategory' => [
                    'name' => $category['name'],
                    'description' => $category['description'],
                ],
            ],
        ]);

        $this->assertDatabaseHas('categories', $category);
    }

    public function test_update_works()
    {
        $category = Category::create([
            "name" => "Category #1",
            "description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book."
        ]);

        $updateCategory = [
            "id" => $category->id,
            "name" => "English",
            "description" => "This category is for the English Language"
        ];

        $response = $this->asProvidedUser()->graphQL(
            /** @lang GraphQL */
            '
            mutation ($id: ID!, $name: String!, $description: String!){
                updateCategory(id: $id, name : $name, description: $description) {
                    id
                    name
                    description
                }
            }
            ',
            $updateCategory
        );

        $response->assertOk();

        $response->assertJson([
            'data' => [
                'updateCategory' => [
                    'id' => $category->id,
                    'name' => $updateCategory['name'],
                    'description' => $updateCategory['description'],
                ],
            ],
        ]);

        $this->assertDatabaseHas('categories', $updateCategory);
    }

    public function test_destroy_works()
    {
        $category = Category::create([
            "name" => "Category #1",
            "description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book."
        ]);

        $response = $this->asProvidedUser()->graphQL(
            /** @lang GraphQL */
            '
            mutation ($id: ID!){
                deleteCategory(id: $id) {
                    name
                    description
                }
            }
            ',
            [
                "id" => $category->id
            ]
        );

        $response->assertOk();

        $response->assertJson([
            'data' => [
                'deleteCategory' => [
                    'name' => $category->name,
                    'description' => $category->description,
                ],
            ],
        ]);

        $this->assertDatabaseMissing('categories', [
            "name" => "Category #1",
            "description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book."
        ]);
    }
}
