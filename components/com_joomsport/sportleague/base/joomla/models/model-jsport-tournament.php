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
class modelJsportTournament
{
    public $row = null;
    public $lists = null;

    public function __construct($id)
    {
        global $jsDatabase;
        $this->row = $jsDatabase->selectObject('SELECT * '
                .'FROM '.DB_TBL_TOURNAMENT
                .' WHERE id = '.intval($id));
        $this->lists['slist'] = $jsDatabase->select("SELECT s.*, t.name, CONCAT(t.name, ' ', s.s_name) as tsname "
                .'FROM '.DB_TBL_SEASONS.' as s'
                .' JOIN '.DB_TBL_TOURNAMENT.' as t  ON s.t_id = t.id'
                .' WHERE t.published = "1" AND t.id = '.intval($id)
                .' ORDER BY t.name, s.ordering');
    }
    public function getRow()
    {
        return $this->row;
    }
}
