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
class modelJsportMatchday
{
    public $matchday_id = null;
    public $lists = null;
    public $row = null;
    public $season = null;

    public function __construct($matchday_id)
    {
        $this->matchday_id = $matchday_id;

        if (!$this->matchday_id) {
            die('ERROR! Matchday ID not DEFINED');
        }
        $this->loadObject();
    }
    private function loadObject()
    {
        global $jsDatabase;
        $this->row = $jsDatabase->selectObject('SELECT * '
                .'FROM '.DB_TBL_MATCHDAY.''
                .' WHERE id = '.$this->matchday_id);
    }
    public function getObject()
    {
        return $this->row;
    }
}
