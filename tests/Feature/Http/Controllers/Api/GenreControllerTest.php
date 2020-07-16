<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Facades\Lang;
use Tests\TestCase;
use Tests\Helpers\Uuid;

class GenreControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testIndex()
    {
        $response = $this->get(route('api.genres.index'));
        $response->assertStatus(200);
        $response->assertJsonCount(0);

        $genre = factory(Genre::class)->create()->refresh();

        $response = $this->get(route('api.genres.index'));

        $response->assertStatus(200);
        $response->assertJsonCount(1);

        $response->assertJson([$genre->toArray()]);
    }

    public function testShow()
    {
        $response = $this->get(route('api.genres.show', ['genre' => Uuid::generate()]));
        $response->assertStatus(404);

        $genre = factory(Genre::class)->create()->refresh();

        $response = $this->get(route('api.genres.show', ['genre' => $genre->id]));
        $response->assertStatus(200);

        $response->assertJson($genre->toArray());
    }

    public function testDataInvalidation()
    {
        $response = $this->json('POST', route('api.genres.store'), []);
        $this->assertDataInvalidationRequiredResponse($response);

        $response = $this->json('POST', route('api.genres.store'), [
            'name' => str_repeat('x', 256),
            'is_active' => 'a'
        ]);
        $this->assertDataInvalidationNonRequiredResponse($response);

        $genre = factory(Genre::class)->create();

        $response = $this->json('PUT', route('api.genres.update', $genre), []);
        $this->assertDataInvalidationRequiredResponse($response);

        $response = $this->json('PUT', route('api.genres.update', $genre), [
            'name' => str_repeat('x', 256),
            'is_active' => 'a'
        ]);
        $this->assertDataInvalidationNonRequiredResponse($response);
    }

    private function assertDataInvalidationRequiredResponse(TestResponse $response) {
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonFragment([
                Lang::get('validation.required', ['attribute' => 'name'])
            ])
            ->assertJsonMissingValidationErrors(['is_active']);
    }

    private function assertDataInvalidationNonRequiredResponse(TestResponse $response) {
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonFragment([
                Lang::get('validation.max.string', [
                    'attribute' => 'name',
                    'max' => 255
                ])
            ])
            ->assertJsonFragment([
                Lang::get('validation.boolean', [
                    'attribute' => 'is active'
                ])
            ]);
    }

    public function testStore() {
        $response = $this->json('POST', route('api.genres.store'), [
           'name'  => 'test'
        ]);

        $id = $response->json('id');
        $genre = Genre::find($id);

        $response
            ->assertStatus(201)
            ->assertJson($genre->toArray());

        $this->assertTrue($response->json('is_active'));

        $response = $this->json('POST', route('api.genres.store'), [
            'name'  => 'test',
            'is_active' => false
        ]);

        $id = $response->json('id');
        $genre = Genre::find($id);

        $response
            ->assertStatus(201)
            ->assertJson($genre->toArray());

        $this->assertFalse($response->json('is_active'));
    }

    public function testUpdate() {

        $genre = factory(Genre::class)->create();
        $nameUpdated = 'name updated';

        $response = $this->json('PUT', route('api.genres.update', $genre), [
            'name'  => $nameUpdated,
            'is_active' => false
        ]);

        $updatedGenre = Genre::find($genre->id);

        $response
            ->assertStatus(200)
            ->assertJson($updatedGenre->toArray());

        $this->assertEquals($response->json('name'), $nameUpdated);
        $this->assertFalse($response->json('is_active'));

        $response = $this->json('PUT', route('api.genres.update', $genre), [
            'name'  => $nameUpdated,
            'is_active' => true
        ]);

        $updatedGenre = Genre::find($genre->id);

        $response
            ->assertStatus(200)
            ->assertJson($updatedGenre->toArray());

        $this->assertEquals($response->json('name'), $nameUpdated);
        $this->assertTrue($response->json('is_active'));
    }

    public function testDestroy() {
        $genre = factory(Genre::class)->create();

        $response = $this->json('DELETE', route('api.genres.destroy', $genre), []);

        $response
            ->assertStatus(204);

        $destroyedGenre = Genre::find($genre->id);

        $this->assertNull($destroyedGenre);

        $response = $this->json('DELETE', route('api.genres.destroy', $genre), []);

        $response
            ->assertStatus(404);
    }

}
