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
 * Locate and record course enrolments.
 */
class enrolment_plan {
    /**
     * SQL to locate user enrolments.
     *
     * @var string
     */
    const SQL = <<<SQL
SELECT
    MIN(ue.id) AS id,
    u.id AS userid,
    c.id AS courseid
FROM {course} c
INNER JOIN {enrol} e
    ON e.courseid = c.id
INNER JOIN {user_enrolments} ue
    ON ue.enrolid = e.id
INNER JOIN {user} u
    ON u.id = ue.userid
INNER JOIN {user_lastaccess} ula
    ON ula.courseid = c.id
    AND ula.userid = u.id
WHERE ula.timeaccess > :startdate1
    AND u.currentlogin > :startdate2
    AND u.currentlogin < :enddate
GROUP BY u.id, c.id
SQL;

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

        $enrolments = $DB->get_recordset_sql(static::SQL, [
            'startdate1' => $this->startdate->getTimestamp(),
            'startdate2' => $this->startdate->getTimestamp(),
            'enddate' => $this->enddate->getTimestamp(),
        ]);

        $count = 0;
        $handle = fopen($outputfile, 'w');
        foreach ($enrolments as $enrolment) {
            $count++;
            fputcsv($handle, [
                $enrolment->id,
                $enrolment->userid,
                $enrolment->courseid,
            ]);
        }
        fclose($handle);
        $enrolments->close();

        return $count;
    }
}
