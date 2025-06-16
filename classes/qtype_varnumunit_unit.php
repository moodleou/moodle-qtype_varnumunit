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

namespace qtype_varnumunit;

/**
 * Class to represent a varnumunit question unit, loaded from the qtype_varnumunit_units DB table.
 *
 * @package qtype_varnumunit
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_varnumunit_unit {

    /**
     * @var int The unique identifier for this unit.
     */
    public $id;

    /**
     * @var string pmatch expression
     */
    public $unit;

    /**
     * @var int Whether to remove spaces in the unit.
     * 0 means do not remove spaces, 1 means remove spaces.
     */
    public $spaceinunit;

    /**
     * @var string Feedback for spacing issues in the unit.
     * This text can include HTML formatting.
     */
    public $spacingfeedback;

    /**
     * @var int Format of the spacing feedback text.
     * 0 means no format, 1 means HTML format.
     */
    public $spacingfeedbackformat;

    /**
     * @var int Whether to replace dashes in the unit.
     * 0 means do not replace, 1 means replace dashes with spaces.
     */
    public $replacedash;

    /**
     * @var float The fraction of the grade for this unit.
     * This is a number between 0 and 1, where 1 means full marks.
     */
    public $fraction;

    /**
     * @var string General feedback for this unit.
     */
    public $feedback;

    /**
     * @var int Format of the feedback text.
     */
    public $feedbackformat;

    /**
     * Constructor for the qtype_varnumunit_unit class.
     *
     * @param int $id The ID of the unit.
     * @param string $unit The pmatch expression for the unit.
     * @param int $spaceinunit Whether to remove spaces in the unit.
     * @param string $spacingfeedback Feedback for spacing issues.
     * @param int $spacingfeedbackformat Format of the spacing feedback.
     * @param int $replacedash Whether to replace dashes in the unit.
     * @param float $fraction The fraction of the grade for this unit.
     * @param string $feedback General feedback for this unit.
     * @param int $feedbackformat Format of the general feedback.
     */
    public function __construct($id, $unit, $spaceinunit, $spacingfeedback, $spacingfeedbackformat, $replacedash,
        $fraction, $feedback, $feedbackformat) {
        $this->id = $id;
        $this->unit = $unit;
        $this->spaceinunit = $spaceinunit;
        $this->spacingfeedback = $spacingfeedback;
        $this->spacingfeedbackformat = $spacingfeedbackformat;
        $this->replacedash = $replacedash;
        $this->fraction = $fraction;
        $this->feedback = $feedback;
        $this->feedbackformat = $feedbackformat;
    }
}
