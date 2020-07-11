<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{

    private $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = new Category();
    }

    public function testFillableAttribute()
    {

        $fillableAttribute = ['name', 'description', 'is_active'];
        $this->assertEquals($fillableAttribute, $this->category->getFillable());
    }

    public function testDatesAttributes() {

        $dates = ['created_at', 'deleted_at', 'updated_at'];

        $this->assertCount(count($dates), $this->category->getDates());

        foreach($dates as $date) {
            $this->assertContains($date, $this->category->getDates());
        }
    }

    public function testCastsAttributes() {

        $casts = [
            'id' => 'string',
            'is_active' => 'boolean'
        ];

        $this->assertEquals($casts, $this->category->getCasts());
    }

    public function testIncrementingAttributes() {

        $this->assertFalse($this->category->getIncrementing());
    }

    public function testUseTraits() {

        $traits = [SoftDeletes::class, Uuid::class];
        $categoryTraits = class_uses(Category::class);

        $this->assertCount(count($traits), $categoryTraits);

        foreach($traits as $trait) {
            $this->assertContains($trait, $categoryTraits);
        }
    }
}
