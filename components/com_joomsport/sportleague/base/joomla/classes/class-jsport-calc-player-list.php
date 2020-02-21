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

require_once JS_PATH_MODELS.'model-jsport-match.php';
require_once JS_PATH_MODELS.'model-jsport-season.php';
class classJsportCalcPlayerList
{
    private $match_id = null;
    private $matchObj = null;
    private $season_id = null;
    private $single = null;
    public function __construct($match_id)
    {
        $this->match_id = $match_id;
        $match = new modelJsportMatch($this->match_id);
        $this->matchObj = $match->row;
        $this->season_id = $match->getSeasonID();
        $season = new modelJsportSeason($this->season_id);
        $this->single = $season->getSingle();
        $this->recalculateColumn();
    }

    public function recalculateColumn()
    {
        global $jsDatabase;
        // get all players from both teams
        $query = 'SELECT p.id, s.team_id
                    FROM '.DB_TBL_PLAYERS.' as p,
                    '.DB_TBL_PLAYERS_TEAM." as s
                    WHERE s.confirmed='0' AND s.player_join='0' AND s.player_id = p.id
                    AND s.team_id = ".$this->matchObj->team1_id.' AND s.season_id='.$this->season_id
                .' ORDER BY p.first_name,p.last_name';
        if ($this->season_id == -1) {
            $query = 'SELECT p.id, s.team_id
                        FROM '.DB_TBL_PLAYERS.' as p,
                        '.DB_TBL_PLAYERS_TEAM." as s
                        WHERE s.confirmed='0' AND s.player_join='0' AND s.player_id = p.id
                        ORDER BY p.first_name,p.last_name";
        }

        if ($this->single == 1) {
            $query = 'SELECT p.id, 0 AS team_id
                    FROM '.DB_TBL_PLAYERS.' as p,
                    '.DB_TBL_SEASON_PLAYERS.' as s
                    WHERE s.player_id = p.id
                    AND s.player_id = '.$this->matchObj->team1_id.' AND s.season_id='.$this->season_id
                .' ORDER BY p.first_name,p.last_name';
        }

        $player1 = $jsDatabase->select($query);

        $query = 'SELECT p.id, s.team_id
                    FROM '.DB_TBL_PLAYERS.' as p,
                    '.DB_TBL_PLAYERS_TEAM." as s
                    WHERE s.confirmed='0' AND s.player_join='0' AND s.player_id = p.id
                    AND s.team_id = ".$this->matchObj->team2_id.' AND s.season_id='.$this->season_id
                .' ORDER BY p.first_name,p.last_name';
        if ($this->season_id == -1) {
            $query = 'SELECT p.id, s.team_id
                        FROM '.DB_TBL_PLAYERS.' as p,
                        '.DB_TBL_PLAYERS_TEAM." as s
                        WHERE s.confirmed='0' AND s.player_join='0' AND s.player_id = p.id
                        ORDER BY p.first_name,p.last_name";
        }
        if ($this->single == 1) {
            $query = 'SELECT p.id, 0 AS team_id
                    FROM '.DB_TBL_PLAYERS.' as p,
                    '.DB_TBL_SEASON_PLAYERS.' as s
                    WHERE s.player_id = p.id
                    AND s.player_id = '.$this->matchObj->team2_id.' AND s.season_id='.$this->season_id
                .' ORDER BY p.first_name,p.last_name';
        }
        $player2 = $jsDatabase->select($query);

        // delete from table where season_id & team_id & playerid
       /* for($intA = 0; $intA < count($player1); $intA ++){
            $query = "DELETE FROM ".DB_TBL_PLAYER_LIST." WHERE player_id = {$player1[$intA]->id} AND team_id = {$player1[$intA]->team_id} AND season_id = {$this->season_id}";
            $jsDatabase->delete($query);
        }
        for($intA = 0; $intA < count($player2); $intA ++){
            $query = "DELETE FROM ".DB_TBL_PLAYER_LIST." WHERE player_id = {$player2[$intA]->id} AND team_id = {$player2[$intA]->team_id} AND season_id = {$this->season_id}";
            $jsDatabase->delete($query);
        }*/
        // calculate all events for current player by team and season

        $query = 'SELECT * FROM '.DB_TBL_EVENTS."  WHERE player_event = '1'";
        $events = $jsDatabase->select($query);
        for ($intC = 0; $intC < count($player1); ++$intC) {
            for ($intA = 0; $intA < count($events); ++$intA) {
                $event = $events[$intA];

                $sum = ($event->result_type == 1 && $event->player_event != '2') ? 'ROUND(AVG(me.ecount),3)' : 'SUM(me.ecount)'; 
                if ($this->season_id == -1) {
                    $query = 'SELECT '.$sum.' as esum'
                            .' FROM '.DB_TBL_PLAYERS.' as p JOIN #__bl_squard as t ON (p.id=t.player_id AND t.team_id='.$player1[$intC]->team_id.')'
                            .' JOIN '.DB_TBL_MATCH." as m ON ((m.team1_id=t.team_id OR m.team2_id=t.team_id) AND m.m_played='1' AND t.match_id = m.id) JOIN #__bl_matchday as md ON (m.m_id=md.id AND md.s_id=-1)"
                            .' LEFT JOIN '.DB_TBL_MATCH_EVENTS.' as me ON (me.match_id = m.id AND me.player_id = p.id AND t.team_id=me.t_id AND '
                            .' '.($event->player_event == '2' ? '(me.e_id = '.$event->sumev1.' OR me.e_id = '.$event->sumev2.')' : "me.e_id = '".intval($event->id)."'").')'
                            .' WHERE m.m_played = 1'
                            ." AND p.id = {$player1[$intC]->id}"
                            .' GROUP BY p.id';
                } elseif ($this->season_id == 0) {
                    $query = 'SELECT '.$sum.' as esum'
                            .' FROM '.DB_TBL_PLAYERS.' as p '
                            .' LEFT JOIN ('.DB_TBL_MATCHDAY.' as md'
                            .'  JOIN '.DB_TBL_MATCH.' as m ON (m.m_id=md.id AND m.m_played = 1)'
                            .'  JOIN '.DB_TBL_MATCH_EVENTS.' as me ON (me.match_id = m.id   AND '
                            .' '.($event->player_event == '2' ? '(me.e_id = '.$event->sumev1.' OR me.e_id = '.$event->sumev2.')' : "me.e_id = '".intval($event->id)."'").') )'
                            .' ON ( (m.team1_id='.$player1[$intC]->team_id.' OR m.team2_id='.$player1[$intC]->team_id.') AND '.$player1[$intC]->team_id.'=me.t_id AND me.player_id = p.id)'
                            ." WHERE p.id = {$player1[$intC]->id}"
                            .' GROUP BY p.id';
                } else {
                    $query = 'SELECT '.$sum.' as esum'
                            .' FROM '.DB_TBL_PLAYERS.' as p'
                            .' JOIN '.DB_TBL_PLAYERS_TEAM.' as t ON (p.id=t.player_id AND t.team_id='.$player1[$intC]->team_id.' '.($this->season_id ? ' AND t.season_id='.$this->season_id : ($seaslist ? ' AND t.season_id IN ('.$seaslist.')' : '')).')'
                            .' LEFT JOIN ('.DB_TBL_MATCHDAY.' as md'
                            .'  JOIN '.DB_TBL_MATCH.' as m ON (m.m_id=md.id AND m.m_played = 1)'
                            .'  JOIN '.DB_TBL_MATCH_EVENTS.' as me ON (me.match_id = m.id   AND '
                            .' '.($event->player_event == '2' ? '(me.e_id = '.$event->sumev1.' OR me.e_id = '.$event->sumev2.')' : "me.e_id = '".intval($event->id)."'").') )'
                            .' ON (md.s_id = t.season_id AND (m.team1_id=t.team_id OR m.team2_id=t.team_id) AND t.team_id=me.t_id AND me.player_id = p.id)'
                            ." WHERE t.confirmed='0'"
                            ." AND p.id = {$player1[$intC]->id}"
                            .' GROUP BY p.id';

                    if ($this->single == 1) {
                        $query = 'SELECT '.$sum.' as esum'
                                .' FROM '.DB_TBL_PLAYERS.' as p'
                                .' JOIN '.DB_TBL_SEASON_PLAYERS.' as sp ON sp.player_id = p.id'
                                .' LEFT JOIN ('.DB_TBL_MATCHDAY.' as md'
                            .'  JOIN '.DB_TBL_MATCH.' as m ON (m.m_id=md.id AND m.m_played = 1)'
                            .'  JOIN '.DB_TBL_MATCH_EVENTS.' as me ON (me.match_id = m.id   AND '
                            .' '.($event->player_event == '2' ? '(me.e_id = '.$event->sumev1.' OR me.e_id = '.$event->sumev2.')' : "me.e_id = '".intval($event->id)."'").') )'
                            .' ON (md.s_id = sp.season_id AND (m.team1_id=p.id OR m.team2_id=p.id) AND me.player_id = p.id)'
                            ." WHERE p.id = {$player1[$intC]->id} AND sp.season_id = ".$this->season_id
                            .' GROUP BY p.id';
                    }
                }

                $value = $jsDatabase->selectValue($query);
                $tblCOl = 'eventid_'.$event->id;
                $is_col = $jsDatabase->selectValue('SHOW COLUMNS FROM '.DB_TBL_PLAYER_LIST." LIKE '".$tblCOl."'");

                if (!$is_col) {
                    $jsDatabase->select('ALTER TABLE '.DB_TBL_PLAYER_LIST.' ADD `'.$tblCOl."` FLOAT NOT NULL DEFAULT  '0'");
                    //$database->query();
                }
                $query = 'INSERT INTO '.DB_TBL_PLAYER_LIST.' (player_id, team_id, season_id, `'.$tblCOl.'`)'
                        ." VALUES({$player1[$intC]->id},{$player1[$intC]->team_id},{$this->season_id},'".floatval($value)."')"
                        .' ON DUPLICATE KEY UPDATE `'.$tblCOl."` = '".floatval($value)."'";
                $jsDatabase->insert($query);
            }
        }

        for ($intC = 0; $intC < count($player2); ++$intC) {
            for ($intA = 0; $intA < count($events); ++$intA) {
                $event = $events[$intA];

                $sum = ($event->result_type == 1 && $event->player_event != '2') ? 'ROUND(AVG(me.ecount),3)' : 'SUM(me.ecount)'; 
                if ($this->season_id == -1) {
                    $query = 'SELECT '.$sum.' as esum'
                            .' FROM '.DB_TBL_PLAYERS.' as p JOIN #__bl_squard as t ON (p.id=t.player_id AND t.team_id='.$player2[$intC]->team_id.')'
                            .' JOIN '.DB_TBL_MATCH." as m ON ((m.team1_id=t.team_id OR m.team2_id=t.team_id) AND m.m_played='1' AND t.match_id = m.id) JOIN #__bl_matchday as md ON (m.m_id=md.id AND md.s_id=-1)"
                            .' LEFT JOIN '.DB_TBL_MATCH_EVENTS.' as me ON (me.match_id = m.id AND me.player_id = p.id AND t.team_id=me.t_id AND '
                            .' '.($event->player_event == '2' ? '(me.e_id = '.$event->sumev1.' OR me.e_id = '.$event->sumev2.')' : "me.e_id = '".intval($event->id)."'").')'
                            .' WHERE m.m_played = 1'
                            ." AND p.id = {$player2[$intC]->id}"
                            .' GROUP BY p.id';
                } elseif ($this->season_id == 0) {
                    $query = 'SELECT '.$sum.' as esum'
                            .' FROM '.DB_TBL_PLAYERS.' as p '
                            .' LEFT JOIN ('.DB_TBL_MATCHDAY.' as md'
                            .'  JOIN '.DB_TBL_MATCH.' as m ON (m.m_id=md.id AND m.m_played = 1)'
                            .'  JOIN '.DB_TBL_MATCH_EVENTS.' as me ON (me.match_id = m.id   AND '
                            .' '.($event->player_event == '2' ? '(me.e_id = '.$event->sumev1.' OR me.e_id = '.$event->sumev2.')' : "me.e_id = '".intval($event->id)."'").') )'
                            .' ON ( (m.team1_id='.$player2[$intC]->team_id.' OR m.team2_id='.$player2[$intC]->team_id.') AND '.$player2[$intC]->team_id.'=me.t_id AND me.player_id = p.id)'
                            ." WHERE p.id = {$player2[$intC]->id}"
                            .' GROUP BY p.id';
                } else {
                    $query = 'SELECT '.$sum.' as esum'
                            .' FROM '.DB_TBL_PLAYERS.' as p'
                            .' JOIN '.DB_TBL_PLAYERS_TEAM.' as t ON (p.id=t.player_id AND t.team_id='.$player2[$intC]->team_id.' '.($this->season_id ? ' AND t.season_id='.$this->season_id : ($seaslist ? ' AND t.season_id IN ('.$seaslist.')' : '')).')'
                            .' LEFT JOIN ('.DB_TBL_MATCHDAY.' as md'
                            .'  JOIN '.DB_TBL_MATCH.' as m ON (m.m_id=md.id AND m.m_played = 1)'
                            .'  JOIN '.DB_TBL_MATCH_EVENTS.' as me ON (me.match_id = m.id   AND '
                            .' '.($event->player_event == '2' ? '(me.e_id = '.$event->sumev1.' OR me.e_id = '.$event->sumev2.')' : "me.e_id = '".intval($event->id)."'").') )'
                            .' ON (md.s_id = t.season_id AND (m.team1_id=t.team_id OR m.team2_id=t.team_id) AND t.team_id=me.t_id AND me.player_id = p.id)'
                            ." WHERE t.confirmed='0'"
                            ." AND p.id = {$player2[$intC]->id} AND t.season_id = ".$this->season_id
                            .' GROUP BY p.id';
                    if ($this->single == 1) {
                        $query = 'SELECT '.$sum.' as esum'
                                .' FROM '.DB_TBL_PLAYERS.' as p'
                                .' JOIN '.DB_TBL_SEASON_PLAYERS.' as sp ON sp.player_id = p.id'
                                .' LEFT JOIN ('.DB_TBL_MATCHDAY.' as md'
                            .'  JOIN '.DB_TBL_MATCH.' as m ON (m.m_id=md.id AND m.m_played = 1)'
                            .'  JOIN '.DB_TBL_MATCH_EVENTS.' as me ON (me.match_id = m.id   AND '
                            .' '.($event->player_event == '2' ? '(me.e_id = '.$event->sumev1.' OR me.e_id = '.$event->sumev2.')' : "me.e_id = '".intval($event->id)."'").') )'
                            .' ON (md.s_id = sp.season_id AND (m.team1_id=p.id OR m.team2_id=p.id) AND me.player_id = p.id)'
                            ." WHERE p.id = {$player2[$intC]->id}"
                            .' GROUP BY p.id';
                    }
                }

                $value = $jsDatabase->selectValue($query);
                $tblCOl = 'eventid_'.$event->id;
                $is_col = $jsDatabase->selectValue('SHOW COLUMNS FROM '.DB_TBL_PLAYER_LIST." LIKE '".$tblCOl."'");

                if (!$is_col) {
                    $jsDatabase->select('ALTER TABLE '.DB_TBL_PLAYER_LIST.' ADD `'.$tblCOl."` FLOAT NOT NULL DEFAULT  '0'");
                    //$database->query();
                }
                $query = 'INSERT INTO '.DB_TBL_PLAYER_LIST.' (player_id, team_id, season_id, `'.$tblCOl.'`)'
                        ." VALUES({$player2[$intC]->id},{$player2[$intC]->team_id},{$this->season_id},'".floatval($value)."')"
                        .' ON DUPLICATE KEY UPDATE `'.$tblCOl."` = '".floatval($value)."'";
                $jsDatabase->insert($query);
            }
        }
    }
}
