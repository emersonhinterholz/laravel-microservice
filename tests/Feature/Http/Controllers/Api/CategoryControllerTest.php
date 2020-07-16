<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Facades\Lang;
use Tests\TestCase;
use Tests\Helpers\Uuid;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testIndex()
    {
        $response = $this->get(route('api.categories.index'));
        $response->assertStatus(200);
        $response->assertJsonCount(0);

        $category = factory(Category::class)->create()->refresh();

        $response = $this->get(route('api.categories.index'));

        $response->assertStatus(200);
        $response->assertJsonCount(1);

        $response->assertJson([$category->toArray()]);
    }

    public function testShow()
    {
        $response = $this->get(route('api.categories.show', ['category' => Uuid::generate()]));
        $response->assertStatus(404);

        $category = factory(Category::class)->create()->refresh();

        $response = $this->get(route('api.categories.show', ['category' => $category->id]));
        $response->assertStatus(200);

        $response->assertJson($category->toArray());
    }

    public function testDataInvalidation()
    {
        $response = $this->json('POST', route('api.categories.store'), []);
        $this->assertDataInvalidationRequiredResponse($response);

        $response = $this->json('POST', route('api.categories.store'), [
            'name' => str_repeat('x', 256),
            'is_active' => 'a'
        ]);
        $this->assertDataInvalidationNonRequiredResponse($response);

        $category = factory(Category::class)->create();

        $response = $this->json('PUT', route('api.categories.update', $category), []);
        $this->assertDataInvalidationRequiredResponse($response);

        $response = $this->json('PUT', route('api.categories.update', $category), [
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
        $response = $this->json('POST', route('api.categories.store'), [
           'name'  => 'test'
        ]);

        $id = $response->json('id');
        $category = Category::find($id);

        $response
            ->assertStatus(201)
            ->assertJson($category->toArray());

        $this->assertTrue($response->json('is_active'));
        $this->assertNull($response->json('description'));

        $response = $this->json('POST', route('api.categories.store'), [
            'name'  => 'test',
            'description' => 'description',
            'is_active' => false
        ]);

        $id = $response->json('id');
        $category = Category::find($id);

        $response
            ->assertStatus(201)
            ->assertJson($category->toArray());

        $this->assertFalse($response->json('is_active'));
        $this->assertEquals($response->json('description'), 'description');
    }

    public function testUpdate() {

        $category = factory(Category::class)->create();
        $nameUpdated = 'name updated';
        $descriptionUpdated = 'description updated';

        $response = $this->json('PUT', route('api.categories.update', $category), [
            'name'  => $nameUpdated,
            'description' => $descriptionUpdated,
            'is_active' => false
        ]);

        $updatedCategory = Category::find($category->id);

        $response
            ->assertStatus(200)
            ->assertJson($updatedCategory->toArray());

        $this->assertEquals($response->json('name'), $nameUpdated);
        $this->assertEquals($response->json('description'), $descriptionUpdated);
        $this->assertFalse($response->json('is_active'));

        $response = $this->json('PUT', route('api.categories.update', $category), [
            'name'  => $nameUpdated,
            'description' => null,
            'is_active' => true
        ]);

        $updatedCategory = Category::find($category->id);

        $response
            ->assertStatus(200)
            ->assertJson($updatedCategory->toArray());

        $this->assertEquals($response->json('name'), $nameUpdated);
        $this->assertEquals($response->json('description'), null);
        $this->assertTrue($response->json('is_active'));
    }

    public function testDestroy() {
        $category = factory(Category::class)->create();

        $response = $this->json('DELETE', route('api.categories.destroy', $category), []);

        $response
            ->assertStatus(204);

        $destroyedCategory = Category::find($category->id);

        $this->assertNull($destroyedCategory);

        $response = $this->json('DELETE', route('api.categories.destroy', $category), []);

        $response
            ->assertStatus(404);
    }

}
