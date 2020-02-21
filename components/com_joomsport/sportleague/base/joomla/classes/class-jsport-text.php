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
class classJsportText
{
    public static function getFormatedText($text)
    {
        JPluginHelper::importPlugin('content');
        $dispatcher = JDispatcher::getInstance();
        $results = @$dispatcher->trigger('onContentPrepare', array('content',null,null));

        return JHTML::_('content.prepare', $text);
    }
}
