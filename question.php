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
 * varnumunit question definition class.
 *
 * @package    qtype
 * @subpackage varnumunit
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/varnumericset/questionbase.php');
require_once($CFG->dirroot.'/question/type/pmatch/pmatchlib.php');


/**
 * Represents a varnumunit question.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_varnumunit_question extends qtype_varnumeric_question_base {
    protected function get_pre_post_validation_error($postorprefix) {
        if (!empty($string) && !empty($postorprefix[0])) {
            return get_string('notvalidnumberprepostfound', 'qtype_varnumunit');
        } else {
            return '';
        }
    }
    protected function get_matching_unit($response) {
        foreach ($this->get_units() as $uid => $unit) {
            if ($this->compare_response_with_unit($response, $unit)) {
                return $unit;
            }
        }
        return null;
    }

    protected function get_units() {
        if (!isset($this->options)) {
            $this->options = new stdClass();
            $this->qtype->load_units($this);
        }
        return $this->options->units;
    }

    protected function clean_unit($unitpartofresponse, $unit) {
        if ($unit->removespace) {
            $unitpartofresponse = preg_replace('!\s!u', '', $unitpartofresponse);
        }
        if ($unit->replacedash) {
            $unitpartofresponse = preg_replace('!\p{Pd}!u', '-', $unitpartofresponse);
        }
        return $unitpartofresponse;
    }

    protected function pmatch_options() {
        return null;
    }

    public function compare_response_with_unit(array $response, $unit) {
        if ($unit->unit == '*') {
            return true;
        }
        list(, $postorprefix) = self::normalize_number_format($response['answer'], $this->requirescinotation);
        $unitpartofresonse = $this->clean_unit($postorprefix[1], $unit);
        return self::compare_string_with_pmatch_expression($unitpartofresonse, $unit->unit, $this->pmatch_options());
    }

    public static function compare_string_with_pmatch_expression($string, $expression, $options) {
        $string = new pmatch_parsed_string($string, $options);
        $expression = new pmatch_expression($expression, $options);
        return $expression->matches($string);
    }

    public function grade_response(array $response) {
        list($gradenumerical, ) = parent::grade_response($response);
        $unit = $this->get_matching_unit($response);
        if (!is_null($unit)) {
            $gradeunit = $unit->fraction;
        } else {
            $gradeunit = 0;
        }
        $unitfraction = $this->unitfraction;
        $overallgrade =  ((1- $unitfraction) * $gradenumerical) + (($unitfraction) * $gradeunit);
        return array($overallgrade,
            question_state::graded_state_for_fraction($overallgrade));
    }
}