<?php

namespace Tests\Feature;

use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenreTest extends TestCase
{
    use RefreshDatabase;

    public function testExample()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testList(){

        Genre::create([
            'name' => 'test'
        ]);

        factory(Genre::class, 1)->create();

        $genres = Genre::all();

        $this->assertCount(2, $genres);

        $genreKey = array_keys($genres->first()->getAttributes());

        $this->assertEqualsCanonicalizing([
            'id',
            'name',
            'is_active',
            'created_at',
            'updated_at',
            'deleted_at'
        ], $genreKey);
    }

    public function testCreate() {

        $genre = Genre::create([
            'name' => 'test'
        ])->refresh();

        $this->assertEquals('test', $genre->name);
        $this->assertTrue($genre->is_active);

        $genre = Genre::create([
            'name' => 'test',
            'is_active' => false
        ])->refresh();

        $this->assertFalse($genre->is_active);
    }

    public function testUpdate() {

        $genre = Genre::create([
            'name' => 'test'
        ])->refresh();

        $genre->update([
            'name' => 'updated name',
            'is_active' => false
        ]);

        $genre = Genre::all()->first();

        $this->assertEquals('updated name', $genre->name);
        $this->assertFalse($genre->is_active);
    }

    public function testDelete() {

        $genre = Genre::create([
            'name' => 'test'
        ]);

        $genre->delete();

        $this->assertCount(0, Genre::all());
    }

    public function testUuid() {

        $genre = Genre::create([
            'name' => 'test'
        ]);

        $this->assertRegExp('/[a-z0-9]{8}\-[a-z0-9]{4}\-[a-z0-9]{4}\-[a-z0-9]{4}\-[a-z0-9]{12}/', $genre->id);
    }
}
