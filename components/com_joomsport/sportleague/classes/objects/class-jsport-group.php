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
require_once JS_PATH_OBJECTS.'class-jsport-season.php';
class classJsportGroup
{
    private $season_id = null;

    public function __construct($id)
    {
        $this->season_id = intval($id);
    }

    public function getGroups()
    {
        global $jsDatabase;
        // check if groups enabled

        // get groups
        $query = 'SELECT id,group_name '
                .' FROM '.DB_TBL_GROUPS
                .' WHERE s_id = '.$this->season_id
                .' ORDER BY ordering,id';
        $groups = $jsDatabase->select($query);

        // return array

        return $groups;
    }
}
