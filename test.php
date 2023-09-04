<?php

require_once('../../config.php');
require_once($CFG->dirroot . '/grade/lib.php');

$courseid = 4;
$gradeitemid = 6;
$userid = 761;

$conversion = \local_gugrades\grades::conversion_factory($courseid, $gradeitemid);

\local_gugrades\api::import_grade($courseid, $gradeitemid, $conversion, intval($userid));
