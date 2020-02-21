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
class classJsportAddtag
{
    public static function addCustom($name, $value)
    {
        $doc = JFactory::getDocument();
        if(method_exists($doc, 'addCustomTag')){
            $doc->addCustomTag('<meta property="'.$name.'" content="'.htmlspecialchars(strip_tags(addslashes($value))).'"/> ');
        }
        
    }
    public static function addJS($link)
    {
        JHtml::script($link);
    }
    public static function addCSS($link)
    {
        JHtml::stylesheet($link);
    }
}
