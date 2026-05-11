<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../app/services/StorageService.php';



class StorageServiceTest extends TestCase
{
    public function testValidateParcelTrue()
    {
        $service = new StorageService();

        $this->assertTrue($service->validateParcel(5, 10));
    }

    public function testValidateParcelFalse()
    {
        $service = new StorageService();

        $this->assertFalse($service->validateParcel(15, 10));
    }

    public function testSimulateWeightReturnsInt()
    {
        $service = new StorageService();

        $result = $service->simulateWeight(10);

        $this->assertIsInt($result);
    }
}