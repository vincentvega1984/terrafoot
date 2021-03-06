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
class modelJsportSeasonlist
{
    public $row = null;
    public $lists = null;

    public function __construct($tournid = null)
    {
        global $jsDatabase;
        $this->row = $jsDatabase->select("SELECT s.*, t.name, CONCAT(t.name, ' ', s.s_name) as tsname, t.t_single "
                .'FROM '.DB_TBL_SEASONS.' as s'
                .' JOIN '.DB_TBL_TOURNAMENT.' as t  ON s.t_id = t.id'
                .' WHERE t.published="1" AND s.published="1" '
                . ($tournid ? " AND t.id = ".$tournid : "")
                .' ORDER BY t.name, s.ordering');
    }
    public function getRow()
    {
        return $this->row;
    }
}
