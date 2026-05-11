<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../app/services/PurchaseOrderService.php';

class PurchaseOrderServiceTest extends TestCase
{
    public function testValidateInboundRejectsZeroWeight()
    {
        $service = new PurchaseOrderService();

        $this->assertFalse($service->validateInbound(1, 0));
    }

    public function testValidateInboundAcceptsPositiveWeight()
    {
        $service = new PurchaseOrderService();

        $this->assertTrue($service->validateInbound(1, 10));
    }
}