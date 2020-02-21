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
class classJsportgetteams
{
    public static function getTeams($options)
    {
        global $jsDatabase;
        $result_array = array();
        if ($options) {
            extract($options);
        }

        if (!isset($ordering)) {
            $ordering = 't.t_name';
        }

        if (isset($season_id) && $season_id) {
            $query = 'SELECT t.* FROM '.DB_TBL_TEAMS.' as t,'
                .' '.DB_TBL_SEASON_TEAMS.' as st'
                .' WHERE t.id=st.team_id AND st.season_id = '.intval($season_id)
                .' ORDER BY '.$ordering
                .(isset($limit) && $limit ? " LIMIT {$limit}" : '')
                .(isset($limit) && $limit && isset($offset) ? " OFFSET {$offset}" : '');

            $query_count = 'SELECT COUNT(t.id) FROM '.DB_TBL_TEAMS.' as t,'
                .' '.DB_TBL_SEASON_TEAMS.' as st'
                .' WHERE t.id=st.team_id AND st.season_id = '.intval($season_id);
        } else {
            $query = 'SELECT t.*'

                .' FROM '.DB_TBL_TEAMS.' as t'

                .' WHERE 1 = 1'

                .' ORDER BY '.$ordering
                .(isset($limit) && $limit ? " LIMIT {$limit}" : '')
                .(isset($limit) && $limit && isset($offset) ? " OFFSET {$offset}" : '');

            $query_count = 'SELECT COUNT(t.id)'

                .' FROM '.DB_TBL_TEAMS.' as t'

                .' WHERE 1 = 1';
        }

        $teams = $jsDatabase->select($query);

        $teams_count = $jsDatabase->selectValue($query_count);

        $result_array['list'] = $teams;
        $result_array['count'] = $teams_count;

        return $result_array;
    }
}
