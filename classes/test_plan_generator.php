<?php

/**
 * Locust plan generator.
 *
 * @author Luke Carrier <luke.carrier@avadolearning.com>
 * @copyright 2018 AVADO Learning Ltd
 */

namespace tool_locustplangenerator;

use coding_exception;
use DateTime;
use dml_exception;
use progress_trace;

defined('MOODLE_INTERNAL') || die;

class test_plan_generator {
    /**
     * Start date.
     *
     * @var DateTime
     */
    protected $startdate;

    /**
     * End date.
     *
     * @var DateTime
     */
    protected $enddate;

    /**
     * Test plan generator facade.
     *
     * @param DateTime $startdate
     * @param DateTime $enddate
     */
    public function __construct(DateTime $startdate, DateTime $enddate) {
        $this->startdate = $startdate;
        $this->enddate = $enddate;
    }

    /**
     * Generate a plan.
     *
     * @param string $outputdir
     * @param progress_trace $trace
     *
     * @throws coding_exception
     * @throws dml_exception
     */
    public function plan($outputdir, progress_trace $trace) {
        $trace->output(sprintf(
                'Generating plan for activity between %s and %s',
                $this->startdate->format(DateTime::ATOM),
                $this->enddate->format(DateTime::ATOM)));

        if (file_exists($outputdir)) {
            throw new coding_exception(sprintf(
                    'Output directory %s already exists', $outputdir));
        }
        mkdir($outputdir);

        $plan = new user_plan($this->startdate, $this->enddate);
        $trace->output(sprintf(
                'Identified %d users who authenticated between start and end dates',
                $plan->plan("{$outputdir}/user.csv")));

        $plan = new enrolment_plan($this->startdate, $this->enddate);
        $trace->output(sprintf(
                'Identified %d enrolments',
                $plan->plan("{$outputdir}/enrolment.csv")));

        $plan = new course_modules_plan($this->startdate, $this->enddate);
        $trace->output(sprintf(
                'Identified %d course modules',
                $plan->plan("{$outputdir}/coursemodule.csv")));

        $trace->output(sprintf('Plan written to %s', $outputdir));
    }
}
