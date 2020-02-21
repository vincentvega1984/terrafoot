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
class classJsportPaginationcms
{
    private $pagination = null;
    public function __construct()
    {
        $this->pagination = new stdClass();
    }

    public function getLimit()
    {
        $mainframe = JFactory::getApplication();

        return $mainframe->getCfg('list_limit');
    }
    public function getOffset()
    {
        return 0;
    }
}
