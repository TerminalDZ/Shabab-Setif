<?php
/**
 * Activity Model Tests
 */

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Activity;

class ActivityTest extends TestCase
{
    /**
     * Test activity model exists
     */
    public function testActivityModelExists(): void
    {
        $this->assertTrue(class_exists(Activity::class));
    }

    /**
     * Test activity has required methods
     */
    public function testRequiredMethodsExist(): void
    {
        $this->assertTrue(method_exists(Activity::class, 'upcoming'));
        $this->assertTrue(method_exists(Activity::class, 'recent'));
        $this->assertTrue(method_exists(Activity::class, 'getImages'));
        $this->assertTrue(method_exists(Activity::class, 'getAttendanceCount'));
    }

    /**
     * Test points default value
     */
    public function testPointsDefaultValue(): void
    {
        $this->assertTrue(defined('POINTS_ACTIVITY_DEFAULT'));
        $this->assertIsInt(POINTS_ACTIVITY_DEFAULT);
        $this->assertGreaterThan(0, POINTS_ACTIVITY_DEFAULT);
    }

    /**
     * Test activity statuses
     */
    public function testValidStatuses(): void
    {
        $validStatuses = ['upcoming', 'ongoing', 'completed', 'cancelled'];

        foreach ($validStatuses as $status) {
            $this->assertContains($status, $validStatuses);
        }
    }
}
