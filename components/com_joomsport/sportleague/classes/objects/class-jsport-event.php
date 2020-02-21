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
require_once JS_PATH_MODELS.'model-jsport-event.php';

class classJsportEvent
{
    private $id = null;
    public $object = null;
    public $lists = null;

    public function __construct($id)
    {
        $this->id = $id;

        if (!$this->id) {
            die('ERROR! Event ID not DEFINED');
        }
        $this->loadObject();
    }

    private function loadObject()
    {
        $obj = new modelJsportEvent($this->id);
        $this->object = $obj->getRow();
    }

    public function getEventName()
    {
        return $this->object->e_name;
    }

    public function getEmblem($isblanked = true, $title = '')
    {
        if(!$title){
            $title = $this->getEventName();
        }
        $html = jsHelperImages::getEmblemEvents($this->object->e_img,0, '', 24, $title);
        if(!$html && !$isblanked){
            $html = $this->getEventName();
        }
        return $html;
    }
}
