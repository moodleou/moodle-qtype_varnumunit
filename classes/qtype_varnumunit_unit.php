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
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_varnumunit_unit {

    public $id;
    /**
     * @var string pmatch expression
     */
    public $unit;
    public $spaceinunit;
    public $spacingfeedback;
    public $spacingfeedbackformat;
    public $replacedash;
    public $fraction;
    public $feedback;
    public $feedbackformat;

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
