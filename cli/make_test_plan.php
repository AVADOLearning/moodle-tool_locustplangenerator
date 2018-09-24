<?php

/**
 * Locust plan generator.
 *
 * @author Luke Carrier <luke.carrier@avadolearning.com>
 * @copyright 2018 AVADO Learning Ltd
 */

use tool_locustplangenerator\test_plan_generator;

define('CLI_SCRIPT', true);
require_once dirname(dirname(dirname(dirname(__DIR__)))) . '/config.php';
require_once "{$CFG->libdir}/clilib.php";

list ($options, $unrecognised) = cli_get_params([
    'startdate' => null,
    'enddate' => null,
    'outputdir' => null,
    'help' => null,
], [
    's' => 'startdate',
    'e' => 'enddate',
    'o' => 'outputdir',
    'h' => 'help',
]);

if ($options['help'] || $unrecognised
        || !$options['startdate'] || !$options['enddate'] || !$options['outputdir']) {
    mtrace(get_string('maketestplan_usage', 'tool_locustplangenerator'));
}

$startdate = DateTime::createFromFormat(DateTime::ATOM, $options['startdate']);
$enddate = DateTime::createFromFormat(DateTime::ATOM, $options['enddate']);

$trace = new text_progress_trace();
$testplangenerator = new test_plan_generator($startdate, $enddate);
$testplangenerator->plan($options['outputdir'], $trace);
$trace->finished();
