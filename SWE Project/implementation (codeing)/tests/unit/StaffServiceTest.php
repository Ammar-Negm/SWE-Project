<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../app/services/StaffService.php';

class StaffServiceTest extends TestCase
{
    public function testGetShiftHistoryReturnsArray()
    {
        $service = new StaffService();

        $result = $service->getShiftHistory(1);

        $this->assertIsArray($result);
    }

    public function testGetActiveClockedInReturnsArray()
    {
        $service = new StaffService();

        $result = $service->getActiveClockedIn();

        $this->assertIsArray($result);
    }
}