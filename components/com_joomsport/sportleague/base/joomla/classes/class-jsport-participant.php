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
require_once JS_PATH_OBJECTS.'class-jsport-team.php';
require_once JS_PATH_OBJECTS.'class-jsport-player.php';
class classJsportParticipant
{
    private $season_id = null;
    public $single = null;
    public function __construct($season_id, $m_single = null)
    {
        $this->season_id = $season_id;
        $obj = new classJsportSeason($this->season_id);
        if ($m_single != null && $season_id <= 0) {
            $this->single = $m_single;
        } else {
            $this->single = $obj->getSingle();
        }
    }

    public function getParticipants($group_id = null)
    {
        global $jsDatabase;

        if ($this->single) {
            $query = "SELECT t.id,bonus_point,t.first_name,t.last_name,'' as t_yteam,t.nick"
                    .' FROM '.DB_TBL_PLAYERS.' as t'
                    .' JOIN '.DB_TBL_SEASON_PLAYERS.' as st ON t.id = st.player_id'
                    .($group_id ? (' JOIN '.DB_TBL_GRTEAMS.' as gr ON gr.t_id = t.id AND gr.g_id = '.$group_id) : '')
                    .' WHERE st.season_id = '.$this->season_id
                    .' ORDER BY t.first_name, t.last_name';
        } else {
            $query = 'SELECT t.id,bonus_point,t.t_yteam,t.t_name,t.t_emblem'
                    .' FROM '.DB_TBL_TEAMS.' as t'
                    .' JOIN '.DB_TBL_SEASON_TEAMS.' as st ON t.id = st.team_id'
                    .($group_id ? (' JOIN '.DB_TBL_GRTEAMS.' as gr ON gr.t_id = t.id AND gr.g_id = '.$group_id) : '')
                    .' WHERE st.season_id = '.$this->season_id
                    .' ORDER BY t.t_name';
        }

        $partcipants = $jsDatabase->select($query);

        return $partcipants;
    }

    public function getParticipiantObj($id)
    {
        if ($id) {
            if ($this->single) {
                $obj = new classJsportPlayer($id, $this->season_id, false);
            } else {
                $obj = new classJsportTeam($id, $this->season_id, false);
            }
        } else {
            $obj = null;
        }

        return $obj;
    }
}
