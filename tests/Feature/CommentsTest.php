<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Comment;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\DatabaseMigrations;


class CommentsTest extends TestCase
{
  use DatabaseMigrations;

  /**
   * @test
   */
  public function it_returns_a_comment_as_a_resource_object()
  {
    $user = factory(User::class)->create();
    $comment = factory(Comment::class)->create();

    Passport::actingAs($user);

    $this->getJson("/api/v1/comments/{$comment->id}", [
      'accept' => 'application/vnd.api+json',
      'content-type' => 'application/vnd.api+json',
    ])->assertStatus(200)
      ->assertJson([
        "data" => [
          "id" => $comment->id,
          "type" => "comments",
          "attributes" => [
            'message' => $comment->message,
            'created_at' => $comment->created_at->toJSON(),
            'updated_at' => $comment->updated_at->toJSON(),
          ]
        ]
      ]);
  }

  /**
   * @test
   */
  public function
  it_returns_all_comments_as_a_collection_of_resource_objects()
  {
    $user = factory(User::class)->create();
    $comments = factory(Comment::class, 3)->create();

    Passport::actingAs($user);

    $this->getJson("/api/v1/comments", [
      'accept' => 'application/vnd.api+json',
      'content-type' => 'application/vnd.api+json',
    ])->assertStatus(200)
      ->assertJson([
        "data" => [
          [
            "id" => $comments[0]->id,
            "type" => "comments",
            "attributes" => [
              'message' => $comments[0]->message,
              'created_at' => $comments[0]->created_at->toJSON(),
              'updated_at' => $comments[0]->updated_at->toJSON(),
            ]
          ],
          [
            "id" => $comments[1]->id,
            "type" => "comments",
            "attributes" => [
              'message' => $comments[1]->message,
              'created_at' => $comments[1]->created_at->toJSON(),
              'updated_at' => $comments[1]->updated_at->toJSON(),
            ]
          ],
          [
            "id" => $comments[2]->id,
            "type" => "comments",
            "attributes" => [
              'message' => $comments[2]->message,
              'created_at' => $comments[2]->created_at->toJSON(),
              'updated_at' => $comments[2]->updated_at->toJSON(),
            ]
          ],
        ]
      ]);
  }

  /**
   * @test
   */
  public function it_can_create_an_comment_from_a_resource_object()
  {
    $user = factory(User::class)->create();
    Passport::actingAs($user);

    $this->postJson('/api/v1/comments', [
      'data' => [
        'type' => 'comments',
        'attributes' => [
          'message' => 'This is a comment that comments a comment',
        ]
      ]
    ], [
      'accept' => 'application/vnd.api+json',
      'content-type' => 'application/vnd.api+json',
    ])->assertStatus(201)
      ->assertJson([
        "data" => [
          "type" => "comments",
          "attributes" => [
            'message' => 'This is a comment that comments a comment',
            'created_at' => now()->setMilliseconds(0)->toJSON(),
            'updated_at' => now()->setMilliseconds(0)->toJSON(),
          ]
        ]
      ]);

    $this->assertDatabaseHas('comments', [
      'message' => 'This is a comment that comments a comment',
    ]);
  }

  /**
   * @test
   */
  public function
  it_validates_that_the_type_member_is_given_when_creating_a_comment()
  {
    $user = factory(User::class)->create();
    Passport::actingAs($user);

    $this->postJson('/api/v1/comments', [
      'data' => [
        'type' => '',
        'attributes' => [
          'message' => 'This is a comment'
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
  }

  /**
   * @test
   */
  public function
  it_validates_that_the_type_member_has_the_value_of_comments_when_creating_a_comment()
  {
    $user = factory(User::class)->create();
    Passport::actingAs($user);

    $this->postJson('/api/v1/comments', [
      'data' => [
        'type' => 'author',
        'attributes' => [
          'message' => 'This is a comment'
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
  }

  /**
   * @test
   */
  public function
  it_validates_that_the_attributes_members_has_been_given_when_creating_a_comment()
  {
    $user = factory(User::class)->create();
    Passport::actingAs($user);

    $this->postJson('/api/v1/comments', [
      'data' => [
        'type' => 'comments',
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
  }

  /**
   * @test
   */
  public function
  it_validates_that_the_attributes_member_is_an_object_given_when_creating_a_comment()
  {
    $user = factory(User::class)->create();
    Passport::actingAs($user);

    $this->postJson('/api/v1/comments', [
      'data' => [
        'type' => 'comments',
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
  }

  /**
   * @test
   */
  public function
  it_validates_that_a_message_attribute_is_given_when_creating_a_comment()
  {
    $user = factory(User::class)->create();
    Passport::actingAs($user);

    $this->postJson('/api/v1/comments', [
      'data' => [
        'type' => 'comments',
        'attributes' => [
          'message' => ''
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
            'title'   => 'Validation Error',
            'details' => 'The data.attributes.message field is required.',
            'source'  => [
              'pointer' => '/data/attributes/message',
            ]
          ]
        ]
      ]);
  }

  /**
   * @test
   */
  public function it_validates_that_a_message_attribute_is_a_string_when_creating_a_comment()
  {
    $user = factory(User::class)->create();
    Passport::actingAs($user);

    $this->postJson('/api/v1/comments', [
      'data' => [
        'type' => 'comments',
        'attributes' => [
          'message' => 42,
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
            'title'   => 'Validation Error',
            'details' => 'The data.attributes.message must be a string.',
            'source'  => [
              'pointer' => '/data/attributes/message',
            ]
          ]
        ]
      ]);
  }

  /**
   * @test
   */
  public function it_can_update_a_comment_from_a_resource_object()
  {
    $user = factory(User::class)->create();
    Passport::actingAs($user);

    $comment = factory(Comment::class)->create();

    $this->patchJson("/api/v1/comments/1", [
      'data' => [
        'id' => '1',
        'type' => 'comments',
        'attributes' => [
          'message' => 'This is an updated comment',
        ]
      ]
    ], [
      'accept' => 'application/vnd.api+json',
      'content-type' => 'application/vnd.api+json',
    ])
      ->assertStatus(200)
      ->assertJson([
        'data' => [
          'id' => $comment->id,
          'type' => 'comments',
          'attributes' => [
            'message' => 'This is an updated comment',
            'created_at' => now()->setMillisecond(0)->toJSON(),
            'updated_at' => now()->setMillisecond(0)->toJSON(),
          ],
        ]
      ]);

    $this->assertDatabaseHas('comments', [
      'message' => 'This is an updated comment',
    ]);
  }

  /**
   * @test
   */
  public function
  it_validates_that_an_id_member_is_given_when_updating_a_comment()
  {
    $user = factory(User::class)->create();
    Passport::actingAs($user);

    $comment = factory(Comment::class)->create();

    $this->patchJson("/api/v1/comments/{$comment->id}", [
      'data' => [
        'type' => 'comments',
        'attributes' => [
          'message' => 'Jane Doe',
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
  }

  /**
   * @test
   */
  public function
  it_validates_that_an_id_member_is_a_string_when_updating_a_comment()
  {
    $user = factory(User::class)->create();
    Passport::actingAs($user);

    $comment = factory(Comment::class)->create();

    $this->patchJson("/api/v1/comments/{$comment->id}", [
      'data' => [
        'id' => 1,
        'type' => 'comments',
        'attributes' => [
          'message' => 'Jane Doe',
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
  }

  /**
   * @test
   */
  public function
  it_validates_that_the_type_member_is_given_when_updating_a_comment()
  {
    $user = factory(User::class)->create();
    Passport::actingAs($user);

    $comment = factory(Comment::class)->create();

    $this->patchJson("/api/v1/comments/{$comment->id}", [
      'data' => [
        'id' => '1',
        'attributes' => [
          'message' => 'Jane Doe',
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
  }

  /**
   * @test
   */
  public function
  it_validates_that_the_type_member_has_a_value_of_comments_given_when_updating_a_comment()
  {
    $user = factory(User::class)->create();
    Passport::actingAs($user);

    $comment = factory(Comment::class)->create();

    $this->patchJson("/api/v1/comments/{$comment->id}", [
      'data' => [
        'id' => '1',
        'type' => 'person',
        'attributes' => [
          'name' => 'Jane Doe',
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
  }

  /**
   * @test
   */
  public function
  it_validates_that_the_attributes_member_has_been_given_when_updating_a_comment()
  {
    $user = factory(User::class)->create();
    Passport::actingAs($user);

    $comment = factory(Comment::class)->create();

    $this->patchJson("/api/v1/comments/{$comment->id}", [
      'data' => [
        'id' => '1',
        'type' => 'comments',
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
  }

  /**
   * @test
   */
  public function
  it_validates_that_the_attributes_member_is_an_object_given_when_updating_a_comment()
  {
    $user = factory(User::class)->create();
    Passport::actingAs($user);

    $comment = factory(Comment::class)->create();

    $this->patchJson("/api/v1/comments/{$comment->id}", [
      'data' => [
        'id' => '1',
        'type' => 'comments',
        'attributes' => 'this is not an object'
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
  }

  /**
   * @test
   */
  public function
  it_validates_that_a_message_attribute_is_given_when_updating_a_comment()
  {
    $user = factory(User::class)->create();
    Passport::actingAs($user);

    $comment = factory(Comment::class)->create();

    $this->patchJson("/api/v1/comments/{$comment->id}", [
      'data' => [
        'id' => '1',
        'type' => 'comments',
        'attributes' => [
          'message' => ''
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
            'details' => 'The data.attributes.message field is required.',
            'source' => [
              'pointer' => '/data/attributes/message',
            ]
          ]
        ]
      ]);
  }

  /**
   * @test
   */
  public function
  it_validates_that_a_message_attribute_is_a_string_when_updating_a_comment()
  {
    $user = factory(User::class)->create();
    Passport::actingAs($user);

    $comment = factory(Comment::class)->create();

    $this->patchJson("/api/v1/comments/{$comment->id}", [
      'data' => [
        'id' => '1',
        'type' => 'comments',
        'attributes' => [
          'message' => 123456
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
            'details' => 'The data.attributes.message must be a string.',
            'source' => [
              'pointer' => '/data/attributes/message',
            ]
          ]
        ]
      ]);
  }

  /**
   * @test
   */
  public function it_can_delete_a_comment_through_a_delete_request()
  {
    $user = factory(User::class)->create();
    Passport::actingAs($user);

    $comment = factory(Comment::class)->create();

    $this->deleteJson("/api/v1/comments/{$comment->id}", [], [
      'Accept' => 'application/vnd.api+json',
      'Content-Type' => 'application/vnd.api+json',
    ])->assertStatus(204);

    $this->assertDatabaseMissing('comments', [
      'id' => $comment->id,
      'message' => $comment->message,
    ]);
  }

  /**
   * @test
   */
  public function it_can_sort_comments_by_created_at_through_a_sort_query_param()
  {
    $user = factory(User::class)->create();
    Passport::actingAs($user);

    $comment1 = factory(Comment::class)->create();
    sleep(2);
    $comment2 = factory(Comment::class)->create();
    sleep(2);
    $comment3 = factory(Comment::class)->create();


    $this->getJson("/api/v1/comments?sort=created_at", [
      'accept' => 'application/vnd.api+json',
      'content-type' => 'application/vnd.api+json',
    ])->assertStatus(200)
      ->assertJson([
        "data" => [
          [
            "id" => $comment1->id,
            "type" => "comments",
            "attributes" => [
              'message' => $comment1->message,
              'created_at' => $comment1->created_at->toJSON(),
              'updated_at' => $comment1->updated_at->toJSON(),
            ]
          ],
          [
            "id" => $comment2->id,
            "type" => "comments",
            "attributes" => [
              'message' => $comment2->message,
              'created_at' => $comment2->created_at->toJSON(),
              'updated_at' => $comment2->updated_at->toJSON(),
            ]
          ],
          [
            "id" => $comment3->id,
            "type" => "comments",
            "attributes" => [
              'message' => $comment3->message,
              'created_at' => $comment3->created_at->toJSON(),
              'updated_at' => $comment3->updated_at->toJSON(),
            ]
          ],
        ]
      ]);
  }

  /**
   * @test
   */
  public function it_can_sort_comments_by_created_at_in_descending_order_through_a_sort_query_param()
  {
    $user = factory(User::class)->create();
    Passport::actingAs($user);

    $comment1 = factory(Comment::class)->create();
    sleep(2);
    $comment2 = factory(Comment::class)->create();
    sleep(2);
    $comment3 = factory(Comment::class)->create();


    $this->getJson("/api/v1/comments?sort=-created_at", [
      'accept' => 'application/vnd.api+json',
      'content-type' => 'application/vnd.api+json',
    ])->assertStatus(200)
      ->assertJson([
        "data" => [
          [
            "id" => $comment3->id,
            "type" => "comments",
            "attributes" => [
              'message' => $comment3->message,
              'created_at' => $comment3->created_at->toJSON(),
              'updated_at' => $comment3->updated_at->toJSON(),
            ]
          ],
          [
            "id" => $comment2->id,
            "type" => "comments",
            "attributes" => [
              'message' => $comment2->message,
              'created_at' => $comment2->created_at->toJSON(),
              'updated_at' => $comment2->updated_at->toJSON(),
            ]
          ],
          [
            "id" => $comment1->id,
            "type" => "comments",
            "attributes" => [
              'message' => $comment1->message,
              'created_at' => $comment1->created_at->toJSON(),
              'updated_at' => $comment1->updated_at->toJSON(),
            ]
          ],
        ]
      ]);
  }

  /**
   * @test
   */
  public function it_can_paginate_comments_through_a_page_query_param()
  {
    $users = factory(User::class)->create();
    Passport::actingAs($users);

    $comments = factory(Comment::class, 6)->create();

    $this->getJson("/api/v1/comments?page[size]=3&page[number]=1", [
      'accept' => 'application/vnd.api+json',
      'content-type' => 'application/vnd.api+json',
    ])->assertStatus(200)
      ->assertJson([
        "data" => [
          [
            "id" => '1',
            "type" => "comments",
            "attributes" => [
              'message' => $comments[0]->message,
              'created_at' => $comments[0]->created_at->toJSON(),
              'updated_at' => $comments[0]->updated_at->toJSON(),
            ]
          ],
          [
            "id" => '2',
            "type" => "comments",
            "attributes" => [
              'message' => $comments[1]->message,
              'created_at' => $comments[1]->created_at->toJSON(),
              'updated_at' => $comments[1]->updated_at->toJSON(),
            ]
          ],
          [
            "id" => '3',
            "type" => "comments",
            "attributes" => [
              'message' => $comments[2]->message,
              'created_at' => $comments[2]->created_at->toJSON(),
              'updated_at' => $comments[2]->updated_at->toJSON(),
            ]
          ],
        ],
        'links' => [
          'first' => route(
            'comments.index',
            ['page[size]' => 3, 'page[number]' => 1]
          ),
          'last' => route(
            'comments.index',
            ['page[size]' => 3, 'page[number]' => 2]
          ),
          'prev' => null,
          'next' => route(
            'comments.index',
            ['page[size]' => 3, 'page[number]' => 2]
          ),
        ]
      ]);
  }

  /**
   * @test
   */
  public function it_can_paginate_comments_through_a_page_query_param_and_show_different_pages()
  {
    $users = factory(User::class)->create();
    Passport::actingAs($users);

    $comments = factory(Comment::class, 6)->create();

    $this->getJson("/api/v1/comments?page[size]=3&page[number]=2", [
      'accept' => 'application/vnd.api+json',
      'content-type' => 'application/vnd.api+json',
    ])->assertStatus(200)
      ->assertJson([
        "data" => [
          [
            "id" => $comments[3]->id,
            "type" => "comments",
            "attributes" => [
              'message' => $comments[3]->message,
              'created_at' => $comments[3]->created_at->toJSON(),
              'updated_at' => $comments[3]->updated_at->toJSON(),
            ]
          ],
          [
            "id" => $comments[4]->id,
            "type" => "comments",
            "attributes" => [
              'message' => $comments[4]->message,
              'created_at' => $comments[4]->created_at->toJSON(),
              'updated_at' => $comments[4]->updated_at->toJSON(),
            ]
          ],
          [
            "id" => $comments[5]->id,
            "type" => "comments",
            "attributes" => [
              'message' => $comments[5]->message,
              'created_at' => $comments[5]->created_at->toJSON(),
              'updated_at' => $comments[5]->updated_at->toJSON(),
            ]
          ],
        ],
        'links' => [
          'first' => route(
            'comments.index',
            ['page[size]' => 3, 'page[number]' => 1]
          ),
          'last' => route(
            'comments.index',
            ['page[size]' => 3, 'page[number]' => 2]
          ),
          'prev' => route(
            'comments.index',
            ['page[size]' => 3, 'page[number]' => 1]
          ),
          'next' => null
        ]
      ]);
  }
}
