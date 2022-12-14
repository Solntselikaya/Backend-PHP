<?php

function formatDbNullableString($str) {
    return !is_null($str) ? "'$str'" : 'NULL';
}

?>