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
defined('_JEXEC') or die('Restricted access');

require_once JS_PATH_ENV.'classes'.DIRECTORY_SEPARATOR.'class-jsport-getmatches.php';
class classJsportMatches
{
    public $params = array();
    private $available_params = array('season_id',
        'matchday_id',
        'team_id',
        'date_from',
        'date_to',
        'played',
        'ordering',
        'place',
        'limit',
        'offset',
        'group_id',
        'date_exclude');
    public function __construct($params)
    {
        if (count($params)) {
            foreach ($params as $key => $value) {
                if (in_array($key, $this->available_params)) {
                    $this->params[$key] = $value;
                }
            }
        }
    }

    public function getMatchList($single = 0)
    {
        $matches = classJsportgetmatches::getMatches($this->params, $single);

        return $matches;
    }
}
