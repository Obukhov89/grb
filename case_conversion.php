<?php
require dirname(dirname(__FILE__)) . '/../../' . '/autoload.php';

use morphos\Gender;
use morphos\Russian\CardinalNumeralGenerator;
use morphos\Russian\Cases;
use morphos\Russian\NounDeclension;
use morphos\Russian\GeographicalNamesInflection;
use morphos\Russian\NounPluralization;
use morphos\Russian\OrdinalNumeralGenerator;


function safe_string($string)
{
    return preg_replace('~[^А-Яа-яЁё -]~u', null, trim($string));
}

foreach (array('name', 'noun', 'geographical-name') as $field) {
    if (isset($_POST[$field])) {
        $_POST[$field] = safe_string($_POST[$field]);
    }
}

$gender_labels = array(Gender::MALE => 'мужской', Gender::FEMALE => 'женский', Gender::NEUTER => 'средний');

?>
