<?php
function sanitizeInput($input, $dbc) {
    $input = strip_tags(trim($input));
    $input = str_replace(array("\r", "\n"), array(" ", " "), $input);
    $input = $dbc->real_escape_string($input);
    return $input;
}

function getPostParam($key) {
    $param = '';
    $keys = array_keys($_POST);
    if (in_array($key, $keys)) {
        $param = $_POST[$key];
    }
    return $param;
}

function getGetParam($key) {
    $param = '';
    $keys = array_keys($_GET);
    if (in_array($key, $keys)) {
        $param = $_GET[$key];
    }
    return $param;
}

function splitName($name) {
    $pieces = explode(",", $name);
    $last = trim($pieces[0]);
    $first = trim($pieces[1]);
    return array($last, $first);
}
?>