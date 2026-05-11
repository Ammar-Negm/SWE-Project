<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../app/services/OrderService.php';

class OrderServiceTest extends TestCase
{
    public function testOptimizePickRouteSortsByShelfLocation()
    {
        $service = new OrderService();

        $tasks = [
            ['shelfLocation' => 'Z3'],
            ['shelfLocation' => 'A1'],
            ['shelfLocation' => 'B2'],
        ];

        $result = $service->optimizePickRoute($tasks);

        $this->assertEquals('A1', $result[0]['shelfLocation']);
    }

    public function testSafeUpdateStatusInvalidFlowReturnsFalse()
    {
        $service = new OrderService();

        // هنفترض order status مش متوافق → لازم يرجع false
        $result = $service->safeUpdateStatus(1, 'Delivered');

        $this->assertFalse($result);
    }
}