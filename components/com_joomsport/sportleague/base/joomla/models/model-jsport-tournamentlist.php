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
class modelJsportTournamentlist
{
    public $row = null;
    public $lists = null;

    public function __construct()
    {
        global $jsDatabase;
        $this->row = $jsDatabase->select('SELECT t.* '
                .'FROM '.DB_TBL_TOURNAMENT.' as t '
                . ' WHERE t.published="1"'
                .' ORDER BY t.name, t.id');
    }
    public function getRow()
    {
        return $this->row;
    }
}
