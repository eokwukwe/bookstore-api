<?php

namespace Tests\Feature;

use App\Author;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class AuthorsTest extends TestCase
{

    use DatabaseMigrations;

    /**
     * @test
     */
    public function it_returns_an_author_as_a_resource_object()
    {
        $author = factory(Author::class)->create();
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->getJson('/api/v1/authors/1', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    "id" => '1',
                    "type" => "authors",
                    "attributes" => [
                        'first_name' => $author->first_name,
                        'last_name' => $author->last_name,
                        'other_name' => $author->other_name,
                        'created_at' => $author->created_at->toJSON(),
                        'updated_at' => $author->updated_at->toJSON(),
                    ]
                ]
            ]);
    }

    /**
     * @test
     */
    public function
    it_returns_all_authors_as_a_collection_of_resource_objects()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $authors = factory(Author::class, 3)->create();

        $this->getJson('/api/v1/authors', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    [
                        "id" => '1',
                        "type" => "authors",
                        "attributes" => [
                            'first_name' => $authors[0]->first_name,
                            'last_name' => $authors[0]->last_name,
                            'other_name' => $authors[0]->other_name,
                            'created_at' => $authors[0]->created_at->toJSON(),
                            'updated_at' => $authors[0]->updated_at->toJSON(),
                        ]
                    ],
                    [
                        "id" => '2',
                        "type" => "authors",
                        "attributes" => [
                            'first_name' => $authors[1]->first_name,
                            'last_name' => $authors[1]->last_name,
                            'other_name' => $authors[1]->other_name,
                            'created_at' => $authors[1]->created_at->toJSON(),
                            'updated_at' => $authors[1]->updated_at->toJSON(),
                        ]
                    ],
                    [
                        "id" => '3',
                        "type" => "authors",
                        "attributes" => [
                            'first_name' => $authors[2]->first_name,
                            'last_name' => $authors[2]->last_name,
                            'other_name' => $authors[2]->other_name,
                            'created_at' => $authors[2]->created_at->toJSON(),
                            'updated_at' => $authors[2]->updated_at->toJSON(),
                        ]
                    ],
                ]
            ]);
    }

    /**
     * @test
     */
    public function it_can_create_an_author_from_a_resource_object()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->postJson(
            '/api/v1/authors',
            [
                'data' => [
                    'type' => 'authors',
                    'attributes' => [
                        'first_name' => 'John',
                        'last_name' => 'Doe',
                        'other_name' => 'Jonny',
                    ]
                ]
            ],
            [
                'accept' => 'application/vnd.api+json',
                'content-type' => 'application/vnd.api+json',
            ]
        )
            ->assertStatus(201)
            ->assertJson([
                "data" => [
                    "id" => '1',
                    "type" => "authors",
                    "attributes" => [
                        'first_name' => 'John',
                        'last_name' => 'Doe',
                        'other_name' => 'Jonny',
                        'created_at' => now()->setMillisecond(0)->toJSON(),
                        'updated_at' => now()->setMillisecond(0)->toJSON(),
                    ]
                ]
            ])
            ->assertHeader('Location', url('/api/v1/authors/1'));

        $this->assertDatabaseHas('authors', [
            'id' => 1,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'other_name' => 'Jonny',
        ]);
    }

    /**
     * @test
     */
    public function
    it_validates_that_the_type_member_is_given_when_creating_an_author()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $this->postJson('/api/v1/authors', [
            'data' => [
                'type' => '',
                'attributes' => [
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'other_name' => 'Jonny',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
                        'details' => 'The data.type field is required.',
                        'source' => [
                            'pointer' => '/data/type',
                        ]
                    ]
                ]
            ]);

        $this->assertDatabaseMissing('authors', [
            'id' => 1,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'other_name' => 'Jonny',
        ]);
    }

    /**
     * @test
     */
    public function
    it_validates_that_the_type_member_has_the_value_of_authors_when_creating_an_author()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->postJson('/api/v1/authors', [
            'data' => [
                'type' => 'author',
                'attributes' => [
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'other_name' => 'Jonny',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
                        'details' => 'The selected data.type is invalid.',
                        'source' => [
                            'pointer' => '/data/type',
                        ]
                    ]
                ]
            ]);

        $this->assertDatabaseMissing('authors', [
            'id' => 1,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'other_name' => 'Jonny',
        ]);
    }

    /**
     * @test
     */
    public function
    it_validates_that_the_attributes_members_has_been_given_when_creating_an_author()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->postJson('/api/v1/authors', [
            'data' => [
                'type' => 'authors',
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
                        'details' => 'The data.attributes field is required.',
                        'source' => [
                            'pointer' => '/data/attributes',
                        ]
                    ]
                ]
            ]);

        $this->assertDatabaseMissing('authors', [
            'id' => 1,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'other_name' => 'Jonny',
        ]);
    }

    /**
     * @test
     */
    public function
    it_validates_that_the_attributes_member_is_an_object_given_when_creating_an_author()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->postJson('/api/v1/authors', [
            'data' => [
                'type' => 'authors',
                'attributes' => 'not an object',
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
                        'details' => 'The data.attributes must be an array.',
                        'source' => [
                            'pointer' => '/data/attributes',
                        ]
                    ]
                ]
            ]);

        $this->assertDatabaseMissing('authors', [
            'id' => 1,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'other_name' => 'Jonny',
        ]);
    }

    /**
     * @test
     */
    public function
    it_validates_that_a_first_name_attribute_is_given_when_creating_an_author()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->postJson('/api/v1/authors', [
            'data' => [
                'type' => 'authors',
                'attributes' => [
                    'first_name' => '',
                    'last_name' => 'Doe',
                    'other_name' => 'Jonny',
                ],
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
                        'details' => 'The data.attributes.first name field is required.',
                        'source' => [
                            'pointer' => '/data/attributes/first_name',
                        ]
                    ]
                ]
            ]);

        $this->assertDatabaseMissing('authors', [
            'id' => 1,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'other_name' => 'Jonny',
        ]);
    }

    /**
     * @test
     */
    public function
    it_validates_that_a_first_name_attribute_is_a_string_when_creating_an_author()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->postJson('/api/v1/authors', [
            'data' => [
                'type' => 'authors',
                'attributes' => [
                    'first_name' => 44,
                    'last_name' => 'Doe',
                    'other_name' => 'Jonny',
                ],
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
                        'details' => 'The data.attributes.first name must be a string.',
                        'source' => [
                            'pointer' => '/data/attributes/first_name',
                        ]
                    ]
                ]
            ]);

        $this->assertDatabaseMissing('authors', [
            'id' => 1,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'other_name' => 'Jonny',
        ]);
    }

    /**
     * @test
     */
    public function
    it_validates_that_a_last_name_attribute_is_given_when_creating_an_author()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->postJson('/api/v1/authors', [
            'data' => [
                'type' => 'authors',
                'attributes' => [
                    'first_name' => 'John',
                    'last_name' => '',
                    'other_name' => 'Jonny',
                ],
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
                        'details' => 'The data.attributes.last name field is required.',
                        'source' => [
                            'pointer' => '/data/attributes/last_name',
                        ]
                    ]
                ]
            ]);

        $this->assertDatabaseMissing('authors', [
            'id' => 1,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'other_name' => 'Jonny',
        ]);
    }

    /**
     * @test
     */
    public function
    it_validates_that_a_other_name_attribute_is_given_when_creating_an_author()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $this->postJson('/api/v1/authors', [
            'data' => [
                'type' => 'authors',
                'attributes' => [
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'other_name' => '',
                ],
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
                        'details' => 'The data.attributes.other name must be a string.',
                        'source' => [
                            'pointer' => '/data/attributes/other_name',
                        ]
                    ]
                ]
            ]);

        $this->assertDatabaseMissing('authors', [
            'id' => 1,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'other_name' => 'Jonny',
        ]);
    }

    /**
     * @test
     */
    public function it_can_update_an_author_from_a_resource_object()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $author = factory(Author::class)->create();

        $creationTimestamp = now();
        sleep(1);

        $this->patchJson('/api/v1/authors/1', [
            'data' => [
                'id' => '1',
                'type' => 'authors',
                'attributes' => [
                    'first_name' => 'Jane',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => '1',
                    'type' => 'authors',
                    'attributes' => [
                        'first_name' => 'Jane',
                        'last_name' => $author->last_name,
                        'other_name' => $author->other_name,
                        'created_at' => $creationTimestamp->setMillisecond(0)->toJSON(),
                        'updated_at' => now()->setMillisecond(0)->toJSON(),
                    ],
                ]
            ]);

        $this->assertDatabaseHas('authors', [
            'id' => 1,
            'first_name' => 'Jane',
            'last_name' => $author->last_name,
            'other_name' => $author->other_name,
        ]);
    }

    /**
     * @test
     */
    public function
    it_validates_that_an_id_member_is_given_when_updating_an_author()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $author = factory(Author::class)->create();

        $this->patchJson('/api/v1/authors/1', [
            'data' => [
                'type' => 'authors',
                'attributes' => [
                    'first_name' => 'Jane',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
                        'details' => 'The data.id field is required.',
                        'source' => [
                            'pointer' => '/data/id',
                        ]
                    ]
                ]
            ]);

        $this->assertDatabaseHas('authors', [
            'id' => 1,
            'first_name' => $author->first_name,
        ]);
    }

    /**
     * @test
     */
    public function
    it_validates_that_an_id_member_is_a_string_when_updating_an_author()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $author = factory(Author::class)->create();
        $this->patchJson('/api/v1/authors/1', [
            'data' => [
                'id' => 1,
                'type' => 'authors',
                'attributes' => [
                    'first_name' => 'Jane',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
                        'details' => 'The data.id must be a string.',
                        'source' => [
                            'pointer' => '/data/id',
                        ]
                    ]
                ]
            ]);
        $this->assertDatabaseHas('authors', [
            'id' => 1,
            'first_name' => $author->first_name,
        ]);
    }

    /**
     * @test
     */
    public function
    it_validates_that_the_type_member_is_given_when_updating_an_author()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $author = factory(Author::class)->create();

        $this->patchJson('/api/v1/authors/1', [
            'data' => [
                'id' => '1',
                'type' => '',
                'attributes' => [
                    'first_name' => 'Jane',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
                        'details' => 'The data.type field is required.',
                        'source' => [
                            'pointer' => '/data/type',
                        ]
                    ]
                ]
            ]);

        $this->assertDatabaseHas('authors', [
            'id' => 1,
            'first_name' => $author->first_name,
        ]);
    }

    /**
     * @test
     */
    public function
    it_validates_that_the_type_member_has_the_value_of_authors_when_updating_an_author()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $author = factory(Author::class)->create();

        $this->patchJson('/api/v1/authors/1', [
            'data' => [
                'id' => '1',
                'type' => 'author',
                'attributes' => [
                    'first_name' => 'Jane',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
                        'details' => 'The selected data.type is invalid.',
                        'source' => [
                            'pointer' => '/data/type',
                        ]
                    ]
                ]
            ]);

        $this->assertDatabaseHas('authors', [
            'id' => 1,
            'first_name' => $author->first_name,
        ]);
    }

    /**
     * @test
     */
    public function
    it_validates_that_the_attributes_member_has_been_given_when_updating_an_author()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $author = factory(Author::class)->create();

        $this->patchJson('/api/v1/authors/1', [
            'data' => [
                'id' => '1',
                'type' => 'authors',
                'attributes' => ''
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
                        'details' => 'The data.attributes field is required.',
                        'source' => [
                            'pointer' => '/data/attributes',
                        ]
                    ]
                ]
            ]);

        $this->assertDatabaseHas('authors', [
            'id' => 1,
            'first_name' => $author->first_name,
        ]);
    }

    /**
     * @test
     */
    public function
    it_validates_that_the_attributes_member_is_an_object_given_when_updating_an_author()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $author = factory(Author::class)->create();

        $this->patchJson('/api/v1/authors/1', [
            'data' => [
                'id' => '1',
                'type' => 'authors',
                'attributes' => 'not an object',
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
                        'details' => 'The data.attributes must be an array.',
                        'source' => [
                            'pointer' => '/data/attributes',
                        ]
                    ]
                ]
            ]);

        $this->assertDatabaseHas('authors', [
            'id' => 1,
            'first_name' => $author->first_name,
        ]);
    }

    /**
     * @test
     */
    public function
    it_validates_that_a_first_name_attribute_is_a_string_when_updating_an_author()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $author = factory(Author::class)->create();

        $this->patchJson('/api/v1/authors/1', [
            'data' => [
                'id' => '1',
                'type' => 'authors',
                'attributes' => [
                    'first_name' => 47,
                ],
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
                        'details' => 'The data.attributes.first name must be a string.',
                        'source' => [
                            'pointer' => '/data/attributes/first_name',
                        ]
                    ]
                ]
            ]);

        $this->assertDatabaseHas('authors', [
            'id' => 1,
            'first_name' => $author->first_name,
        ]);
    }

    /**
     * @test
     */
    public function
    it_validates_that_a_last_name_attribute_is_a_string_when_updating_an_author()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $author = factory(Author::class)->create();

        $this->patchJson('/api/v1/authors/1', [
            'data' => [
                'id' => '1',
                'type' => 'authors',
                'attributes' => [
                    'last_name' => 47,
                ],
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
                        'details' => 'The data.attributes.last name must be a string.',
                        'source' => [
                            'pointer' => '/data/attributes/last_name',
                        ]
                    ]
                ]
            ]);

        $this->assertDatabaseHas('authors', [
            'id' => 1,
            'last_name' => $author->last_name,
        ]);
    }

    /**
     * @test
     */
    public function
    it_validates_that_a_other_name_attribute_is_a_string_when_updating_an_author()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $author = factory(Author::class)->create();

        $this->patchJson(
            '/api/v1/authors/1',
            [
                'data' => [
                    'id' => '1',
                    'type' => 'authors',
                    'attributes' => [
                        'other_name' => 47,
                    ],
                ]
            ],
            [
                'accept' => 'application/vnd.api+json',
                'content-type' => 'application/vnd.api+json',
            ]
        )
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
                        'details' => 'The data.attributes.other name must be a string.',
                        'source' => [
                            'pointer' => '/data/attributes/other_name',
                        ]
                    ]
                ]
            ]);

        $this->assertDatabaseHas('authors', [
            'id' => 1,
            'other_name' => $author->other_name,
        ]);
    }

    /**
     * @test
     */
    public function it_can_delete_an_author_through_a_delete_request()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $author = factory(Author::class)->create();

        $this->deleteJson('/api/v1/authors/1', [], [
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json',
        ])->assertStatus(204);

        $this->assertDatabaseMissing('authors', [
            'id' => 1,
            'first_name' => $author->first_name,
            'last_name' => $author->last_name,
            'other_name' => $author->other_name,
        ]);
    }

    /**
     * @test
     * @watch
     */
    // public function
    // it_can_sort_authors_by_name_through_a_sort_query_parameter()
    // {

    //     $user = factory(User::class)->create();
    //     Passport::actingAs($user);

    //     $authors = collect([
    //         'Bertram',
    //         'Claus',
    //         'Anna',
    //     ])->map(function ($name) {
    //         return factory(Author::class)->create([
    //             'first_name' => $name,
    //         ]);
    //     });

    //     $this->get('/api/v1/authors?sort=first_name', [
    //         'accept' => 'application/vnd.api+json',
    //         'content-type' => 'application/vnd.api+json',
    //     ])->assertStatus(200)->assertJson([
    //         "data" => [
    //             [
    //                 "id" => '2',
    //                 "type" => "authors",
    //                 "attributes" => [
    //                     'first_name' => 'Anna',
    //                     'last_name' => $authors[2]->last_name,
    //                     'other_name' => $authors[2]->other_name,
    //                     'created_at' => $authors[2]->created_at->toJSON(),
    //                     'updated_at' => $authors[2]->updated_at->toJSON(),
    //                 ]
    //             ],
    //             [
    //                 "id" => '1',
    //                 "type" => "authors",
    //                 "attributes" => [
    //                     'name' => 'Bertram',
    //                     'last_name' => $authors[0]->last_name,
    //                     'other_name' => $authors[0]->other_name,
    //                     'created_at' => $authors[0]->created_at->toJSON(),
    //                     'updated_at' => $authors[0]->updated_at->toJSON(),
    //                 ]
    //             ],
    //             [
    //                 "id" => '2',
    //                 "type" => "authors",
    //                 "attributes" => [
    //                     'first_name' => 'Claus',
    //                     'last_name' => $authors[1]->last_name,
    //                     'other_name' => $authors[1]->other_name,
    //                     'created_at' => $authors[1]->created_at->toJSON(),
    //                     'updated_at' => $authors[1]->updated_at->toJSON(),
    //                 ]
    //             ],
    //         ]
    //     ]);
    // }

    /**
     * @test
     */
    public function
    it_can_paginate_authors_through_a_page_query_parameter()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $authors = factory(Author::class, 10)->create();

        $this->get('/api/v1/authors?page[size]=5&page[number]=1', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(200)->assertJson([
            "data" => [
                [
                    "id" => '1',
                    "type" => "authors",
                    "attributes" => [
                        'first_name' => $authors[0]->first_name,
                        'last_name' => $authors[0]->last_name,
                        'other_name' => $authors[0]->other_name,
                        'created_at' => $authors[0]->created_at->toJSON(),
                        'updated_at' => $authors[0]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '2',
                    "type" => "authors",
                    "attributes" => [
                        'first_name' => $authors[1]->first_name,
                        'last_name' => $authors[1]->last_name,
                        'other_name' => $authors[1]->other_name,
                        'created_at' => $authors[1]->created_at->toJSON(),
                        'updated_at' => $authors[1]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '3',
                    "type" => "authors",
                    "attributes" => [
                        'first_name' => $authors[2]->first_name,
                        'last_name' => $authors[2]->last_name,
                        'other_name' => $authors[2]->other_name,
                        'created_at' => $authors[2]->created_at->toJSON(),
                        'updated_at' => $authors[2]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '4',
                    "type" => "authors",
                    "attributes" => [
                        'first_name' => $authors[3]->first_name,
                        'last_name' => $authors[3]->last_name,
                        'other_name' => $authors[3]->other_name,
                        'created_at' => $authors[3]->created_at->toJSON(),
                        'updated_at' => $authors[3]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '5',
                    "type" => "authors",
                    "attributes" => [
                        'first_name' => $authors[4]->first_name,
                        'last_name' => $authors[4]->last_name,
                        'other_name' => $authors[4]->other_name,
                        'created_at' => $authors[4]->created_at->toJSON(),
                        'updated_at' => $authors[4]->updated_at->toJSON(),
                    ]
                ],
            ],
            'links' => [
                'first' => route('authors.index', [
                    'page[size]' => 5, 'page[number]' => 1
                ]),
                'last' => route('authors.index', [
                    'page[size]' => 5, 'page[number]' => 2
                ]),
                'prev' => null,
                'next' => route('authors.index', [
                    'page[size]' => 5, 'page[number]' => 2
                ]),
            ]
        ]);
    }
}
