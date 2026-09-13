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


if (!function_exists('format_rupiah')) {
    function format_rupiah($value, $prefix = '')
    {
        if ($value === null || $value === '') {
            return '';
        }
        return $prefix . number_format((float) $value,0,',','.');
    }
}


if (!function_exists('angka')) {
    function angka($value)
    {
        if ($value === null || $value === '') {
            return '';
        }

        return number_format((float) $value,0,',','.');
    }
}


if (!function_exists('persen')) {

    function persen($value)
    {
        if ($value === null || $value === '') {
            return '0%';
        }

        return number_format((float) $value,2,',','.') . '%';
    }
}

if (!function_exists('parse_rupiah')) {
    function parse_rupiah($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) preg_replace('/[^0-9]/','',$value);
    }
}