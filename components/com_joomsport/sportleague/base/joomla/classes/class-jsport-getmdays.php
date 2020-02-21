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
class classJsportgetmdays
{
    public static function getMdays($options)
    {
        global $jsDatabase;
        $result_array = array();
        if ($options) {
            extract($options);
        }

        if (!isset($ordering)) {
            $ordering = 'md.m_name, md.id';
        }

        if (isset($season_id) && $season_id) {
            $query = 'SELECT md.* FROM '.DB_TBL_MATCHDAY.' as md'

                .' WHERE md.s_id = '.intval($season_id)
                .' ORDER BY '.$ordering
                .(isset($limit) && $limit ? " LIMIT {$limit}" : '')
                .(isset($limit) && $limit && isset($offset) ? " OFFSET {$offset}" : '');
            $mdays = $jsDatabase->select($query);

            return $mdays;
        }
    }
}
