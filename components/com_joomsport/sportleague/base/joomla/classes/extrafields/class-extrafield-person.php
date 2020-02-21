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
require_once JS_PATH_OBJECTS.'class-jsport-person.php';

class classExtrafieldPerson
{
    public static function getValue($ef)
    {
        $res = '';
        if($ef->fvalue){
            try{
                $obj = new classJsportPerson($ef->fvalue);
                $res = $obj->getName(true);
            }catch (Exception $e){
                
            }
        }
        return $res;
    }
}
