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
 * Locate and record course modules.
 */
class course_modules_plan {
    /**
     * SQL to locate CMs.
     *
     * @var string
     */
    const SQL = <<<SQL
SELECT
    cm.id AS cmid,
    m.name AS instancetype,
    cm.instance AS instanceid,
    c.id AS courseid
FROM {course} c
INNER JOIN {course_modules} cm
    ON cm.course = c.id
INNER JOIN {modules} m
    ON m.id = cm.module
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
    AND U.currentlogin < :enddate
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

        $cms = $DB->get_recordset_sql(static::SQL, [
            'startdate1' => $this->startdate->getTimestamp(),
            'startdate2' => $this->startdate->getTimestamp(),
            'enddate' => $this->enddate->getTimestamp(),
        ]);

        $count = 0;
        $handle = fopen($outputfile, 'w');
        foreach ($cms as $cm) {
            $count++;
            fputcsv($handle, [
                $cm->cmid,
                $cm->instancetype,
                $cm->instanceid,
                $cm->courseid,
            ]);
        }
        fclose($handle);
        $cms->close();

        return $count;
    }
}
