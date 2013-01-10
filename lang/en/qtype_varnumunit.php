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
 * Strings for component 'qtype_varnumunit', language 'en', branch 'MOODLE_23_STABLE'
 *
 * @package    qtype
 * @subpackage varnumunit
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$string['allgradeforunit'] = 'All of grade is for units.        No grade for the numerical part of the question.';
$string['anyotherunit'] = 'Any other unit';
$string['correctansweris'] = 'The correct numerical part of the question is: {$a}.';
$string['nogradeforunit'] = 'No grade for units.         All of grade is for the numerical part of the question.';
$string['notenoughunits'] = 'You have not entered any expressions to match units. You must enter at least one expression to match
 units';
$string['notvalidnumberprepostfound'] = 'There should be no string prefixing your answer.';
$string['percentgradefornumandunit'] = '{$a->unit} of grade is for units. {$a->num} of grade is for the numerical part of the
question.';
$string['pluginname'] = 'Variable numeric set with units';
$string['pluginname_help'] = 'In response to a question the respondent types a number and appropriate units.

This question is similar to the \'Variable numeric set\' question type but it accepts, grades and gives feedback for units too.

Numbers used in the question and used to calculate the answer are chosen from predefined sets which can be precalculated from
mathematical expressions.

All expressions are calculated at the time of question creation and values from random functions are the same for all users.';
$string['pluginname_link'] = 'question/type/varnumunit';
$string['pluginnameadding'] = 'Adding a Variable numeric set question with units';
$string['pluginnameediting'] = 'Editing a Variable numeric set question with units';
$string['pluginnamesummary'] = 'Allows a numeric response with units, question can have several \'variants\',
expressions are pre evaluated for each question variant';
$string['removespace'] = 'Remove spaces';
$string['replacedash'] = 'Replace dashes';
$string['requirescinotation'] = 'Require scientific notation and allow entry of units with superscripts';

$string['summarise_response'] = 'Number : "{$a->numeric}", Unit : "{$a->unit}"';

$string['unitmustbegiven'] = 'You have supplied a grade and / or feedback here but not specified an expression to match units with.
Enter an expression or reset the grade to zero and remove feedback.';
$string['unitno'] = 'Unit {$a}';
$string['unitsfractionsnomax'] = 'One of the units should have a score of 100% so it is possible to get full marks for the unit
part of the question.';
$string['unitweighting'] = 'Proportion of Grade for Units';
$string['value'] = 'Value';
$string['value_help'] = 'Enter values for \'Predefined variables\' here or you will see calculated values displayed here for a
\'Calculated variable\'.';

