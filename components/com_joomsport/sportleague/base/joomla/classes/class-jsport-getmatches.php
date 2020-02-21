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
class classJsportgetmatches
{
    public static function getMatches($options, $single = 0)
    {

        $result_array = array();

        if ($options) {
            extract($options);
        }
        global $jsDatabase;
        if (!isset($ordering)) {
            $ordering = 'md.ordering, m.m_date, m.m_time';
        }
        
        if(isset($date_from)){
            $date_from = date("Y-m-d", strtotime($date_from));
        }
        if(isset($date_to)){
            $date_to = date("Y-m-d", strtotime($date_to));
        }
        
        $season_sql = '';
        $season_sql_where = '';
        if (isset($season_id) && $season_id == 0) {
            $season_sql = ', '.DB_TBL_SEASONS.' as s,'
                    .' '.DB_TBL_TOURNAMENT.' as tr ';
            $season_sql_where = " AND ((tr.id=s.t_id AND tr.t_single = '".$single."' AND md.s_id=s.s_id AND tr.published='1' AND s.published='1') OR (md.s_id = -1 ))";

        } elseif (isset($season_id) && $season_id > 0) {
            $season_sql = ', '.DB_TBL_SEASONS.' as s,'
                    .' '.DB_TBL_TOURNAMENT.' as tr ';
            $season_sql_where = " AND ((tr.id=s.t_id AND tr.t_single = '".$single."' AND md.s_id=s.s_id AND tr.published='1' AND s.published='1'))";
        }
        
        $group_sql_where = '';
        if (isset($group_id) && $group_id != 0) {
            $query = "SELECT t_id FROM ".DB_TBL_GRTEAMS." WHERE g_id=".$group_id;
            $group_teams = $jsDatabase->selectColumn($query);
            $group_sql_where = " AND (m.team1_id IN ('".implode("','",$group_teams)."') AND m.team2_id IN ('".implode("','",$group_teams)."') ) ";
        }

        $query = 'SELECT DISTINCT(m.id),m.*,md.m_name '
                .'FROM '.DB_TBL_MATCHDAY.' as md'
                .' JOIN '.DB_TBL_MATCH.' as m ON md.id = m.m_id'
                .$season_sql
                .' WHERE 1=1'
                .$season_sql_where
                .$group_sql_where
                .(isset($season_id) && $season_id ? ' AND md.s_id = '.$season_id : '')
                .(isset($matchday_id) ? ' AND md.id = '.$matchday_id : '')
                .(isset($team_id) ? (isset($place) ? ($place == 1 ? ' AND m.team1_id = '.$team_id : ' AND m.team2_id = '.$team_id) : ' AND (m.team1_id = '.$team_id.' OR m.team2_id = '.$team_id.')') : '')
                .(isset($played) ? " AND m.m_played = '".$played."'" : '')
                .(isset($date_from) ? " AND m.m_date >= '".$date_from."'" : '')
                .(isset($date_to) ? " AND m.m_date <= '".$date_to."'" : '')
            .(isset($date_exclude) ? " AND m.m_date != '".$date_exclude."'" : '')
                .' AND (m.team1_id > 0 AND m.team2_id > 0)'
                .' ORDER BY '.$ordering
                .(isset($limit) && $limit ? " LIMIT {$limit}" : '')
                .(isset($limit) && $limit && isset($offset) ? " OFFSET {$offset}" : '');
        $matches = $jsDatabase->select($query);

        $query = 'SELECT COUNT(DISTINCT(m.id))'
                .'FROM '.DB_TBL_MATCHDAY.' as md'
                .' JOIN '.DB_TBL_MATCH.' as m ON md.id = m.m_id'
                .$season_sql
                .' WHERE 1=1'
                .$season_sql_where
                .$group_sql_where
                .(isset($season_id) && $season_id ? ' AND md.s_id = '.$season_id : '')
                .(isset($matchday_id) ? ' AND md.id = '.$matchday_id : '')
                .(isset($team_id) ? ' AND (m.team1_id = '.$team_id.' OR m.team2_id = '.$team_id.')' : '')
                .(isset($played) ? " AND m.m_played = '".$played."'" : '')
                .(isset($date_from) ? " AND m.m_date >= '".$date_from."'" : '')
                .(isset($date_to) ? " AND m.m_date <= '".$date_to."'" : '')
            .(isset($date_exclude) ? " AND m.m_date != '".$date_exclude."'" : '')
                .' AND (m.team1_id > 0 AND m.team2_id > 0)';
        $matches_count = $jsDatabase->selectValue($query);

        $result_array['list'] = $matches;
        $result_array['count'] = $matches_count;

        return $result_array;
    }
}
