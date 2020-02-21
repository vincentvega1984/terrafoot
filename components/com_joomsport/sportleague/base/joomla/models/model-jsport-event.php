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
class modelJsportEvent
{
    public $event_id = null;
    public $lists = null;
    private $row = null;

    public function __construct($id)
    {
        $this->event_id = $id;

        if (!$this->event_id) {
            die('ERROR! Event ID not DEFINED');
        }
        $this->loadObject();
    }
    private function loadObject()
    {
        global $jsDatabase;
        $this->row = $jsDatabase->selectObject('SELECT * '
                .'FROM '.DB_TBL_EVENTS.''
                .' WHERE id = '.$this->event_id);
        if($this->row){
            $this->row->e_name = classJsportTranslation::get('events_'.$this->row->id, 'e_name',$this->row->e_name);
        }
    }
    public function getRow()
    {
        return $this->row;
    }
}
