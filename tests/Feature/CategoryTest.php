<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function testExample()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testList(){

        $category = Category::create([
            'name' => 'test'
        ]);

        factory(Category::class, 1)->create();

        $categories = Category::all();

        $this->assertCount(2, $categories);

        $categoryKey = array_keys($categories->first()->getAttributes());

        $this->assertEqualsCanonicalizing([
            'id',
            'name',
            'description',
            'is_active',
            'created_at',
            'updated_at',
            'deleted_at'
        ], $categoryKey);
    }

    public function testCreate() {

        $category = Category::create([
            'name' => 'test'
        ])->refresh();

        $this->assertEquals('test', $category->name);
        $this->assertNull($category->description);
        $this->assertTrue($category->is_active);

        $category = Category::create([
            'name' => 'test',
            'description' => null
        ])->refresh();

        $this->assertNull($category->description);

        $category = Category::create([
            'name' => 'test',
            'description' => 'test description'
        ])->refresh();

        $this->assertEquals('test description', $category->description);

        $category = Category::create([
            'name' => 'test',
            'is_active' => false
        ])->refresh();

        $this->assertFalse($category->is_active);
    }

    public function testUpdate() {

        $category = Category::create([
            'name' => 'test'
        ])->refresh();

        $category->update([
            'name' => 'updated name',
            'description' => 'updated description',
            'is_active' => false
        ]);

        $category = Category::all()->first();

        $this->assertEquals('updated name', $category->name);
        $this->assertEquals('updated description', $category->description);
        $this->assertFalse($category->is_active);
    }

    public function testDelete() {

        $category = Category::create([
            'name' => 'test'
        ]);

        $category->delete();

        $this->assertCount(0, Category::all());
    }

    public function testUuid() {

        $category = Category::create([
            'name' => 'test'
        ]);

        $this->assertRegExp('/[a-z0-9]{8}\-[a-z0-9]{4}\-[a-z0-9]{4}\-[a-z0-9]{4}\-[a-z0-9]{12}/', $category->id);
    }
}
