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
 * Defines the editing form for the variable numeric question type.
 *
 * @package    qtype
 * @subpackage varnumunit
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/varnumericset/edit_varnumericset_form_base.php');
require_once($CFG->dirroot.'/question/type/pmatch/pmatchlib.php');

/**
 * variable numeric question editing form definition.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_varnumunit_edit_form extends qtype_varnumeric_edit_form_base {

    public function qtype() {
        return 'varnumunit';
    }

    /**
     * Get list of form elements to repeat for each 'units' block.
     * @param object $mform the form being built.
     * @param $label the label to use for the header.
     * @param $gradeoptions the possible grades for each answer.
     * @param $repeatedoptions reference to array of repeated options to fill
     * @param $unitoption reference to return the name of $question->options
     *                       field holding an array of units
     * @return array of form fields.
     */
    protected function get_per_unit_fields($mform, $label, $gradeoptions) {
        $repeated = array();
        $repeated[] = $mform->createElement('header', 'unithdr', $label);
        $repeated[] = $mform->createElement('textarea', 'units', get_string('answer', 'question'),
            array('rows' => '2', 'cols' => '60', 'class' => 'textareamonospace'));
        $repeated[] = $mform->createElement('selectyesno', 'removespace', get_string('removespace', 'qtype_varnumunit'));
        $repeated[] = $mform->createElement('selectyesno', 'replacedash', get_string('replacedash', 'qtype_varnumunit'));
        $repeated[] = $mform->createElement('select', 'unitsfraction',
            get_string('grade'), $gradeoptions);
        $repeated[] = $mform->createElement('editor', 'unitsfeedback',
            get_string('feedback', 'question'),
            array('rows' => 5), $this->editoroptions);
        return $repeated;
    }

    protected function data_preprocessing($question) {
        $question = parent::data_preprocessing($question);
        return $this->data_preprocessing_units($question);
    }

    /**
     * Perform the necessary preprocessing for the unit fields.
     * @param object $question the data being passed to the form.
     * @return object $question the modified data.
     */
    protected function data_preprocessing_units($question) {
        if (empty($question->options)) {
            return $question;
        }

        $question->units = array();
        $question->unitsfraction = array();
        $question->removespace = array();
        $question->replacedash = array();

        $key = 0;
        foreach ($question->options->units as $unitid => $unit) {
            if ($unit->unit != '*') {
                $question->units[$key] = $unit->unit;
                $question->removespace[$key] = $unit->removespace;
                $question->replacedash[$key] = $unit->replacedash;
                $question->unitsfraction[$key] = 0 + $unit->fraction;

                $question->unitsfeedback[$key] = $this->unit_feedback_html_element_preprocess('unitsfeedback['.$key.']',
                                                                                $unitid,
                                                                                $unit->feedback,
                                                                                $unit->feedbackformat);
                $key++;
            } else {
                $question->otherunitfeedback = $this->unit_feedback_html_element_preprocess('otherunitfeedback',
                                                                                $unitid,
                                                                                $unit->feedback,
                                                                                $unit->feedbackformat);

            }
        }

        return $question;
    }

    protected function unit_feedback_html_element_preprocess($draftitemidkey, $unitid, $feedback, $feedbackformat) {
        // Feedback field and attached files.
        $formelementdata = array();
        $draftitemid = file_get_submitted_draft_itemid($draftitemidkey);
        $formelementdata['text'] = file_prepare_draft_area(
            $draftitemid,
            $this->context->id,
            $this->db_table_prefix(),
            'unitsfeedback',
            (!empty($unitid) ? (int) $unitid : null),
            $this->fileoptions,
            $feedback
        );
        $formelementdata['itemid'] = $draftitemid;
        $formelementdata['format'] = $feedbackformat;
        return $formelementdata;

    }

    /**
     * Add a set of form fields, obtained from get_per_answer_fields, to the form,
     * one for each existing answer, with some blanks for some new ones.
     * @param object $mform the form being built.
     * @param $label the label to use for each option.
     * @param $gradeoptions the possible grades for each answer.
     * @param int|\the $minoptions the minimum number of answer blanks to display.
     *      Default QUESTION_NUMANS_START.
     * @param int|\the $addoptions the number of answer blanks to add. Default QUESTION_NUMANS_ADD.
     * @return void
     */
    protected function add_per_unit_fields(&$mform, $label, $gradeoptions,
                                             $minoptions = QUESTION_NUMANS_START, $addoptions = QUESTION_NUMANS_ADD) {
        global $DB;
        $repeated = $this->get_per_unit_fields($mform, $label, $gradeoptions);

        if (isset($this->question->options)) {
            $countanswers = count($this->question->options->units);
            foreach ($this->question->options->units as $unit) {
                if ($unit->unit == '*') {
                    $countanswers--;
                    break;
                }
            }
        } else {
            $countanswers = 0;
        }
        if ($this->question->formoptions->repeatelements) {
            $repeatsatstart = max($minoptions, $countanswers + $addoptions);
        } else {
            $repeatsatstart = $countanswers;
        }

        $repeatedoptions = array();
        $repeatedoptions['units']['type'] = PARAM_RAW_TRIMMED;

        $this->repeat_elements($repeated, $repeatsatstart, $repeatedoptions,
            'noanswers', 'addanswers', $addoptions,
            get_string('addmorechoiceblanks', 'qtype_multichoice'));
    }

    /**
     * Add answer options for any other (wrong) answer.
     *
     * @param MoodleQuickForm $mform the form being built.
     */
    protected function add_other_unit_fields($mform) {
        $mform->addElement('header', 'otherunithdr',
            get_string('anyotherunit', 'qtype_varnumunit'));
        $mform->addElement('static', 'otherunitfraction', get_string('grade'), '0%');
        $mform->addElement('editor', 'otherunitfeedback', get_string('feedback', 'question'),
            array('rows' => 5), $this->editoroptions);
    }

    protected function add_answer_form_part($mform) {
        $this->add_per_answer_fields($mform, get_string('answerno', 'qtype_varnumericset', '{no}'),
                                                                    question_bank::fraction_options(), 2, 1);
        $this->add_per_unit_fields($mform, get_string('unitno', 'qtype_varnumunit', '{no}'),
                                                                    question_bank::fraction_options(), 2, 1);
        $this->add_other_unit_fields($mform);
    }

    protected function definition() {
        parent::definition();
        $mform = $this->_form;
        question_bank::fraction_options();
        $mform->insertElementBefore(
            $mform->createElement('select',
                                    'unitfraction',
                                    get_string('unitweighting', 'qtype_varnumunit'),
                                    $this->grade_weighting()),
            'generalfeedback'
        );
        $elrequirescinotation = $mform->getElement('requirescinotation');
        $elrequirescinotation->setLabel(get_string('requirescinotation', 'qtype_varnumunit'));
        $mform->setDefault('unitfraction', '0.1000000');
    }

    protected static function grade_weighting() {
        // define basic array of grades. This list comprises all fractions of the form:
        // a. p/q for q <= 6, 0 <= p <= q
        // b. p/10 for 0 <= p <= 10
        // c. 1/q for 1 <= q <= 10
        // d. 1/20
        $rawfractions = array(
            1.0000000,
            0.9000000,
            0.8333333,
            0.8000000,
            0.7500000,
            0.7000000,
            0.6666667,
            0.6000000,
            0.5000000,
            0.4000000,
            0.3333333,
            0.3000000,
            0.2500000,
            0.2000000,
            0.1666667,
            0.1428571,
            0.1250000,
            0.1111111,
            0.1000000,
            0.0500000,

        );

        $fractionoptions = array();

        foreach ($rawfractions as $fraction) {
            $a = new stdClass();
            $unitfraction = (1 - $fraction);
            $a->unit = (100 * $unitfraction) . '%';
            $a->num = (100 * $fraction). '%';
            $fractionoptions["$unitfraction"] = get_string('percentgradefornumandunit', 'qtype_varnumunit', $a);
        }
        return $fractionoptions;
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        $units = $data['units'];
        $unitcount = 0;
        $maxgrade = false;
        foreach ($units as $key => $unit) {
            $trimmedunit = trim($unit);
            if ($trimmedunit !== '') {
                $expression = new pmatch_expression($trimmedunit);
                if (!$expression->is_valid()) {
                    $errors["units[$key]"] = $expression->get_parse_error();
                }
                $unitcount++;
                if ($data['unitsfraction'][$key] == 1) {
                    $maxgrade = true;
                }
            } else if ($data['unitsfraction'][$key] != 0 ||
                !html_is_blank($data['unitsfeedback'][$key]['text'])) {
                $errors["units[$key]"] = get_string('unitmustbegiven', 'qtype_varnumunit');
                $unitcount++;
            }
        }
        if ($unitcount === 0) {
            $errors['units[0]'] = get_string('notenoughunits', 'qtype_varnumunit');
        }
        if ($maxgrade === false) {
            $errors['unitsfraction[0]'] = get_string('unitsfractionsnomax', 'qtype_varnumunit');
        }

        return $errors;
    }
}
