<?php

jimport('joomla.application.component.view');

class JSBaseView extends JViewLegacy
{
    /**
     * @var string A cached application's date format
     */
    protected static $_dateFormat;
    /**
     * @var JSPRO_Models A Model.
     */
    public $_model;

    protected static $_dateFormats = array(
        '%d-%m-%Y %H:%M' => 'd-m-Y H:i',
        '%d.%m.%Y %H:%M' => 'd.m.Y H:i',
        '%m-%d-%Y %I:%M %p' => 'm-d-Y g:i A',
        '%m %B, %Y %H:%M' => 'j F, Y H:i',
        '%m %B, %Y %I:%H %p' => 'j F, Y g:i A',
        '%d-%m-%Y' => 'd-m-Y',
        '%A %d %B, %Y  %H:%M' => 'l d F, Y H:i',
    );

    public function __construct(&$model)
    {
        $this->_model = $model;
        parent::__construct();
    }

    public static function getDateFormat()
    {
        //        static $model;
//        if (is_null($model)) {
//            $model = new JSPRO_Models();
//        }

        if (is_null(self::$_dateFormat)) {
            self::$_dateFormat = getJS_Config('date_format');
        }

        return self::$_dateFormat;
    }

    public static function formatDate($date, $format = null, $jsFormat = true)
    {
        if (is_null($format)) {
            if (!$format = self::getDateFormat()) {
                reset(self::$_dateFormats);
                $format = key(self::$_dateFormats);
            }
        }
        if ($jsFormat) {
            if (isset(self::$_dateFormats[$format])) {
                $format = self::$_dateFormats[$format];
            } else {
                $format = reset(self::$_dateFormats);
            }
        }

        if ($date instanceof DateTime) {
            $timestamp = $date->getTimestamp();
        } elseif (is_int($date)) {
            $timestamp = $date;
        } else {
            $timestamp = strtotime((string) $date);
        }

        //return date($format, $timestamp);
        $dt = new JDate($date);

        return $dt->format($format);
    }
}
