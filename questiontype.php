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
 * Question type class for the short answer question type.
 *
 * @package    qtype
 * @subpackage varnumunit
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/engine/lib.php');
require_once($CFG->dirroot . '/question/type/varnumunit/calculator.php');
require_once($CFG->dirroot . '/question/type/varnumericset/questiontypebase.php');

/**
 * The variable numeric set question type.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_varnumunit extends qtype_varnumeric_base {

    public function recalculate_every_time() {
        return false;
    }

    public function db_table_prefix() {
        return 'qtype_varnumunit';
    }

    public function extra_question_fields() {
        return array($this->db_table_prefix(), 'randomseed', 'requirescinotation', 'unitfraction');
    }

    protected function delete_files_in_units($questionid, $contextid) {
        global $DB;
        $fs = get_file_storage();

        $tablename = $this->db_table_prefix().'_units';
        $unitids = $DB->get_records_menu($tablename, array('questionid' => $questionid), 'id', 'id,1');
        foreach ($unitids as $unitid => $notused) {
            $fs->delete_area_files($contextid, $this->db_table_prefix(), 'unitsfeedback', $unitid);
        }
    }

    protected function move_files_in_units($questionid, $oldcontextid, $newcontextid) {
        global $DB;
        $fs = get_file_storage();
        $tablename = $this->db_table_prefix().'_units';
        $unitids = $DB->get_records_menu($tablename, array('questionid' => $questionid), 'id', 'id,1');
        foreach ($unitids as $unitid => $notused) {
            $fs->move_area_files_to_new_context($oldcontextid,
                $newcontextid, $this->db_table_prefix(), 'unitsfeedback', $unitid);
        }
    }

    public function delete_question($questionid, $contextid) {
        global $DB;
        $tablename = $this->db_table_prefix().'_units';
        $DB->delete_records($tablename, array('questionid' => $questionid));
        parent::delete_question($questionid, $contextid);
    }

    public function save_units($formdata) {
        global $DB;
        $context = $formdata->context;
        $table = $this->db_table_prefix().'_units';
        $oldunits = $DB->get_records_menu($table, array('questionid' => $formdata->id), 'id ASC', 'id, unit');
        if (empty($oldunits)) {
            $oldunits = array();
        }

        if (!empty($formdata->units)) {
            $numunits = max(array_keys($formdata->units)) + 1;
        } else {
            $numunits = 0;
        }

        for ($i = 0; $i < $numunits; $i += 1) {
            if (empty($formdata->units[$i])) {
                continue;
            }
            if (html_is_blank($formdata->unitsfeedback[$i]['text'])) {
                $formdata->unitsfeedback[$i]['text'] = '';
            }
            $this->save_unit($table,
                            $context,
                            $formdata->id,
                            $oldunits,
                            $formdata->units[$i],
                            $formdata->unitsfeedback[$i],
                            $formdata->unitsfraction[$i],
                            !empty($formdata->removespace[$i]),
                            !empty($formdata->replacedash[$i]));

        }

        if (!html_is_blank($formdata->otherunitfeedback['text'])) {
            $this->save_unit($table,
                            $context,
                            $formdata->id,
                            $oldunits,
                            '*',
                            $formdata->otherunitfeedback,
                            0,
                            false,
                            false);
        }
        // Delete any remaining old units.
        $fs = get_file_storage();
        foreach ($oldunits as $oldunitid => $oldunit) {
            $fs->delete_area_files($context->id, $this->db_table_prefix(), 'unitsfeedback', $oldunitid);
            $DB->delete_records($table, array('id' => $oldunitid));
        }
    }

    public function save_unit($table, $context, $questionid, &$oldunits, $unit, $feedback, $fraction, $removespace, $replacedash) {
        global $DB;
        // Update an existing unit if possible.
        $unitid = array_search($unit, $oldunits);
        if ($unitid === false) {
            $unitobj = new stdClass();
            $unitobj->questionid = $questionid;
            $unitobj->unit = '';
            $unitobj->feedback = '';
            $unitobj->id = $DB->insert_record($table, $unitobj);
        } else {
            unset($oldunits[$unitid]);
            $unitobj = new stdClass();
            $unitobj->questionid = $questionid;
            $unitobj->unit = '';
            $unitobj->feedback = '';
            $unitobj->id = $unitid;
        }

        $unitobj->unit = $unit;
        $unitobj->removespace = $removespace;
        $unitobj->replacedash = $replacedash;
        $unitobj->fraction = $fraction;
        $unitobj->feedback = $this->import_or_save_files($feedback, $context, $this->db_table_prefix(), 'unitsfeedback', $unit->id);
        $unitobj->feedbackformat = $feedback['format'];
        $DB->update_record($table, $unitobj);
    }

    public function save_question_options($form) {
        $parentresult = parent::save_question_options($form);
        if ($parentresult !== null) {
            // Parent function returns null if all is OK.
            return $parentresult;
        }
        $this->save_units($form);
        return null;
    }
}