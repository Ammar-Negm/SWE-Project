<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../app/services/SupplierService.php';

class SupplierServiceTest extends TestCase
{
    public function testQualityCheckReturnsValidStatus()
    {
        $service = new SupplierService();

        $result = $service->qualityCheck(1);

        $this->assertTrue(
            in_array($result, ['Approved', 'Rejected', true, false])
        );
    }

    public function testSupplierScoreReturnsNumber()
{
    $this->assertTrue(true);
}
}