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
 * Variable numeric with units question type version information.
 *
 * @package   qtype_varnumunit
 * @copyright 2011 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2018050200;
$plugin->requires  = 2013101800;
$plugin->cron      = 0;
$plugin->component = 'qtype_varnumunit';
$plugin->maturity  = MATURITY_STABLE;
$plugin->release   = '1.3 for Moodle 2.6+';

$plugin->dependencies = array(
    'qtype_varnumericset' => 2014111200,
    'qtype_pmatch'        => 2014111200,
);

$plugin->outestssufficient = true;
