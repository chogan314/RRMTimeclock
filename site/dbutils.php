<?php

function verifyPunches($punchRecords) {
    $punchState = 2;
    foreach ($punchRecords as $record) {
        if ($record['punch_type'] == 'punch-in') {
            if ($punchState == 1) {
                return false;
            }
            $punchState = 1;
        } else {
            if ($punchState == 0) {
                return false;
            }
            $punchState = 0;
        }
    }
    return true;
}

?>