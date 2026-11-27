<?php
// This file is part of Moodle - http://moodle.org/
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

namespace block_activity_results;

use advanced_testcase;
use block_activity_results;
use context_course;

/**
 * PHPUnit block_activity_results tests
 *
 * @package    block_activity_results
 * @category   test
 * @copyright  2026 Jayce Birrell <jayce.birrell@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\block_activity_results::class)]
final class block_activity_results_test extends advanced_testcase {
    #[\Override]
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot . '/blocks/moodleblock.class.php');
        require_once($CFG->dirroot . '/blocks/activity_results/block_activity_results.php');
        parent::setUpBeforeClass();
    }

    /**
     * Percentage format must not throw a division by zero when the grade item has a max grade of 0.
     *
     * @param bool $usegroups Whether to display group results.
     * @param int $showbest Number of best scores to show.
     * @param int $showworst Number of worst scores to show.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('grademax_zero_provider')]
    public function test_get_content_grademax_zero(bool $usegroups, int $showbest, int $showworst): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $assign = $generator->create_module('assign', ['course' => $course->id, 'groupmode' => VISIBLEGROUPS]);
        $student = $generator->create_and_enrol($course, 'student');
        $group = $generator->create_group(['courseid' => $course->id]);
        $generator->create_group_member(['groupid' => $group->id, 'userid' => $student->id]);

        $gradeitem = \grade_item::fetch(['itemmodule' => 'assign', 'iteminstance' => $assign->id]);
        $gradeitem->update_final_grade($student->id, 0);
        // The DB returns grademax as a decimal string ("0.00000"), which is truthy in PHP.
        $DB->set_field('grade_items', 'grademax', 0, ['id' => $gradeitem->id]);

        $block = new block_activity_results();
        $block->instance = (object) ['id' => 1, 'parentcontextid' => context_course::instance($course->id)->id];
        $block->config = (object) [
            'activitygradeitemid' => $gradeitem->id,
            'showbest' => $showbest,
            'showworst' => $showworst,
            'gradeformat' => B_ACTIVITYRESULTS_GRADE_FORMAT_PCT,
            'usegroups' => $usegroups,
        ];

        $this->assertStringContainsString('--%', $block->get_content()->text);
    }

    /**
     * Data provider for test_get_content_grademax_zero.
     *
     * @return array
     */
    public static function grademax_zero_provider(): array {
        return [
            'Users, best' => [false, 1, 0],
            'Users, worst' => [false, 0, 1],
            'Groups, best' => [true, 1, 0],
            'Groups, worst' => [true, 0, 1],
        ];
    }
}
