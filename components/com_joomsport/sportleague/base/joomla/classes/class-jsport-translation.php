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
class classJsportTranslation
{
    public static function get($field,$name, $value)
    {
        global $jsConfig;
        if($jsConfig->get('multilanguage',0) != 1){
            return $value;
        }
        $res = '';
        $lang = JFactory::getLanguage();
        $tag = $lang->getTag();
        $db		= JFactory::getDBO();
        $db->setQuery("SELECT translation FROM #__bl_translations WHERE jsfield='".addslashes($field)."' AND languageID='".$tag."' LIMIT 1");
        $translation = $db->loadResult();
        $translation = json_decode($translation, true);
        if($translation && isset($translation[$name])){
            $res =  $translation[$name];
        }

        return $res?$res:$value;
    }
}
