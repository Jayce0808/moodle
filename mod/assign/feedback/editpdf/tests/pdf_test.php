<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace assignfeedback_editpdf;

defined('MOODLE_INTERNAL') || die();

/**
 * Unit tests for pdf class.
 *
 * @package    assignfeedback_editpdf
 * @category   test
 * @copyright  2026 Jayce Birrell
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \assignfeedback_editpdf\pdf
 */
final class pdf_test extends \advanced_testcase {

    /**
     * Call the private get_timeout_prefix method via reflection.
     *
     * @param int $seconds
     * @return string
     */
    private function get_timeout_prefix(int $seconds): string {
        $method = new \ReflectionMethod(pdf::class, 'get_timeout_prefix');
        return $method->invoke(null, $seconds);
    }

    /**
     * Test that get_timeout_prefix returns empty string when timeout is disabled.
     */
    public function test_get_timeout_prefix_disabled(): void {
        $this->assertSame('', $this->get_timeout_prefix(0));
        $this->assertSame('', $this->get_timeout_prefix(-1));
    }

    /**
     * Test that get_timeout_prefix returns the correct prefix when binary is available.
     */
    public function test_get_timeout_prefix_with_binary(): void {
        $result = $this->get_timeout_prefix(120);

        if ($result === '') {
            $this->markTestSkipped('GNU timeout utility not available on this platform.');
        }

        $this->assertStringContainsString('timeout', $result);
        $this->assertStringContainsString('120', $result);
    }
}
