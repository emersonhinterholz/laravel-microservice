<?php

namespace Tests\Unit;

use App\Models\Genre;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\TestCase;

class GenreTest extends TestCase
{

    private $genre;

    protected function setUp(): void
    {
        parent::setUp();
        $this->genre = new Genre();
    }

    public function testFillableAttribute()
    {

        $fillableAttribute = ['name', 'is_active'];
        $this->assertEquals($fillableAttribute, $this->genre->getFillable());
    }

    public function testDatesAttributes() {

        $dates = ['created_at', 'deleted_at', 'updated_at'];

        $this->assertCount(count($dates), $this->genre->getDates());

        foreach($dates as $date) {
            $this->assertContains($date, $this->genre->getDates());
        }
    }

    public function testCastsAttributes() {

        $casts = [
            'id' => 'string',
            'is_active' => 'boolean'
        ];

        $this->assertEquals($casts, $this->genre->getCasts());
    }

    public function testIncrementingAttributes() {

        $this->assertFalse($this->genre->getIncrementing());
    }

    public function testUseTraits() {

        $traits = [SoftDeletes::class, Uuid::class];
        $genreTraits = class_uses(Genre::class);

        $this->assertCount(count($traits), $genreTraits);

        foreach($traits as $trait) {
            $this->assertContains($trait, $genreTraits);
        }
    }
}
