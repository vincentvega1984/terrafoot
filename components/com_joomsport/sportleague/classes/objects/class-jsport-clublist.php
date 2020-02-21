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
require_once JS_PATH_MODELS.'model-jsport-clublist.php';
require_once JS_PATH_OBJECTS.'class-jsport-club.php';
class classJsportClublist
{
    private $object = null;
    public $lists = null;

    public function __construct()
    {
        $obj = new modelJsportClublist();
        $object = $obj->getRow();
        for ($intA = 0; $intA < count($object); ++$intA) {
            $this->object[] = new classJsportClub($object[$intA]->id);
        }
        $this->lists['options']['title'] = classJsportLanguage::get('BLFA_CLUBLIST');
    }

    public function getRow()
    {
        return $this->object;
    }
}
