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
 * Serve question type files
 *
 * @package    qtype_varnumunit
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Checks file access for varnumunit questions.
 *
 * @param stdClass $course The course the question belongs to.
 * @param stdClass $cm The course module.
 * @param context $context The context of the question.
 * @param string $filearea The file area being accessed.
 * @param array $args The arguments for the file.
 * @param bool $forcedownload Whether to force download the file.
 * @param array $options Additional options for file serving.
 */
function qtype_varnumunit_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options= []) {
    global $DB, $CFG;
    require_once($CFG->libdir . '/questionlib.php');
    question_pluginfile($course, $context, 'qtype_varnumunit', $filearea, $args, $forcedownload, $options);
}
