<?php

function _p($var) {
    echo '<pre>';
    print_r($var);
    echo '</pre>';
    die();
}

$resultDb = 'pivot_live';
$limeDb = 'limesurvey_live';
$cleanLimeDb = 'limesurvey_live_newer';
$limeTablePrefix = 'lime_';
$host = 'rds-production.cmdnaljvctrq.ap-southeast-2.rds.amazonaws.com';

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

$dbUser = 'limesurvey_live';
$dbPass = 'bjmcb^fI8q!A$%yQxkLGz7AlM!oPYqiq';

$dbUserResult = 'pivot_live';
$dbPassResult = 'Ub3^5t&p!t!51i@qX&mBBkHllOTJ9Zgl';

try {
    $pdoResult = new PDO(
        'mysql:host='.$host.';dbname='.$resultDb,
        $dbUserResult,
        $dbPassResult,
        $options
    );
} catch (PDOException $e) {
    die('Failed connection: ' . $e->getMessage());
}

$sids = [];

$stmt = $pdoResult->query('SELECT `limesurvey_id` FROM cycles_classes WHERE `limesurvey_id` > 0');
while ($row = $stmt->fetch()) {
    $sids[] = $row['limesurvey_id'];
}
$stmt = $pdoResult->query('SELECT `self_sid` FROM users WHERE `self_sid` > 0');
while ($row = $stmt->fetch()) {
    $sids[] = $row['self_sid'];
}

echo 'count sids ='.count($sids);
echo "\n";

try {
    $pdoLime = new PDO(
        'mysql:host='.$host.';dbname='.$limeDb,
        $dbUser,
        $dbPass,
        $options
    );
} catch (PDOException $e) {
    die('Failed connection: db:'.$limeDb.' ' . $e->getMessage());
}

try {
    $pdoCleanLime = new PDO(
        'mysql:host='.$host.';dbname='.$cleanLimeDb,
        $dbUser,
        $dbPass,
        $options
    );
} catch (PDOException $e) {
    die('Failed connection: ' . $e->getMessage());
}

//_p($pdoCleanLime);

$pdoCleanLime->exec('ALTER TABLE  '.$cleanLimeDb.'.'.$limeTablePrefix.'questions DROP `modulename`');
foreach ($sids as $sid) {
    $pdoCleanLime->exec('DROP TABLE IF EXISTS '.$cleanLimeDb.'.'.$limeTablePrefix.'survey_'.$sid.';');
    try {
        $pdoCleanLime->exec('CREATE TABLE '.$cleanLimeDb.'.'.$limeTablePrefix.'survey_'.$sid.' LIKE '.$limeDb.'.'.$limeTablePrefix.'survey_'.$sid.';');
        $pdoCleanLime->exec('INSERT '.$cleanLimeDb.'.'.$limeTablePrefix.'survey_'.$sid.' SELECT * FROM  '.$limeDb.'.'.$limeTablePrefix.'survey_'.$sid.';');
    } catch (\Exception $e) {
        echo 'failed to create table survey for sid '.$sid;
        echo "\n";
    }
    $pdoCleanLime->exec('DROP TABLE IF EXISTS '.$cleanLimeDb.'.'.$limeTablePrefix.'survey_'.$sid.'_timings;');
    try {
        $pdoCleanLime->exec('CREATE TABLE '.$cleanLimeDb.'.'.$limeTablePrefix.'survey_'.$sid.'_timings LIKE '.$limeDb.'.'.$limeTablePrefix.'survey_'.$sid.'_timings;');
        $pdoCleanLime->exec('INSERT '.$cleanLimeDb.'.'.$limeTablePrefix.'survey_'.$sid.'_timings SELECT * FROM  '.$limeDb.'.'.$limeTablePrefix.'survey_'.$sid.'_timings;');
    } catch (\Exception $e) {
        echo 'failed to create table timings for sid '.$sid;
        echo "\n";
    }
    try {
        $pdoCleanLime->exec('INSERT '.$cleanLimeDb.'.'.$limeTablePrefix.'questions SELECT * FROM  '.$limeDb.'.'.$limeTablePrefix.'questions WHERE sid = '.$sid);
    } catch (\Exception $e) {
        echo 'failed to copy questions for sid '.$sid;
        echo "\n";
    }
    try {
        $pdoCleanLime->exec('INSERT '.$cleanLimeDb.'.'.$limeTablePrefix.'surveys SELECT * FROM  '.$limeDb.'.'.$limeTablePrefix.'surveys WHERE sid = '.$sid);
    } catch (\Exception $e) {
        echo 'failed to copy survey for sid '.$sid;
        echo "\n";
    }
    try {
        $pdoCleanLime->exec('INSERT '.$cleanLimeDb.'.'.$limeTablePrefix.'surveys_languagesettings SELECT * FROM  '.$limeDb.'.'.$limeTablePrefix.'surveys_languagesettings WHERE surveyls_survey_id = '.$sid);
    } catch (\Exception $e) {
        echo 'failed to copy languagesettings for sid '.$sid;
        echo "\n";
    }
    try {
        $pdoCleanLime->exec('INSERT '.$cleanLimeDb.'.'.$limeTablePrefix.'groups SELECT * FROM  '.$limeDb.'.'.$limeTablePrefix.'groups WHERE sid = '.$sid);
    } catch (\Exception $e) {
        echo 'failed to copy groups for sid '.$sid;
        echo "\n";
    }
    try {
        $pdoCleanLime->exec('INSERT '.$cleanLimeDb.'.'.$limeTablePrefix.'permissions SELECT * FROM  '.$limeDb.'.'.$limeTablePrefix.'permissions WHERE entity_id = '.$sid);
    } catch (\Exception $e) {
        echo 'failed to copy permissions for sid '.$sid;
        echo "\n";
    }
    $questions = [];
    $stmt = $pdoLime->query('SELECT `qid` FROM '.$limeTablePrefix.'questions WHERE `sid` = ' . $sid);
    while ($row = $stmt->fetch()) {
        $questions[] = $row['qid'];
    }
    if (count($questions) > 0) {
        foreach ($questions as $question) {
            try {
                $pdoCleanLime->exec('INSERT '.$cleanLimeDb.'.'.$limeTablePrefix.'question_attributes SELECT * FROM  '.$limeDb.'.'.$limeTablePrefix.'question_attributes WHERE qid = '.$question);
            } catch (\Exception $e) {
                echo 'failed to copy question_attributes for sid '.$sid;
                echo "\n";
            }
            try {
                $pdoCleanLime->exec('INSERT '.$cleanLimeDb.'.'.$limeTablePrefix.'answers SELECT * FROM  '.$limeDb.'.'.$limeTablePrefix.'answers WHERE qid = '.$question);
            } catch (\Exception $e) {
                echo 'failed to copy answers for sid '.$sid;
                echo "\n";
            }
        }
    } else {
        echo 'no questions for sid '.$sid;
        echo "\n";
    }

}
$pdoCleanLime->exec('ALTER TABLE  '.$cleanLimeDb.'.'.$limeTablePrefix.'questions ADD `modulename` VARCHAR( 255 ) NULL DEFAULT NULL ;');



