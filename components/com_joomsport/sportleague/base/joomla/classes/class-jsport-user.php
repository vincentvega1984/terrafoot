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
class classJsportUser
{
    public static function getUserId()
    {
        $user = JFactory::getUser();

        return $user->id;
    }
    public static function getUserValue()
    {
    }
    public static function getUser()
    {
    }
}
