<?php

namespace Tests\Feature;

use App\Models\Category;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{

    use RefreshDatabase;

    public function testBasicTest()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testDatabase() {

        Category::create(['name' => 'test', 'description' => 'test']);

        $this->assertDatabaseHas('categories', ['name' => 'test']);
    }
}
