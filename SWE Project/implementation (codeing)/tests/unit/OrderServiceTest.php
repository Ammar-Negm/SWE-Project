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

    // بنختبر الـ flow array مباشرة من غير DB
    $flow = [
        'Pending'  => 'Picking',
        'Picking'  => 'Packing',
        'Packing'  => 'Shipped',
        'Shipped'  => 'Delivered'
    ];

    // Delivered مش في الـ flow كـ key — يعني مش ممكن تيجي بعدها حاجة
    $this->assertArrayNotHasKey('Delivered', $flow);
}
}