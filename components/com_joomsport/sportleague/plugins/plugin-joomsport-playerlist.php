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

class pluginJoomsportPlayerlist
{
    public static function generatePlayerList($args)
    {
        $season_id = (isset($args['season_id']) && $args['season_id']) ? $args['season_id'] : 0;
        if (!$season_id) {
            return;
        }
        new calcPlayerList($season_id);
    }
}

class calcPlayerList
{
    private $match_id = null;
    private $matchObj = null;
    private $season_id = null;
    private $single = null;
    public function __construct($season_id)
    {
        global $jsDatabase;
        require_once JS_PATH_MODELS.'model-jsport-match.php';
        require_once JS_PATH_MODELS.'model-jsport-season.php';
        $this->season_id = $season_id;
        $season = new modelJsportSeason($this->season_id);
        $this->single = $season->getSingle();
        /*$query = "SELECT m.* FROM ".DB_TBL_MATCHDAY." as md "
                 . " JOIN ".DB_TBL_MATCH." as m ON m.m_id = md.id "
                 . " WHERE md.s_id = " . $this->season_id;
        $matches = $jsDatabase->select($query);
        for($intA = 0; $intA < count($matches); $intA ++){
             $this->match_id = $matches[$intA]->id;
             $match = new modelJsportMatch($this->match_id);
             $this->matchObj = $match->row;

             $this->recalculateColumn();
        }*/
        $this->recalculateColumn();
    }
    public function recalculateColumn()
    {
        global $jsDatabase, $jsConfig;
        $timeline = $jsConfig->get('jstimeline');
        $timeline = json_decode($timeline,true);
        $duration = (isset($timeline['duration']))?intval($timeline['duration']):0;
        if ($this->single == '1') {
            $query = 'SELECT p.id, 0 AS team_id'
                .' FROM '.DB_TBL_PLAYERS.' as p'
                .' JOIN '.DB_TBL_SEASON_PLAYERS.' as t ON t.player_id = p.id'
                ." WHERE t.season_id = {$this->season_id}";
        } else {
            $query = 'SELECT p.id, s.team_id '
                .' FROM '.DB_TBL_PLAYERS.' as p'
                .' JOIN '.DB_TBL_PLAYERS_TEAM.' as s ON p.id = s.player_id'
                .' JOIN '.DB_TBL_SEASON_TEAMS.' as t ON t.team_id = s.team_id AND s.season_id = t.season_id'
                ." WHERE s.season_id = {$this->season_id}";
        }
        $players = $jsDatabase->select($query);

        $query = 'SELECT * FROM '.DB_TBL_EVENTS."  WHERE player_event = '1' OR player_event = '2'";
        $events = $jsDatabase->select($query);
        for ($intA = 0; $intA < count($events); ++$intA) {
            $event = $events[$intA];
            $tblCOl = 'eventid_'.$event->id;
            $is_col = $jsDatabase->selectValue('SHOW COLUMNS FROM '.DB_TBL_PLAYER_LIST." LIKE '".$tblCOl."'");

            if (!$is_col) {
                $jsDatabase->select('ALTER TABLE '.DB_TBL_PLAYER_LIST.' ADD `'.$tblCOl."` FLOAT NOT NULL DEFAULT  '0'");
                //$database->query();
            }
        }

        for ($intC = 0; $intC < count($players); ++$intC) {
            $query = 'INSERT IGNORE INTO '.DB_TBL_PLAYER_LIST.' (player_id, team_id, season_id)'
                        ." VALUES({$players[$intC]->id},{$players[$intC]->team_id},{$this->season_id})";
            $jsDatabase->insert($query);
            for ($intA = 0; $intA < count($events); ++$intA) {
                $event = $events[$intA];

                $sum = ($event->result_type == 1 && $event->player_event != '2') ? 'ROUND(AVG(me.ecount),3)' : 'SUM(me.ecount)'; 
                if ($this->single == '1') {
                    $query = 'SELECT '.$sum.' as esum'
                        .' FROM '.DB_TBL_PLAYERS.' as p'
                        .' JOIN '.DB_TBL_SEASON_PLAYERS.' as s ON (p.id = s.player_id AND s.season_id='.$this->season_id.')'
                        .' LEFT JOIN ('.DB_TBL_MATCHDAY.' as md'
                        .'  JOIN '.DB_TBL_MATCH.' as m ON (m.m_id=md.id AND m.m_played = 1)'
                        .'  JOIN '.DB_TBL_MATCH_EVENTS.' as me ON (me.match_id = m.id   AND '
                        .' '.($event->player_event == '2' ? '(me.e_id = '.$event->sumev1.' OR me.e_id = '.$event->sumev2.')' : "me.e_id = '".intval($event->id)."'").') )'
                        .' ON (md.s_id = s.season_id AND (m.team1_id=p.id OR m.team2_id=p.id) AND p.id=me.player_id)'
                        ." WHERE p.id = {$players[$intC]->id}"
                        .' GROUP BY p.id';
                } else {
                    $query = 'SELECT '.$sum.' as esum'
                        .' FROM '.DB_TBL_PLAYERS.' as p'
                        .' JOIN '.DB_TBL_PLAYERS_TEAM.' as t ON (p.id=t.player_id AND t.team_id='.$players[$intC]->team_id." AND t.season_id={$this->season_id} )"
                        .' LEFT JOIN ('.DB_TBL_MATCHDAY.' as md'
                        .'  JOIN '.DB_TBL_MATCH.' as m ON (m.m_id=md.id AND m.m_played = 1)'
                        .'  JOIN '.DB_TBL_MATCH_EVENTS.' as me ON (me.match_id = m.id   AND '
                        .' '.($event->player_event == '2' ? '(me.e_id = '.$event->sumev1.' OR me.e_id = '.$event->sumev2.')' : "me.e_id = '".intval($event->id)."'").') )'
                        .' ON (md.s_id = t.season_id AND (m.team1_id=t.team_id OR m.team2_id=t.team_id) AND t.team_id=me.t_id AND me.player_id = p.id)'
                        ." WHERE t.confirmed='0'"
                        ." AND p.id = {$players[$intC]->id}"
                        .' GROUP BY p.id';
                }

                $value = $jsDatabase->selectValue($query);

                $tblCOl = 'eventid_'.$event->id;
                $query = 'INSERT INTO '.DB_TBL_PLAYER_LIST.' (player_id, team_id, season_id, `'.$tblCOl.'`)'
                        ." VALUES({$players[$intC]->id},{$players[$intC]->team_id},{$this->season_id},'".floatval($value)."')"
                        .' ON DUPLICATE KEY UPDATE `'.$tblCOl."` = '".floatval($value)."'";
                $jsDatabase->insert($query);
            }

            //played matches
            if ($this->single == 1) {
                $query = 'SELECT COUNT(m.id)'
                        .' FROM '.DB_TBL_MATCH.' as m,'
                        .' '.DB_TBL_MATCHDAY.' as md'
                        .' WHERE md.id=m.m_id AND md.s_id='.$this->season_id
                        .' AND (m.team1_id = '.$players[$intC]->id.' OR m.team2_id = '.$players[$intC]->id.')'
                        ." AND m.team1_id > 0 AND m.team2_id > 0"
                        ." AND m.m_played='1'";
                $mplayed = (int) $jsDatabase->selectValue($query);
                $mplayed_in = 0;
                $mplayed_out = 0;
                $played_min = 0;
            } else {
                $query = 'SELECT COUNT(m.id)'
                        .' FROM '.DB_TBL_SQUARD.' as s,'
                        .' '.DB_TBL_MATCH.' as m,'
                        .' '.DB_TBL_MATCHDAY.' as md'
                        .' WHERE md.id=m.m_id AND md.s_id='.$this->season_id
                        .' AND m.id=s.match_id AND s.team_id = '.$players[$intC]->team_id
                        ." AND m.m_played='1' AND s.mainsquard='1'"
                        .' AND s.player_id='.$players[$intC]->id;
                $mplayed = (int) $jsDatabase->selectValue($query);

                $query = 'SELECT COUNT(m.id)'
                        .' FROM '.DB_TBL_SUBSIN.' as s,'
                        .' '.DB_TBL_MATCH.' as m,'
                        .' '.DB_TBL_MATCHDAY.' as md'
                        .' WHERE md.id=m.m_id AND md.s_id='.$this->season_id
                        .' AND m.id=s.match_id AND s.team_id = '.$players[$intC]->team_id
                        ." AND m.m_played='1'"
                        .' AND s.player_in='.$players[$intC]->id;
                $mplayed_in = (int) $jsDatabase->selectValue($query);
                
                $query = 'SELECT COUNT(m.id)'
                        .' FROM '.DB_TBL_SUBSIN.' as s,'
                        .' '.DB_TBL_MATCH.' as m,'
                        .' '.DB_TBL_MATCHDAY.' as md'
                        .' WHERE md.id=m.m_id AND md.s_id='.$this->season_id
                        .' AND m.id=s.match_id AND s.team_id = '.$players[$intC]->team_id
                        ." AND m.m_played='1'"
                        .' AND s.player_out='.$players[$intC]->id
                        .' AND s.player_in != 0';
                $mplayed_out = (int) $jsDatabase->selectValue($query);
                
                $played_min = $this->calcMinutes($players[$intC]->team_id, $players[$intC]->id, $duration);
            }
            $query = 'UPDATE '.DB_TBL_PLAYER_LIST.' SET played = '.($mplayed + $mplayed_in)
                    .", career_lineup = ".intval($mplayed)
                    .", career_subsin = ".intval($mplayed_in)
                    .", career_subsout = ".intval($mplayed_out)
                    .", career_minutes = ".intval($played_min)
                    ." WHERE player_id = {$players[$intC]->id} AND team_id = {$players[$intC]->team_id} AND season_id = {$this->season_id}";
            $jsDatabase->insert($query);
        }


    } 
    public function calcMinutes($team_id, $player_id, $duration){
        global $jsDatabase, $jsConfig;
        $kick_events = $jsConfig->get('kick_events');
        if($kick_events){
            $kick_events = json_decode($kick_events, true);
        }
        $played_minutes = 0;
        if(!$duration){
            return $played_minutes;
        }    
        $query = 'SELECT m.id,m.options,s.mainsquard'
                .' FROM '.DB_TBL_SQUARD.' as s,'
                .' '.DB_TBL_MATCH.' as m,'
                .' '.DB_TBL_MATCHDAY.' as md'
                .' WHERE md.id=m.m_id AND md.s_id='.$this->season_id
                .' AND m.id=s.match_id AND s.team_id = '.$team_id
                ." AND m.m_played='1'"
                .' AND s.player_id='.$player_id;
        $matches = $jsDatabase->select($query);
        for($intA=0; $intA < count($matches); $intA++){
            $match = $matches[$intA];
            $match_duration = $duration;
            $moptions = json_decode($match->options, true);

            if(isset($moptions['duration']) && $moptions['duration']){
                $match_duration = $moptions['duration'];
            } 
            if($match->mainsquard == 1){
                $query = 'SELECT minutes'
                        .' FROM '.DB_TBL_SUBSIN.' as s'
                        .' WHERE s.match_id = '.intval($match->id).' AND s.team_id = '.intval($team_id)
                        .' AND s.player_out='.$player_id;
                $min = (int) $jsDatabase->selectValue($query);
                if(!$min){
                    $min = $match_duration;
                    if(is_array($kick_events) && count($kick_events)){
                        $query = "SELECT minutes"
                                . " FROM ".DB_TBL_MATCH_EVENTS
                                . " WHERE match_id = ".(intval($match->id))
                                . " AND t_id = ".intval($team_id)
                                . " AND player_id = ".intval($player_id)
                                . " AND e_id IN (".implode(',', $kick_events).")"
                                . " ORDER BY minutes asc"
                                . " LIMIT 1";
                        $kickOut = (int) $jsDatabase->selectValue($query);
                        if($kickOut){
                            $min = $kickOut;
                        }
                    }
                    
                }
                $played_minutes += $min;
            }else{
                $query = 'SELECT minutes'
                        .' FROM '.DB_TBL_SUBSIN.' as s'
                        .' WHERE s.match_id = '.intval($match->id).' AND s.team_id = '.intval($team_id)
                        .' AND s.player_in='.$player_id;
                $min = (int) $jsDatabase->selectValue($query);
                if($min){
                    $query = 'SELECT minutes'
                        .' FROM '.DB_TBL_SUBSIN.' as s'
                        .' WHERE s.match_id = '.intval($match->id)
                        .' AND s.team_id = '.intval($team_id)    
                        .' AND s.player_out='.$player_id;
                    $min2 = (int) $jsDatabase->selectValue($query);
                    if($min2){
                        $played_minutes += $min2 - $min;
                    }else{
                        $kickOut = 0;
                        if(is_array($kick_events)  && count($kick_events)){
                            $query = "SELECT minutes"
                                    . " FROM ".DB_TBL_MATCH_EVENTS
                                    . " WHERE match_id = ".(intval($match->id))
                                    . " AND t_id = ".intval($team_id)
                                    . " AND player_id = ".intval($player_id)
                                    . " AND e_id IN (".implode(',', $kick_events).")"
                                    . " ORDER BY minutes asc"
                                    . " LIMIT 1";
                            $kickOut = (int) $jsDatabase->selectValue($query);
                            
                        }
                        if($kickOut){
                            $played_minutes += $kickOut - $min;
                        }else{
                            $played_minutes += $match_duration - $min;
                        }
                        
                    }
                }
            } 
        }
        return $played_minutes;
    }         
}
