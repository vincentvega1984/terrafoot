<?php
/*------------------------------------------------------------------------
# JoomSport Professional 
# ------------------------------------------------------------------------
# BearDev development company 
# Copyright (C) 2011 JoomSport.com. All Rights Reserved.
# @license - http://joomsport.com/news/license.html GNU/GPL
# Websites: http://www.JoomSport.com 
# Technical Support:  Forum - http://joomsport.com/helpdesk/
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');

require_once JS_PATH_ENV.'classes'.DIRECTORY_SEPARATOR.'class-jsport-ef.php';
class classJsportExtrafields
{
    public static function getExtraFieldValue($id, $uid, $type, $season_id)
    {
        $obj = new classJsportEf($type);

        return $obj->getValue($uid, $id, $season_id);
    }
    public static function getExtraFieldList($uid, $type, $season_id)
    {
        $obj = new classJsportEf($type);

        return $obj->getList($uid, $season_id);
    }
    public static function getExtraFieldListTable($type, $table = true)
    {
        $obj = new classJsportEf($type);
        if($table){
            return $obj->getListTable();
        }else{
            return $obj->getListDisplay();
        }
    }
    public static function getExtraFieldListSQ($type)
    {
        $obj = new classJsportEf($type);
        return $obj->getListEF();
    }
}
