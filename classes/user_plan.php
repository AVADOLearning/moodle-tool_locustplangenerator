<?php

/**
 * Locust plan generator.
 *
 * @author Luke Carrier <luke.carrier@avadolearning.com>
 * @copyright 2018 AVADO Learning Ltd
 */

namespace tool_locustplangenerator;

use DateTime;

defined('MOODLE_INTERNAL') || die;

/**
 * Locate and record users.
 */
class user_plan {
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
     * Initialiser.
     *
     * @param DateTime $startdate
     * @param DateTime $enddate
     */
    public function __construct(DateTime $startdate, DateTime $enddate) {
        $this->startdate = $startdate;
        $this->enddate = $enddate;
    }

    /**
     * Generate and write the plan.
     *
     * @param string $outputfile
     *
     * @return int
     * @throws \dml_exception
     */
    public function plan($outputfile) {
        global $DB;

        $users = $DB->get_recordset_select(
                'user', 'currentlogin > :startdate AND currentlogin < :enddate',
                [
                    'startdate' => $this->startdate->getTimestamp(),
                    'enddate' => $this->enddate->getTimestamp(),
                ], 'id, username');

        $count = 0;
        $handle = fopen($outputfile, 'w');
        foreach ($users as $user) {
            $count++;
            fputcsv($handle, [
                $user->id,
                $user->username,
            ]);
        }
        fclose($handle);
        $users->close();

        return $count;
    }
}
