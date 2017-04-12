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

function validateName($name, $allowEmpty = false) {
    if ($allowEmpty && $name == "") {
        return true;
    }
    return preg_match('/^\w{1,60}$/', $name) === 1;
}

function validatePassword($password, $allowEmpty = false) {
    if ($allowEmpty && $password == "") {
        return true;
    }
    return preg_match('/^\S{8,}$/', $password) === 1;
}

function validateDate($date, $allowEmpty = false) {
    if ($allowEmpty && $date == "") {
        return true;
    }
    return preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) === 1;
}

function validateTime($time, $allowEmpty = false) {
    if ($allowEmpty && $time == "") {
        return true;
    }
    return preg_match('/^\d{2}:\d{2}$/', $time) === 1;
}

function validateNumber($number, $allowEmpty = false) {
    if ($allowEmpty && $number == "") {
        return true;
    }
    return preg_match('/^\d+$/', $number) === 1;
}

function validateSplitName($splitName, $allowEmpty = false) {
    if ($allowEmpty && $splitName == "") {
        return true;
    }
    return preg_match('/^\w{1,60}\s*,\s*\w{1,60}$/', $splitName) === 1;
}

function validateNameWithSpaces($name, $allowEmpty = false) {
    if ($allowEmpty && $name == "") {
        return true;
    }
    return preg_match("/^[\w\s]{1,60}$/", $name) === 1;
}

function formatName($name) {
    return ucwords(strtolower($name));
}

?>