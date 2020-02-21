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
defined('_JEXEC') or die;

class JoomSportModelPlayer extends JModelItem
{
    protected $_context = 'com_joomsport.player';

    public function getObject()
    {
        require_once JPATH_COMPONENT.'/sportleague/sportleague.php';
        //return $this->_item[$pk];
            return $controllerSportLeague;
    }
}
