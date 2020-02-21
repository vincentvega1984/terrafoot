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
require_once JS_PATH_MODELS.'model-jsport-season.php';
require_once JS_PATH_OBJECTS.'class-jsport-event.php';
class classJsportgetplayers
{
    public static function getPlayersFromTeam($options, $player_id = null, $groupby = true)
    {
        global $jsDatabase;

        $result_array = array();
        if ($options) {
            extract($options);
        }

        if (!isset($ordering) || !$ordering || $ordering == ' ') {
            $ordering = 'p.first_name, p.last_name';
        }
$app = JFactory::getApplication(); 
$prefix = $app->get('dbprefix');
        $query = "SELECT COLUMN_NAME
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE table_name = '".str_replace('#__', $prefix, DB_TBL_PLAYER_LIST)."'
                AND table_schema = '".$app->get('db')."'
            AND column_name LIKE 'eventid%'";
        $cols = $jsDatabase->select($query);

        $sql_select = '';
        for ($intQ = 0; $intQ < count($cols); ++$intQ) {
            $res_type = 0;
            $Aplayer_event = 0;
            $eventid = (int) str_replace('eventid_', '', $cols[$intQ]->COLUMN_NAME);
            if($eventid){
                $query = 'SELECT result_type FROM '.DB_TBL_EVENTS." WHERE id={$eventid}";
                $res_type = $jsDatabase->selectValue($query);
                $query = 'SELECT player_event FROM '.DB_TBL_EVENTS." WHERE id={$eventid}";
                $Aplayer_event = $jsDatabase->selectValue($query);
            }
            if($res_type == 1 && $Aplayer_event != '2'){
                $sql_select .= ',COALESCE(AVG('.$cols[$intQ]->COLUMN_NAME.'),0) as '.$cols[$intQ]->COLUMN_NAME;
            }else{
                $sql_select .= ',COALESCE(SUM('.$cols[$intQ]->COLUMN_NAME.'),0) as '.$cols[$intQ]->COLUMN_NAME;
            }
            
        }
        
        $ef_sql = '';
        if(isset($sortbyextra) && $sortbyextra){
            $query = 'SELECT field_type,season_related'
                .' FROM '.DB_TBL_EXTRA_FILDS.' WHERE id=' . $sortbyextra;
            $efobj = $jsDatabase->selectObject($query);
            if($efobj->field_type == 3){
                $ef_sql = ' LEFT JOIN '
                        . '(SELECT es.sel_value as fvalue,ef1.uid, ef1.season_id'
                        . ' FROM '.DB_TBL_EXTRA_VALUES.' as ef1 '
                        . '  JOIN '.DB_TBL_EXTRA_SELECT.' as es ON es.id = ef1.fvalue AND ef1.f_id=es.fid'
                        . ' WHERE ef1.f_id='.$sortbyextra.') as ef'
                        . ' ON p.id=ef.uid';
                if($efobj->season_related){
                    $ef_sql .= ' AND ef.season_id = ' . (isset($season_id)?$season_id:0);
                }else{
                    $ef_sql .= ' AND ef.season_id = 0';
                }
                
                
            }else{
                $ef_sql = ' LEFT JOIN '.DB_TBL_EXTRA_VALUES.' as ef ON p.id=ef.uid AND ef.f_id='.$sortbyextra;
                if($efobj->season_related){
                    $ef_sql .= ' AND ef.season_id = ' . (isset($season_id)?$season_id:0);
                }else{
                    $ef_sql .= ' AND ef.season_id = 0';
                }
            }
        }

        if ((isset($season_id) && $season_id)) {
            $season = new modelJsportSeason($season_id);
            


            $query = 'SELECT * FROM '.DB_TBL_PLAYER_LIST.' '
                    .' WHERE season_id = '.$season_id;
            $table = $jsDatabase->select($query);
            if (!$table) {
                classJsportPlugins::get('generatePlayerList', array('season_id' => $season_id));

            }
            
            $single = $season->getSingle();
            if ($single == 1) {
                $query = 'SELECT DISTINCT(p.id),p.*,p.id as id,pl.*, p.id as id'
                    .$sql_select
                    .' FROM '.DB_TBL_PLAYERS.' as p'

                    .' JOIN '.DB_TBL_SEASON_PLAYERS.' as st ON st.player_id = p.id'
                    .' LEFT JOIN '.DB_TBL_PLAYER_LIST.' as pl ON p.id = pl.player_id  AND st.season_id = pl.season_id'
                    .(isset($season_id) && $season_id ? " AND pl.season_id = {$season_id}" : '')
                    .$ef_sql
                    .' WHERE 1 = 1'
                    .(isset($player_id) ? ' AND p.id = '.$player_id : '')
                    .(isset($season_id) && $season_id ? ' AND st.season_id = '.$season_id.' '.($season_id ? ' AND pl.season_id='.$season_id : '') : '')

                    .' GROUP BY p.id'
                    .' ORDER BY '.$ordering
                    .(isset($limit) && $limit ? " LIMIT {$limit}" : '')
                    .(isset($limit) && $limit && isset($offset) ? " OFFSET {$offset}" : '');

                $query_count = 'SELECT COUNT((p.id))'
                    //.$sql_select
.' FROM '.DB_TBL_PLAYERS.' as p'
                    .' JOIN '.DB_TBL_SEASON_PLAYERS.' as st ON st.player_id = p.id'
                    .' LEFT JOIN '.DB_TBL_PLAYER_LIST.' as pl ON p.id = pl.player_id  AND st.season_id = pl.season_id'
                    .(isset($season_id) && $season_id ? " AND st.season_id = {$season_id}" : '')
                    .' WHERE 1 = 1'
                    .(isset($player_id) ? ' AND p.id = '.$player_id : '')
                    .(isset($season_id) && $season_id ? ' AND st.season_id = '.$season_id : '');
            } else {
                $query = 'SELECT pl.*, p.id as id'
                    .$sql_select
                    .' FROM '.DB_TBL_PLAYERS.' as p'
                    .' JOIN '.DB_TBL_PLAYERS_TEAM.' as pt ON pt.player_id = p.id'
                    .' JOIN '.DB_TBL_SEASON_TEAMS.' as st ON pt.team_id = st.team_id'
                    .' LEFT JOIN '.DB_TBL_PLAYER_LIST.' as pl ON p.id = pl.player_id AND pl.team_id = pt.team_id AND st.season_id = pl.season_id'
                    .(isset($season_id) && $season_id ? " AND pl.season_id = {$season_id}" : '')
                    .$ef_sql
                    .' WHERE 1 = 1'
                    .(isset($player_id) ? ' AND p.id = '.$player_id : '')
                    .(isset($season_id) && $season_id ? ' AND st.season_id = '.$season_id.' '.($season_id ? ' AND pt.season_id='.$season_id : '') : '')
                    .(isset($team_id) ? ' AND pt.team_id = '.$team_id : '')
                    .(($groupby)?' GROUP BY p.id':' GROUP BY st.team_id')
                    .' ORDER BY '.$ordering
                    .(isset($limit) && $limit ? " LIMIT {$limit}" : '')
                    .(isset($limit) && $limit && isset($offset) ? " OFFSET {$offset}" : '');

                $query_count = 'SELECT COUNT((p.id))'
                    //.$sql_select
.' FROM '.DB_TBL_PLAYERS.' as p'
                    .' JOIN '.DB_TBL_PLAYERS_TEAM.' as pt ON pt.player_id = p.id'
                    .' JOIN '.DB_TBL_SEASON_TEAMS.' as st ON pt.team_id = st.team_id'
                    .' LEFT JOIN '.DB_TBL_PLAYER_LIST.' as pl ON p.id = pl.player_id AND pl.team_id = pt.team_id AND st.season_id = pl.season_id'
                    .(isset($season_id) && $season_id ? " AND pl.season_id = {$season_id}" : '')
                    .' WHERE 1 = 1'
                    .(isset($player_id) ? ' AND p.id = '.$player_id : '')
                    .(isset($season_id) && $season_id ? ' AND st.season_id = '.$season_id.' '.($season_id ? ' AND pt.season_id='.$season_id : '') : '')
                    .(isset($team_id) ? ' AND pt.team_id = '.$team_id : '');
            }
        } else {
            
            
            //calc all seasons
            $query = "SELECT s.s_id "
                    . " FROM ".DB_TBL_SEASONS." as s"
                    . " JOIN ".DB_TBL_TOURNAMENT." as t ON s.t_id=t.id"
                    . " WHERE s.published='1' AND t.published='1'"
                    . " ORDER BY s.s_name";
            $seasonsColumn = $jsDatabase->selectColumn($query);
            for($intCol= 0; $intCol < count($seasonsColumn); $intCol++){
                $query = 'SELECT * FROM '.DB_TBL_PLAYER_LIST.' '
                    .' WHERE season_id = '.$seasonsColumn[$intCol];
                $table = $jsDatabase->select($query);
                if (!$table) {
                    classJsportPlugins::get('generatePlayerList', array('season_id' => $seasonsColumn[$intCol]));

                }
            }
            //
            
            
            $query = 'SELECT DISTINCT(p.id),p.*,pl.*, p.id as id'
                    .$sql_select
                    .' FROM '.DB_TBL_PLAYERS.' as p'

                    .' LEFT JOIN '.DB_TBL_PLAYER_LIST.' as pl ON p.id = pl.player_id'
                    .(isset($season_id) && $season_id ? " AND pl.season_id = {$season_id}" : '')
                    .$ef_sql        
                    .' WHERE 1 = 1'
                    .(isset($player_id) ? ' AND p.id = '.$player_id.(($groupby)?'':'') : '')
                    .(isset($team_id) ? ' AND pl.team_id = '.$team_id : '')
                    .(($groupby)?' GROUP BY p.id':' GROUP BY pl.team_id,pl.season_id ')
                    .' ORDER BY '.$ordering
                    .(isset($limit) && $limit ? " LIMIT {$limit}" : '')
                    .(isset($limit) && $limit && isset($offset) ? " OFFSET {$offset}" : '');

            $query_count = 'SELECT COUNT(DISTINCT(p.id))'
                    //.$sql_select
.' FROM '.DB_TBL_PLAYERS.' as p'

                    .' LEFT JOIN '.DB_TBL_PLAYER_LIST.' as pl ON p.id = pl.player_id'
                    .(isset($season_id) && $season_id ? " AND pl.season_id = {$season_id}" : '')
                    .' WHERE 1 = 1'
                    .(isset($player_id) ? ' AND p.id = '.$player_id : '')
                    .(isset($team_id) ? ' AND pl.team_id = '.$team_id : '');
        }
//echo $query;die();

        $players = $jsDatabase->select($query);

        $players_count = $jsDatabase->selectValue($query_count);

        $result_array['list'] = $players;
        $result_array['count'] = $players_count;

        return $result_array;
    }

    public static function getPlayersEvents($season_id = 0)
    {
        global $jsDatabase;
        $query = 'SELECT DISTINCT(ev.id),ev.*'
                .' FROM '.DB_TBL_EVENTS.' as ev,'
                .' '.DB_TBL_MATCH_EVENTS.' as me,'
                .' '.DB_TBL_MATCH.' as m,'
                .' '.DB_TBL_MATCHDAY.' as md'
                .' WHERE (ev.id = me.e_id OR (ev.sumev1 = me.e_id OR ev.sumev2 = me.e_id))'
                .' AND me.match_id = m.id AND m.m_id=md.id '
                .($season_id ? 'AND md.s_id='.$season_id : '')
                .' AND (ev.player_event = 1 OR ev.player_event = 2)'
                .' ORDER BY ev.ordering';
        $events = $jsDatabase->select($query);

        $events_array = array();
        for ($intA = 0; $intA < count($events); ++$intA) {
            $objEvent = new classJsportEvent($events[$intA]->id);
            $events_array['eventid_'.$events[$intA]->id] = $objEvent;
        }

        return $events_array;
    }
    public static function getPlayersPlayedMatches($player_id, $team_id = 0, $season_id = 0)
    {
        global $jsDatabase;
        $query = 'SELECT SUM(played)'
                .' FROM '.DB_TBL_PLAYER_LIST
                ." WHERE player_id = {$player_id}"
                .($team_id ? ' AND team_id = '.$team_id : '')
                .($season_id ? ' AND season_id = '.$season_id : '');

        return (int) $jsDatabase->selectValue($query);
    }
}
