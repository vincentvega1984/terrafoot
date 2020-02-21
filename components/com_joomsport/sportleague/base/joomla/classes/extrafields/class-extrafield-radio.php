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
class classExtrafieldRadio
{
    public static function getValue($ef)
    {
        $html = '';
        if ($ef->fvalue != '') {
            $html = $ef->fvalue ? classJsportLanguage::get('JYES') : classJsportLanguage::get('JNO');
        }

        return $html;
    }
}
