<?php
use PHPUnit\Framework\TestCase;

class StorageServiceTest extends TestCase
{
    private $storage;

    protected function setUp(): void
    {
        require_once __DIR__ . '/../../app/services/StorageService.php';
        $this->storage = new StorageService();
    }

    public function testSimulateWeightInRange()
    {
        $result = $this->storage->simulateWeight(10);
        $this->assertGreaterThanOrEqual(8, $result);
        $this->assertLessThanOrEqual(12, $result);
    }

    public function testValidateParcelTrue()
    {
        $this->assertTrue($this->storage->validateParcel(5, 10));
    }

    public function testValidateParcelFalse()
    {
        $this->assertFalse($this->storage->validateParcel(15, 10));
    }
}