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
class classExtrafieldSelect
{
    public static function getValue($ef)
    {
        global $jsDatabase;
        $query = 'SELECT sel_value FROM '.DB_TBL_EXTRA_SELECT." WHERE id='".(int) $ef->fvalue."'";

        $val = $jsDatabase->selectValue($query);
        $val = classJsportTranslation::get('fields_'.$ef->id, 'selectfield_'.$ef->fvalue,$val);
    
        return $val;
    }
}
