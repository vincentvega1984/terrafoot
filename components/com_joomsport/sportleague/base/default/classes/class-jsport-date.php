<?php

class classJsportDate
{
    public static function getDate($date, $time, $format = null)
    {
        global $jsConfig;
        date_default_timezone_set('Europe/London');
        if (!$format) {
            $format = $jsConfig->get('date_format');
            $format = str_replace('%', '', $format);
        }
        $timestamp = strtotime($date.' '.$time);

        $date = date($format, $timestamp);

        return $date;
    }
}
