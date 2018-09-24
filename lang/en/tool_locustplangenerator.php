<?php

/**
 * Locust plan generator.
 *
 * @author Luke Carrier <luke.carrier@avadolearning.com>
 * @copyright 2018 AVADO Learning Ltd
 */

defined('MOODLE_INTERNAL') || die;

/** @var string[] $string */

$string['pluginname'] = 'Locust plan generator';

$string['maketestplan_usage'] = 'Locust plan generator

Options:
    --startdate=ATOMUTCDATE
    --enddate=ATOMUTCDATE
    --outputdir=OUTPUTDIR

ATOMUTCDATEs should be supplied in the ISO-8601 (\DateTimeInterface::ATOM)
format:
    2018-09-24T00:00:00+00:00

Example:
$ php admin/tool/locustplangenerator/cli/make_test_plan.php \
          --startdate=2018-09-17T00:00:00+00:00 \
          --enddate=2018-09-24T00:00:00+00:00 \
          --outputdir=~/plan
';
