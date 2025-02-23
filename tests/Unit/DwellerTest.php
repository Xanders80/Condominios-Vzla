<?php

namespace Tests\Unit;

use App\Models\Dweller;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DwellerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_get_the_first_dweller()
    {
        // Arrange: Create a dweller
        $dweller = Dweller::factory()->create();

        // Act: Retrieve the first dweller
        $firstDweller = Dweller::getFirstDweller();

        // Assert: Check if the retrieved dweller is the same as the created one
        $this->assertEquals($dweller->id, $firstDweller->id);
    }

    /** @test */
    public function it_returns_a_new_dweller_instance_when_no_dwellers_exist()
    {
        // Act: Retrieve the first dweller when none exist
        $firstDweller = Dweller::getFirstDweller();

        // Assert: Check if the result is an instance of Dweller
        $this->assertInstanceOf(Dweller::class, $firstDweller);
    }
}
