<?php

if (! function_exists('checklist')) {

    function checklist($value, $expected, $label)
    {
        return sprintf(
            '%s %s',
            $value == $expected
                ? '<i class="fa-regular fa-square-check fa-lg text-success"></i>'
                : '<i class="fa-regular fa-square fa-lg"></i>',
            e($label)
        );
    }

}