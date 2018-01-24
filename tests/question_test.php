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

/**
 * Unit tests for the varnumunit question definition class.
 *
 * @package   qtype_varnumunit
 * @copyright 2018 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();
global $CFG;

require_once($CFG->dirroot . '/question/type/varnumericset/question.php');
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');


/**
 * Unit tests for the varnumunit question definition class.
 *
 * @copyright 2018 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_varnumunit_question_test extends advanced_testcase {

    public function test_classify_response_correct_response() {
        $question = test_question_maker::make_question('varnumunit', 'simple_1_m');
        $question->start_attempt(new question_attempt_step(), 1);
        $actual = $question->classify_response(['answer' => '1m']);
        $this->assertEquals('match(m)', $actual['unitpart']->responseclassid);
        $this->assertEquals('m', $actual['unitpart']->response);
        $this->assertEquals(1, $actual['unitpart']->fraction);
        $this->assertEquals(1, $actual['numericpart']->responseclassid);
        $this->assertEquals(1, $actual['numericpart']->response);
        $this->assertEquals(1, $actual['numericpart']->fraction);
    }

    public function test_classify_response_blank_response() {
        $question = test_question_maker::make_question('varnumunit', 'simple_1_m');
        $question->start_attempt(new question_attempt_step(), 1);
        $actual = $question->classify_response(['answer' => '']);
        $this->assertEquals('', $actual['unitpart']->responseclassid);
        $this->assertEquals('[No response]', $actual['unitpart']->response);
        $this->assertEquals(0, $actual['unitpart']->fraction);
        $this->assertEquals('', $actual['numericpart']->responseclassid);
        $this->assertEquals('[No response]', $actual['numericpart']->response);
        $this->assertEquals(0, $actual['numericpart']->fraction);
    }
}
