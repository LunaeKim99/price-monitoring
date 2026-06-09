<?php

namespace Tests\Unit\Domain\Entities;

use App\Domain\Entities\Commodity;
use PHPUnit\Framework\TestCase;

class CommodityTest extends TestCase
{
    public function test_can_create_commodity(): void
    {
        $commodity = new Commodity(
            name: 'Beras Premium',
            category: 'Sembako',
            unit: 'kg',
            isActive: true
        );

        $this->assertEquals('Beras Premium', $commodity->getName());
        $this->assertEquals('Sembako', $commodity->getCategory());
        $this->assertEquals('kg', $commodity->getUnit());
        $this->assertTrue($commodity->isActive());
        $this->assertNull($commodity->getId());
    }

    public function test_can_set_and_get_id(): void
    {
        $commodity = new Commodity('Test');
        $commodity->setId(1);

        $this->assertEquals(1, $commodity->getId());
    }

    public function test_can_update_name(): void
    {
        $commodity = new Commodity('Original');
        $commodity->setName('Updated');

        $this->assertEquals('Updated', $commodity->getName());
    }

    public function test_default_is_active(): void
    {
        $commodity = new Commodity('Test');

        $this->assertTrue($commodity->isActive());
    }

    public function test_can_set_inactive(): void
    {
        $commodity = new Commodity('Test');
        $commodity->setIsActive(false);

        $this->assertFalse($commodity->isActive());
    }
}
