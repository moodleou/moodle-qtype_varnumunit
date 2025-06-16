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
 * The varnumunit question definition class.
 *
 * @package    qtype_varnumunit
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use qtype_varnumunit\qtype_varnumunit_unit;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/varnumericset/questionbase.php');
require_once($CFG->dirroot.'/question/type/pmatch/pmatchlib.php');
require_once($CFG->dirroot . '/question/type/varnumunit/questiontype.php');

/**
 * Represents a varnumunit question.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_varnumunit_question extends qtype_varnumeric_question_base {

    /** @var $unitfraction float fraction of grade for this question that is for the correct unit. */
    public $unitfraction;

    /** @var qtype_varnumunit_unit[] the units. */
    public $units;

    /**
     * Gets the validation error message for pre and post validation.
     *
     * @param string $prefix The prefix of the response.
     * @param string $postfix The postfix of the response.
     */
    protected function get_pre_post_validation_error($prefix, $postfix) {
        if (!empty($prefix)) {
            return get_string('notvalidnumberprepostfound', 'qtype_varnumunit');
        } else {
            return '';
        }
    }

    /**
     * Returns the matching answer for the response.
     *
     * @param array $response The response to check.
     */
    public function get_matching_unit($response) {
        foreach ($this->get_units() as $uid => $unit) {
            // Clone unit variable because we're going to change the object property.
            $unit = clone $unit;
            $unitcorrect = $this->check_for_unit_in_response($response, $unit);
            $requiresinglespace = $unit->spaceinunit == qtype_varnumunit::SPACEINUNIT_PRESERVE_SPACE_REQUIRE;
            $hasspacingfeedback = $unit->feedback == $unit->spacingfeedback;
            if ($unitcorrect || ($requiresinglespace && $hasspacingfeedback)) {
                return $unit;
            }
        }
        return null;
    }

    /**
     * Returns the matching answer for the response.
     */
    protected function get_units() {
        return $this->units;
    }

    /**
     * Removes unwanted characters from the unit part of the response.
     *
     * This method is used to ensure that the unit part of the response
     * conforms to the expected format, such as replacing dashes with spaces.
     *
     * @param string $unitpartofresponse The unit part of the response.
     * @param qtype_varnumunit_unit $unit The unit object containing formatting rules.
     */
    protected function remove_unwanted_chars_from_unit($unitpartofresponse, qtype_varnumunit_unit $unit) {
        if ($unit->replacedash) {
            $unitpartofresponse = preg_replace('!\p{Pd}!u', '-', $unitpartofresponse ?? '');
        }
        return $unitpartofresponse;
    }

    /**
     * Returns the pmatch options for this question type.
     *
     * Override this method in subclasses to provide specific pmatch options.
     *
     * @return array|null The pmatch options or null if not applicable.
     */
    protected function pmatch_options() {
        return null;
    }

    /**
     * Checks if the unit part of the response matches the expected unit.
     *
     * @param array $response The response to check.
     * @param qtype_varnumunit_unit $unit The unit to check against.
     */
    public function check_for_unit_in_response(array $response, qtype_varnumunit_unit $unit) {
        if (!isset($response['answer'])) {
            return false;
        }
        list(, $unitpartofresonse) = $this->split_response_into_num_and_unit($response['answer']);
        if ($unit->unit == '*') {
            return $unitpartofresonse !== null;
        }

        if ($unit->spaceinunit == qtype_varnumunit::SPACEINUNIT_REMOVE_ALL_SPACE) {
            $unitpartofresonse = preg_replace('!\s!u', '', $unitpartofresonse ?? '');
        }

        $unitpartofresonse = $this->remove_unwanted_chars_from_unit($unitpartofresonse, $unit);
        $rightunit = self::check_for_match_for_unit_pmatch_expression($unitpartofresonse, $unit->unit, $this->pmatch_options());

        // Got the right unit, now we'll perform a final check for single space mode.
        if ($rightunit && $unit->spaceinunit == qtype_varnumunit::SPACEINUNIT_PRESERVE_SPACE_REQUIRE) {
            $spacecount = strlen($unitpartofresonse) - strlen(ltrim($unitpartofresonse));
            if ($spacecount != 1) {
                $unit->feedback = $unit->spacingfeedback;
                $unit->fraction = 0;
                return false;
            }
        }

        return $rightunit;
    }

    /**
     * Checks if a string matches a pmatch expression.
     *
     * @param string $string The string to check.
     * @param string $expression The pmatch expression to match against.
     * @param array $options Options for the pmatch library.
     */
    public static function check_for_match_for_unit_pmatch_expression($string, $expression, $options) {
        $string = new pmatch_parsed_string($string, $options);
        $expression = new pmatch_expression($expression, $options);
        return $expression->matches($string);
    }

    #[\Override]
    public function grade_response(array $response) {
        $gradenumerical = $this->grade_numeric_part_of_response($response);
        $gradeunit = $this->grade_unit_part_of_response($response);
        $overallgrade = $this->weight_grades_for_num_and_unit_part($gradenumerical, $gradeunit);
        return [$overallgrade, question_state::graded_state_for_fraction($overallgrade)];
    }

    /**
     * Weights the grades for the numeric and unit parts of the response.
     *
     * @param float $gradenumerical The grade for the numeric part.
     * @param float $gradeunit The grade for the unit part.
     */
    protected function weight_grades_for_num_and_unit_part($gradenumerical, $gradeunit) {
        $unitfraction = $this->unitfraction;
        return ((1 - $unitfraction) * $gradenumerical) + (($unitfraction) * $gradeunit);
    }

    /**
     * Grades the unit part of the response.
     *
     * @param array $response The response to grade.
     */
    protected function grade_unit_part_of_response($response) {
        $unit = $this->get_matching_unit($response);
        if (!is_null($unit)) {
            return $unit->fraction;
        } else {
            return 0;
        }
    }

    /**
     * Grades the numeric part of the response.
     *
     * @param array $response The response to grade.
     */
    protected function grade_numeric_part_of_response($response) {
        list($gradenumerical, ) = parent::grade_response($response);
        return $gradenumerical;
    }

    #[\Override]
    public function summarise_response(array $response) {
        if (isset($response['answer'])) {
            $a = new stdClass();
            list($a->numeric, $a->unit) = $this->split_response_into_num_and_unit($response['answer']);
            return get_string('summarise_response', 'qtype_varnumunit', $a);
        } else {
            return null;
        }
    }

    /**
     * Split the response into numeric and unit parts.
     *
     * @param string $response The response to split.
     * @return array An array containing the numeric part and the unit part.
     */
    protected function split_response_into_num_and_unit($response) {
        $num = new qtype_varnumericset_number_interpreter_number_with_optional_sci_notation($this->usesupeditor);
        $num->match($response);
        $numeric = $num->get_normalised();
        return [$numeric, $num->get_postfix()];
    }

    #[\Override]
    protected function feedback_for_post_prefix_parts($prefix, $postfix) {
        return '';
    }

    #[\Override]
    public function compute_final_grade($responses, $totaltries) {
        // Remove non numeric part of response to pass numeric part to parent class.
        $numericresponses = [];
        foreach ($responses as $responseno => $response) {
            list($numericpartofresponse, ) = $this->split_response_into_num_and_unit($response['answer']);
            $numericresponses[] = ['answer' => $numericpartofresponse];
        }

        $numerictotaltries = $totaltries;
        while ((count($numericresponses) >= 2) &&
                    $numericresponses[count($numericresponses) - 1] === $numericresponses[count($numericresponses) - 2]) {
            array_pop($numericresponses);
            $numerictotaltries--;
        }

        $numericpartfraction = parent::compute_final_grade($numericresponses, $numerictotaltries);
        $matchsince = -1;
        $lastid = 0;

        if (count($responses)) {
            foreach ($responses as $i => $response) {
                $match = $this->get_matching_unit($response);
                if ($match !== null && $lastid !== $match->id) {
                    $matchsince = $i;
                    $lastid = $match->id;
                }
            }
        } else {
            $match = null;
        }

        if ($match !== null) {
            $totalpenalty = $matchsince * $this->penalty;
            $unitpartfraction = max(0, $match->fraction - $totalpenalty);
        } else {
            $unitpartfraction = 0;
        }
        return $this->weight_grades_for_num_and_unit_part($numericpartfraction, $unitpartfraction);
    }

    /**
     * Classify the response into numeric and unit parts.
     *
     * @param array $response The response to classify.
     * @return array An associative array with 'unitpart' and 'numericpart' classified responses.
     */
    public function classify_response(array $response) {
        if (!isset($response['answer'])) {
            $response['answer'] = '';
        }

        [$numpart, $unitpart] = $this->split_response_into_num_and_unit($response['answer']);
        $calculatorname = $this->qtype->calculator_name();
        $numresponsehtmlized = $calculatorname::htmlize_exponent($numpart);

        $ans = $this->get_matching_answer($response);
        if ($ans === null) {
            $numericclassifiedresponse = question_classified_response::no_response();
        } else {
            $numericclassifiedresponse = new question_classified_response($ans->id, $numresponsehtmlized, $ans->fraction);
        }

        $unit = $this->get_matching_unit($response);
        if ($unit === null) {
            $unitclassifiedresponse = question_classified_response::no_response();
        } else {
            if ($unit->spaceinunit == qtype_varnumunit::SPACEINUNIT_PRESERVE_SPACE_REQUIRE) {
                $unitpart = trim($unitpart);
            }
            $unitclassifiedresponse = new question_classified_response($unit->unit, $unitpart, $unit->fraction);
        }

        return ["unitpart" => $unitclassifiedresponse,
                     "numericpart" => $numericclassifiedresponse];
    }
}
