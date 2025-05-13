<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\FullAddress;

class FullAddressTest extends TestCase
{
    public function testGetAddressesBySector()
    {
        $fullAddress = new FullAddress();
        $searchTerm = 'exampleSector'; // Replace with an actual sector for testing
        $result = $fullAddress->getAddressesBySector($searchTerm);

        $this->assertIsArray($result);
        // Additional assertions can be added based on expected results
    }
}
