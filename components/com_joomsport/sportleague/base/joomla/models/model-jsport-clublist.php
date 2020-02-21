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
class modelJsportClublist
{
    public $row = null;
    public $lists = null;

    public function __construct()
    {
        global $jsDatabase;
        $this->row = $jsDatabase->select('SELECT t.*, t.c_name as name '
                .'FROM '.DB_TBL_CLUB.' as t '
                .' ORDER BY t.c_name, t.id');
    }
    public function getRow()
    {
        return $this->row;
    }
}
