<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Book;
use App\Models\Author;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AuthorsAuthorizationTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function a_user_cannot_create_an_author()
    {
        $user = factory(User::class)->create([
            'role' => 'user',
        ]);

        Passport::actingAs($user);

        $this->postJson('/api/v1/authors', [
            'data' => [
                'type' => 'authors',
                'attributes' => [
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'other_name' => 'Demo',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(403)->assertJson([
            'errors' => [
                [
                    'title' => 'Access Denied Http Exception',
                    'details' => 'This action is unauthorized.',
                ]
            ]
        ]);
    }

    /**
     * @test
     */
    public function an_admin_can_create_a_book()
    {
        $user = factory(User::class)->create([
            'role' => 'admin',
        ]);

        Passport::actingAs($user);

        $this->postJson('/api/v1/authors', [
            'data' => [
                'type' => 'authors',
                'attributes' => [
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'other_name' => 'Demo',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(201);
    }

    /**
     * @test
     */
    public function a_user_cannot_update_an_author()
    {
        $user = factory(User::class)->create([
            'role' => 'user',
        ]);
        Passport::actingAs($user);

        $author = factory(Author::class)->create();

        $this->patchJson('/api/v1/authors/1', [
            'data' => [
                'id' => '1',
                'type' => 'authors',
                'attributes' => [
                    'first_name' => 'Billy',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(403)->assertJson([
            'errors' => [
                [
                    'title' => 'Access Denied Http Exception',
                    'details' => 'This action is unauthorized.',
                ]
            ]
        ]);
    }
    /**
     * @test
     */
    public function an_admin_can_update_an_author()
    {

        $user = factory(User::class)->create([
            'role' => 'admin',
        ]);
        Passport::actingAs($user);

        $author = factory(Author::class)->create();

        $this->patchJson('/api/v1/authors/1', [
            'data' => [
                'id' => '1',
                'type' => 'authors',
                'attributes' => [
                    'first_name' => 'Billy',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(200);
    }

    /**
     * @test
     */
    public function a_user_cannot_delete_a_book()
    {
        $user = factory(User::class)->create([
            'role' => 'user',
        ]);
        Passport::actingAs($user);

        $author = factory(Author::class)->create();

        $this->delete('/api/v1/authors/1', [], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(403)->assertJson([
            'errors' => [
                [
                    'title' => 'Access Denied Http Exception',
                    'details' => 'This action is unauthorized.',
                ]
            ]
        ]);
    }

    /**
     * @test
     */
    public function an_admin_can_delete_a_book()
    {
        $user = factory(User::class)->create([
            'role' => 'admin',
        ]);

        Passport::actingAs($user);

        $author = factory(Author::class)->create();

        $this->delete('/api/v1/authors/1', [], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(204);
    }

    /**
     * @test
     */
    public function a_user_can_fetch_a_list_of_authors()
    {
        $user = factory(User::class)->create([
            'role' => 'user',
        ]);

        Passport::actingAs($user);

        $author = factory(Author::class, 3)->create();

        $this->get('/api/v1/authors', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(200);
    }

    /**
     * @test
     */
    public function an_admin_can_fetch_a_list_of_authors()
    {
        $user = factory(User::class)->create([
            'role' => 'admin',
        ]);

        Passport::actingAs($user);

        $author = factory(Author::class, 3)->create();

        $this->get('/api/v1/authors', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(200);
    }

    /**
     * @test
     */
    public function a_user_can_fetch_a_single_author()
    {
        $user = factory(User::class)->create([
            'role' => 'user'
        ]);

        Passport::actingAs($user);

        $author = factory(Author::class)->create();

        $this->getJson('/api/v1/authors/1', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(200);
    }

    /**
     * @test
     */
    public function an_admin_can_fetch_a_single_author()
    {
        $user = factory(User::class)->create([
            'role' => 'admin'
        ]);

        Passport::actingAs($user);

        $author = factory(Author::class)->create();

        $this->getJson('/api/v1/authors/1', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(200);
    }

    /**
     * @test
     */
    public function a_user_cannot_modify_relationship_links_for_books()
    {
        $books = factory(Book::class, 10)->create();
        $author = factory(Author::class)->create();

        $user = factory(User::class)->create([
            'role' => 'user'
        ]);
        Passport::actingAs($user);

        $this->patchJson('/api/v1/authors/1/relationships/books', [
            'data' => [
                [
                    'id' => '5',
                    'type' => 'books',
                ],
                [
                    'id' => '6',
                    'type' => 'books',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(403)->assertJson([
            'errors' => [
                [
                    'title' => 'Access Denied Http Exception',
                    'details' => 'This action is unauthorized.',
                ]
            ]
        ]);
    }

    /**
     * @test
     */
    public function an_admin_can_modify_relationship_links_for_authors()
    {
        $books = factory(Book::class, 10)->create();
        $author = factory(Author::class)->create();

        $user = factory(User::class)->create([
            'role' => 'admin'
        ]);
        Passport::actingAs($user);

        $this->patchJson('/api/v1/authors/1/relationships/books', [
            'data' => [
                [
                    'id' => '5',
                    'type' => 'books',
                ],
                [
                    'id' => '6',
                    'type' => 'books',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(204);
    }
}
