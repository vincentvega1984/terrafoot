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
// No direct access.
defined('_JEXEC') or die;

require dirname(__FILE__).'/../models.php';

class edit_matchJSModel extends JSPRO_Models
{
    public $_data = null;
    public $_lists = null;
    public $season_id = null;
    public $t_single = null;
    public $t_type = null;
    public $id = null;
    public $m_id = null;
    public $tid = null;
    public $_user = null;
    /*moder rights  1 - season admin, 2 - team moderator, 3 - registered player*/
    public $acl = null;
    public function __construct($acl)
    {
        $this->acl = $acl;
        parent::__construct();
        if ($this->acl == 1) {
            $this->season_id = $this->mainframe->getUserStateFromRequest('com_joomsport.sid', 'sid', 0, 'int');
        } elseif ($this->acl == 2) {
            $this->season_id = $this->mainframe->getUserStateFromRequest('com_joomsport.moderseason', 'moderseason', 0, 'int');
        } elseif ($this->acl == 3) {
            $this->_user = JFactory::getUser();
            if ($this->_user->get('guest')) {
                $return_url = $_SERVER['REQUEST_URI'];
                $return_url = base64_encode($return_url);
                if (getVer() >= '1.6') {
                    $uopt = 'com_users';
                } else {
                    $uopt = 'com_user';
                }
                $return = 'index.php?option='.$uopt.'&view=login&return='.$return_url;

                // Redirect to a login form
                $this->mainframe->redirect($return, JText::_('BLMESS_NOT_LOGIN'));
            }
            $cid = JRequest::getVar('cid', array(0), '', 'array');
            JArrayHelper::toInteger($cid, array(0));
            if ($cid[0]) {
                $this->id = $cid[0];
            } else {
                JError::raiseError(403, JText::_('Access Forbidden'));

                return;
            }
        }

        if ($this->acl != 1) {
            $this->_lists['jsmr_mark_played'] = $this->getJS_Config('jsmr_mark_played');
            $this->_lists['jsmr_editresult_yours'] = $this->getJS_Config('jsmr_editresult_yours');
            $this->_lists['jsmr_editresult_opposite'] = $this->getJS_Config('jsmr_editresult_opposite');
            $this->_lists['jsmr_edit_playerevent_yours'] = $this->getJS_Config('jsmr_edit_playerevent_yours');
            $this->_lists['jsmr_edit_playerevent_opposite'] = $this->getJS_Config('jsmr_edit_playerevent_opposite');
            $this->_lists['jsmr_edit_matchevent_yours'] = $this->getJS_Config('jsmr_edit_matchevent_yours');
            $this->_lists['jsmr_edit_matchevent_opposite'] = $this->getJS_Config('jsmr_edit_matchevent_opposite');
            $this->_lists['jsmr_edit_squad_yours'] = $this->getJS_Config('jsmr_edit_squad_yours');
            $this->_lists['jsmr_edit_squad_opposite'] = $this->getJS_Config('jsmr_edit_squad_opposite');
        }
    }

    public function getData()
    {
        $this->_params = $this->JS_PageTitle(JText::_('BLFA_MATCH_EDIT'));
        $this->_lists['post_max_size'] = $this->getValSettingsServ('post_max_size');
        if ($this->acl == 2) {
            $this->getGlobFilters(true);

            $this->tid = JRequest::getVar('tid', 0, '', 'int');
        }

        $is_id = 0;
        $cid = JRequest::getVar('cid', array(0), '', 'array');

        JArrayHelper::toInteger($cid, array(0));
        if ($cid[0]) {
            $is_id = $cid[0];
        }

        $row = new JTableMatch($this->db);
        $row->load($is_id);
        $this->m_id = $row->m_id;
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }

        if ($this->acl != 1) {
            $this->_lists['jsmr_mark_played'] = $this->getJS_Config('jsmr_mark_played');
            $this->_lists['jsmr_editresult_yours'] = $this->getJS_Config('jsmr_editresult_yours');
            $this->_lists['jsmr_editresult_opposite'] = $this->getJS_Config('jsmr_editresult_opposite');
            $this->_lists['jsmr_edit_playerevent_yours'] = $this->getJS_Config('jsmr_edit_playerevent_yours');
            $this->_lists['jsmr_edit_playerevent_opposite'] = $this->getJS_Config('jsmr_edit_playerevent_opposite');
            $this->_lists['jsmr_edit_matchevent_yours'] = $this->getJS_Config('jsmr_edit_matchevent_yours');
            $this->_lists['jsmr_edit_matchevent_opposite'] = $this->getJS_Config('jsmr_edit_matchevent_opposite');
            $this->_lists['jsmr_edit_squad_yours'] = $this->getJS_Config('jsmr_edit_squad_yours');
            $this->_lists['jsmr_edit_squad_opposite'] = $this->getJS_Config('jsmr_edit_squad_opposite');

            if ($this->_lists['jsmr_mark_played'] == 0 && $row->m_played == 1) {
                JError::raiseError(403, JText::_('Access Forbidden'));

                return;
            }
        }

        $query = 'SELECT s_id FROM #__bl_matchday WHERE id='.$row->m_id;
        $this->db->setQuery($query);
        $s_id = $this->db->loadResult();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
        $this->season_id = $s_id ? $s_id : $this->season_id;
        $tourn = $this->getTournOpt($this->season_id);
        $this->_lists['t_type'] = $this->getMatchDayType($row);

        if ($this->season_id != -1) {
            $tourn = $this->getTournOpt($this->season_id);
            $this->t_single = $tourn->t_single;
            $this->_lists['t_type'] = $this->getMatchDayType($row);
            //$this->t_type = $tourn->t_type;
            $this->_lists['s_enbl_extra'] = $tourn->s_enbl_extra;
            $this->_lists['tourn'] = $tourn->name;
        } else {
            $this->t_single = 0;
            if ($this->acl == 3) {
                $this->t_single = 1;
            }
            $this->_lists['t_type'] = 0;
            $this->_lists['s_enbl_extra'] = 1;
        }

        if ($this->acl == 2) {
            $this->_lists['esport_invite_match'] = $this->getJS_Config('esport_invite_match');
            if ($this->_lists['esport_invite_match']) {
                $is_minv[] = JHTML::_('select.option',  0, JText::_('BLFA_ALLFROMTEAM'), 'id', 'v_name');
                $is_minv[] = JHTML::_('select.option',  1, JText::_('BLFA_ALLLINEUP'), 'id', 'v_name');

                $this->_lists['is_minv'] = JHTML::_('select.genericlist',   $is_minv, 'is_minv', 'class="selectpicker styled-long" size="1"', 'id', 'v_name', 0);
            }
        }

        if ($this->acl == 3) {
            $query = 'Select * FROM #__bl_players WHERE usr_id='.$this->_user->id;
            $this->db->setQuery($query);
            $usr = $this->db->loadObject();
            $error = $this->db->getErrorMsg();
            if ($error) {
                return JError::raiseError(500, $error);
            }
            $this->_lists['usr'] = $usr;
        }
        $this->_lists['teams_season'] = $this->teamsToModer();
        $this->getMdfilter($row->m_id);
        $this->getPlEvent($row->id);
        $this->getTeamEvents($row->id);
        $this->getParticipiant($row);
        $this->getMPlayers($row);
        $js = 'onchange="enblnp();"';
        $javascriptus = ($this->_lists['t_type'] == 1 || $this->_lists['t_type'] == 2) ? ' onchange="javascript:chng_disbl_aet();"' : '';
        $this->_lists['new_points'] = JHTML::_('select.booleanlist',  'new_points', ' '.$js, $row->new_points);
        $this->_lists['extra'] = JHTML::_('select.booleanlist',  'is_extra', ' '.$javascriptus, $row->is_extra);
        $tmp_arr[] = JHTML::_('select.option',  0, JText::_('BLBE_NOT_PLAYED'), 'id', 'value');
        $tmp_arr[] = JHTML::_('select.option',  1, JText::_('BLFA_PLAYED'), 'id', 'value');
        $query = 'SELECT id, stName as value FROM #__bl_match_statuses'
                        .' ORDER BY ordering';
        $this->db->setQuery($query);
        $selvals = $this->db->loadObjectList();
        $selvals = $this->_lists['m_played_i'] = array_merge($tmp_arr, $selvals);
        $this->_lists['m_played'] = JHTML::_('select.genericlist',   $selvals, 'm_played', 'class="inputbox" size="1"', 'id', 'value', $row->m_played);


        $query = 'SELECT p.ph_name as name,p.id as id,p.ph_filename as filename'
                .' FROM #__bl_assign_photos as ap, #__bl_photos as p'
                .' WHERE ap.photo_id = p.id AND cat_type = 3 AND cat_id = '.$row->id;
        $this->db->setQuery($query);
        $this->_lists['photos'] = $this->db->loadObjectList();
        //extra fields
        $this->_lists['ext_fields'] = $this->getBEAdditfields(2, $row->id);

        $this->getMMaps($row->id);
        $this->getMLineUp($row);
        $this->getMVenue($row->venue_id);
        if($this->acl == 1){
            $this->_lists['boxhtml'] = $this->getBoxStat($row);
        }
        $this->_lists['panel'] = $this->getePanel($this->_lists['teams_season'], 0, null, 0);
        $this->_data = $row;
    }
    public function getMVenue($venue_id)
    {
        $is_venue[] = JHTML::_('select.option',  0, JText::_('BLFA_SELVENUE'), 'id', 'v_name');
        $query = 'SELECT * FROM #__bl_venue ORDER BY v_name';
        $this->db->setQuery($query);
        $venue = $this->db->loadObjectList();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
        if (count($venue)) {
            $is_venue = array_merge($is_venue, $venue);
        }
        $this->_lists['venue'] = JHTML::_('select.genericlist',   $is_venue, 'venue_id', 'class="selectpicker" size="1"', 'id', 'v_name', $venue_id);
    }
    public function getMLineUp(&$row)
    {
        $query = 'SELECT p.id FROM #__bl_players as p, #__bl_squard as s '
                .' WHERE p.id=s.player_id AND s.match_id='.$row->id." AND s.team_id={$row->team1_id} AND s.mainsquard = '1'";
        $this->db->setQuery($query);
        $this->_lists['squard1'] = $this->db->loadColumn();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
        $query = 'SELECT p.id FROM #__bl_players as p, #__bl_squard as s WHERE p.id=s.player_id AND s.match_id='.$row->id." AND s.team_id={$row->team2_id} AND s.mainsquard = '1'";
        $this->db->setQuery($query);
        $this->_lists['squard2'] = $this->db->loadColumn();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
        $query = 'SELECT p.id FROM #__bl_players as p, #__bl_squard as s WHERE p.id=s.player_id AND s.match_id='.$row->id." AND s.team_id={$row->team1_id} AND s.mainsquard = '0'";
        $this->db->setQuery($query);
        $this->_lists['squard1_res'] = $this->db->loadColumn();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
        $query = 'SELECT p.id FROM #__bl_players as p, #__bl_squard as s WHERE p.id=s.player_id AND s.match_id='.$row->id." AND s.team_id={$row->team2_id} AND s.mainsquard = '0'";
        $this->db->setQuery($query);
        $this->_lists['squard2_res'] = $this->db->loadColumn();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
        //subs in
        $query = "SELECT s.*,CONCAT(p1.first_name,' ',p1.last_name) as plin,CONCAT(p2.first_name,' ',p2.last_name) as plout FROM #__bl_subsin as s, #__bl_players as p1, #__bl_players as p2 WHERE p1.id=s.player_in AND p2.id=s.player_out AND s.match_id=".$row->id." AND s.team_id={$row->team1_id} ORDER BY s.minutes";
        $this->db->setQuery($query);
        $this->_lists['subsin1'] = $this->db->loadObjectList();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
        $query = "SELECT s.*,CONCAT(p1.first_name,' ',p1.last_name) as plin,CONCAT(p2.first_name,' ',p2.last_name) as plout FROM #__bl_subsin as s, #__bl_players as p1, #__bl_players as p2 WHERE p1.id=s.player_in AND p2.id=s.player_out AND s.match_id=".$row->id." AND s.team_id={$row->team2_id} ORDER BY s.minutes";
        $this->db->setQuery($query);
        $this->_lists['subsin2'] = $this->db->loadObjectList();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
    }
    public function getMMaps($id)
    {
        $query = 'SELECT m.*,mp.m_score1,mp.m_score2'
                .' FROM #__bl_seas_maps as sm, #__bl_maps as m LEFT JOIN #__bl_mapscore as mp ON m.id=mp.map_id AND mp.m_id='.$id
                .' WHERE m.id=sm.map_id AND sm.season_id='.$this->season_id
                .' ORDER BY m.id';
        $this->db->setQuery($query);
        $this->_lists['maps'] = $this->db->loadObjectList();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
    }
    public function getMdfilter($m_id)
    {
        $query = 'SELECT * FROM #__bl_matchday  WHERE s_id = '.($this->season_id).' ORDER BY m_name';
        $this->db->setQuery($query);
        $mday = $this->db->loadObjectList();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
        $is_matchday[] = JHTML::_('select.option',  0, JText::_('BLFA_SELMATCHDAY'), 'id', 'm_name');
        $mdayis = array_merge($is_matchday, $mday);
        $this->_lists['mday'] = JHTML::_('select.genericlist',   $mdayis, 'm_id', 'class="selectpicker" size="1"', 'id', 'm_name', $m_id);
        if ($this->t_type == 1 || $this->acl != 1 || $this->t_type == 2) {
            $query = 'SELECT m_name FROM #__bl_matchday  WHERE id = '.($m_id);
            $this->db->setQuery($query);
            $mdayname = $this->db->loadResult();

            $this->_lists['mday'] = $mdayname;
        }
    }
    public function getPlEvent($id)
    {
        $query = "SELECT * FROM #__bl_events WHERE player_event = '1' ORDER BY e_name";
        $this->db->setQuery($query);
        $events = $this->db->loadObjectList();
        $is_event[] = JHTML::_('select.option',  0, JText::_('BLFA_SELEVENT'), 'id', 'e_name');
        if (count($events)) {
            $is_event = array_merge($is_event, $events);
        }
        $this->_lists['events'] = JHTML::_('select.genericlist',   $is_event, 'event_id', 'class="selectpicker" size="1"', 'id', 'e_name', 0);

        $query = "SELECT me.*,ev.e_name,CONCAT(p.first_name,' ',p.last_name) as p_name"
                .' FROM  #__bl_events as ev , #__bl_players as p, #__bl_match_events as me'
                ." WHERE me.player_id = p.id AND ev.player_event = '1' AND  me.e_id = ev.id AND me.match_id = ".$id
                .' ORDER BY me.eordering, CAST(me.minutes AS UNSIGNED),p.first_name,p.last_name';
        $this->db->setQuery($query);
        $this->_lists['m_events'] = $this->db->loadObjectList();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
    }
    public function getTeamEvents($id)
    {
        $query = "SELECT * FROM #__bl_events WHERE player_event = '0' ORDER BY e_name";
        $this->db->setQuery($query);
        $events = $this->db->loadObjectList();
        $is_event[] = JHTML::_('select.option',  0, JText::_('BLFA_SELEVENT'), 'id', 'e_name');
        if (count($events)) {
            $is_event = array_merge($is_event, $events);
        }
        $this->_lists['team_events'] = JHTML::_('select.genericlist',   $is_event, 'tevent_id', 'class="selectpicker" size="1"', 'id', 'e_name', 0);

        $query = 'SELECT me.*,ev.e_name,p.t_name as p_name,p.id as pid'
                .' FROM  #__bl_events as ev, #__bl_teams as p , #__bl_match_events as me'
                ." WHERE me.t_id = p.id AND ev.player_event = '0' AND  me.e_id = ev.id AND me.match_id = ".$id
                .' ORDER BY me.eordering,p.t_name';
        $this->db->setQuery($query);
        $this->_lists['t_events'] = $this->db->loadObjectList();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
    }
    public function getParticipiant(&$row)
    {
        if ($this->t_single) {
            $query = "SELECT CONCAT(first_name,' ',last_name) FROM #__bl_players WHERE id= ".$row->team1_id;
        } else {
            $query = 'SELECT t_name FROM #__bl_teams WHERE id= '.$row->team1_id;
        }
        $this->db->setQuery($query);
        $team_1 = $this->db->loadResult();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
        if ($this->t_single) {
            $query = "SELECT CONCAT(first_name,' ',last_name) FROM #__bl_players WHERE id= ".$row->team2_id;
        } else {
            $query = 'SELECT t_name FROM #__bl_teams WHERE id= '.$row->team2_id;
        }
        $this->db->setQuery($query);
        $team_2 = $this->db->loadResult();
        $this->_lists['teams1'] = $team_1;
        $this->_lists['teams2'] = $team_2;

        $is_team2[] = JHTML::_('select.option',  0, JText::_('BLFA_SELTEAM'), 'id', 'p_name');
        if ($this->acl == 1) {
            $is_team2[] = JHTML::_('select.option', $row->team1_id, $team_1, 'id', 'p_name');
            $is_team2[] = JHTML::_('select.option', $row->team2_id, $team_2, 'id', 'p_name');
        } else {
            if (($this->_lists['jsmr_edit_matchevent_opposite'] == 1 && !in_array($row->team1_id, $this->_lists['teams_season'])) || ($this->_lists['jsmr_edit_matchevent_yours'] == 1 && in_array($row->team1_id, $this->_lists['teams_season']))) {
                $is_team2[] = JHTML::_('select.option', $row->team1_id, $team_1, 'id', 'p_name');
            }
            if (($this->_lists['jsmr_edit_matchevent_opposite'] == 1 && !in_array($row->team2_id, $this->_lists['teams_season'])) || ($this->_lists['jsmr_edit_matchevent_yours'] == 1 && in_array($row->team2_id, $this->_lists['teams_season']))) {
                $is_team2[] = JHTML::_('select.option', $row->team2_id, $team_2, 'id', 'p_name');
            }
        }
        $this->_lists['sel_team'] = JHTML::_('select.genericlist',   $is_team2, 'teamz_id', 'class="selectpicker" size="1"', 'id', 'p_name', 0);
    }
    public function getMPlayers(&$row)
    {
        if ($this->t_single) {
            $is_player[] = JHTML::_('select.option',  0, JText::_('BLFA_SELPLAYER'), 'id', 'p_name');
            if ($this->acl == 1 || (($this->_lists['jsmr_edit_playerevent_opposite'] == 1 && $row->team1_id != $this->_lists['usr']->id) || ($this->_lists['jsmr_edit_playerevent_yours'] == 1 && $row->team1_id == $this->_lists['usr']->id))) {
                $is_player[] = JHTML::_('select.option',  $row->team1_id, $this->_lists['teams1'], 'id', 'p_name');
            }
            if ($this->acl == 1 || (($this->_lists['jsmr_edit_playerevent_opposite'] == 1 && $row->team2_id != $this->_lists['usr']->id) || ($this->_lists['jsmr_edit_playerevent_yours'] == 1 && $row->team2_id == $this->_lists['usr']->id))) {
                $is_player[] = JHTML::_('select.option',  $row->team2_id, $this->_lists['teams2'], 'id', 'p_name');
            }
            $ev_pl = $is_player;
            $this->_lists['players'] = JHTML::_('select.genericlist',   $ev_pl, 'playerz_id', 'class="selectpicker" size="1"', 'id', 'p_name', 0);
        } else {
            $query = "SELECT CONCAT(p.id,'*',s.team_id) as id,CONCAT(p.first_name,' ',p.last_name) as p_name,p.id as pid"
                    .' FROM #__bl_players as p, #__bl_players_team as s'
                    ." WHERE s.confirmed = '0' AND s.player_id = p.id AND s.team_id = ".$row->team1_id.' AND s.season_id='.$this->season_id
                    .' ORDER BY p.first_name,p.last_name';
            if ($this->season_id == -1) {
                $query = "SELECT DISTINCT(p.id),p.id as id,
				            CONCAT(p.first_name,' ',p.last_name) as p_name,p.id as pid FROM #__bl_players as p, #__bl_players_team as s WHERE s.confirmed = '0' AND s.player_join='0' AND s.player_id = p.id ORDER BY p.first_name,p.last_name";
            }
            $this->db->setQuery($query);

            $players_1 = $this->_lists['team1_players'] = $this->db->loadObjectList();
            $error = $this->db->getErrorMsg();
            if ($error) {
                return JError::raiseError(500, $error);
            }

            $mjarr1 = array();
            for ($i = 0; $i < count($players_1);++$i) {
                $mjarr1[] = $players_1[$i]->pid;
            }

            $query = "SELECT CONCAT(p.id,'*',s.team_id) as id,CONCAT(p.first_name,' ',p.last_name) as p_name, p.id as pid"
                        .' FROM #__bl_players as p, #__bl_squard as s'
                        .' WHERE s.player_id NOT IN('.(count($mjarr1) ? implode(',', $mjarr1) : "''").") AND p.id=s.player_id AND s.match_id='".$row->m_id."' AND s.team_id='".$row->team1_id."'"
                        .' ORDER BY p.first_name,p.last_name';
            $this->db->setQuery($query);
            $squard1 = $this->db->loadObjectList();

            if (count($squard1)) {
                $players_1 = array_merge($players_1, $squard1);
            }
            //////////////

            $query = "SELECT CONCAT(p.id,'*',s.team_id) as id,CONCAT(p.first_name,' ',p.last_name) as p_name,p.id as pid"
                    .' FROM #__bl_players as p, #__bl_players_team as s'
                    ." WHERE s.confirmed = '0' AND s.player_id = p.id AND s.team_id = ".$row->team2_id.' AND s.season_id='.$this->season_id
                    .' ORDER BY p.first_name,p.last_name';
            if ($this->season_id == -1) {
                $query = "SELECT DISTINCT(p.id),p.id as id,
				            CONCAT(p.first_name,' ',p.last_name) as p_name,p.id as pid FROM #__bl_players as p, #__bl_players_team as s WHERE s.confirmed = '0' AND s.player_join='0' AND s.player_id = p.id  ORDER BY p.first_name,p.last_name";
            }
            $this->db->setQuery($query);

            $players_2  = $this->_lists['team2_players'] = $this->db->loadObjectList();
            $error = $this->db->getErrorMsg();
            if ($error) {
                return JError::raiseError(500, $error);
            }

            $mjarr2 = array();
            for ($i = 0; $i < count($players_2);++$i) {
                $mjarr2[] = $players_2[$i]->pid;
            }

            $query = "SELECT CONCAT(p.id,'*',s.team_id) as id,CONCAT(p.first_name,' ',p.last_name) as p_name, p.id as pid"
                        .' FROM #__bl_players as p, #__bl_squard as s'
                        .' WHERE s.player_id NOT IN('.(count($mjarr2) ? implode(',', $mjarr2) : "''").") AND p.id=s.player_id AND s.match_id='".$row->m_id."' AND s.team_id='".$row->team2_id."'"
                        .' ORDER BY p.first_name,p.last_name';
            $this->db->setQuery($query);
            $squard2 = $this->db->loadObjectList();

            if (count($squard2)) {
                $players_2 = array_merge($players_2, $squard2);
            }
            /////

            $is_player[] = JHTML::_('select.option',  0, JText::_('BLFA_SELPLAYER'), 'id', 'p_name');

            $is_player[] = JHTML::_('select.optgroup',  $this->_lists['teams1'], 'id', 'p_name');

            $is_player2[] = JHTML::_('select.optgroup',  $this->_lists['teams2'], 'id', 'p_name');

            $jqre = '<select name="playerz_id" id="playerz_id"  size="1">';
            $jqre .= '<option value="">'.JText::_('BLFA_SELPLAYER').'</option>';
            if ($this->acl == 1 || (($this->_lists['jsmr_edit_playerevent_opposite'] == 1 && !in_array($row->team1_id, $this->_lists['teams_season'])) || ($this->_lists['jsmr_edit_playerevent_yours'] == 1 && in_array($row->team1_id, $this->_lists['teams_season'])))) {
                $jqre .= '<optgroup label="'.$this->_lists['teams1'].'">';
                for ($g = 0;$g < count($players_1);++$g) {
                    $jqre .= '<option value="'.$players_1[$g]->id.'*'.$row->team1_id.'">'.$players_1[$g]->p_name.'</option>';
                }
                $jqre .= '</optgroup>';
            }
            if ($this->acl == 1 || (($this->_lists['jsmr_edit_playerevent_opposite'] == 1 && !in_array($row->team2_id, $this->_lists['teams_season'])) || ($this->_lists['jsmr_edit_playerevent_yours'] == 1 && in_array($row->team2_id, $this->_lists['teams_season'])))) {
                $jqre .= '<optgroup label="'.$this->_lists['teams2'].'">';
                for ($g = 0;$g < count($players_2);++$g) {
                    $jqre .= '<option value="'.$players_2[$g]->id.'*'.$row->team2_id.'">'.$players_2[$g]->p_name.'</option>';
                }
                $jqre .= '</optgroup>';
            }
            $jqre .= '</select>';
            $this->_lists['players'] = $jqre;

            $this->getPlList($players_1, $players_2);
        }
    }
    public function getPlList($players_1, $players_2)
    {
        if (!$this->t_single) {
            $this->_lists['pl1'] = $players_1;
            $this->_lists['pl2'] = $players_2;

            $is_player_sq[] = JHTML::_('select.option',  0, JText::_('BLFA_SELPLAYER'), 'pid', 'p_name');
            if (count($players_1)) {
                $ev_pl = array_merge($is_player_sq, $players_1);
            } else {
                $ev_pl = $is_player_sq;
            }
            $this->_lists['players_team1'] = JHTML::_('select.genericlist',   $ev_pl, 'playersq1_id', 'class="selectpicker" size="1"', 'pid', 'p_name', 0);
            $this->_lists['players_team1_out'] = JHTML::_('select.genericlist',   $ev_pl, 'playersq1_out_id', 'class="selectpicker" size="1"', 'pid', 'p_name', 0);

            $this->_lists['players_team1_res'] = JHTML::_('select.genericlist',   $ev_pl, 'playersq1_id_res', 'class="selectpicker" size="1"', 'pid', 'p_name', 0);
            if (count($players_2)) {
                $ev_pl = array_merge($is_player_sq, $players_2);
            } else {
                $ev_pl = $is_player_sq;
            }
            $this->_lists['players_team2'] = JHTML::_('select.genericlist',   $ev_pl, 'playersq2_id', 'class="selectpicker" size="1"', 'pid', 'p_name', 0);
            $this->_lists['players_team2_out'] = JHTML::_('select.genericlist',   $ev_pl, 'playersq2_out_id', 'class="selectpicker" size="1"', 'pid', 'p_name', 0);

            $this->_lists['players_team2_res'] = JHTML::_('select.genericlist',   $ev_pl, 'playersq2_id_res', 'class="selectpicker" size="1"', 'pid', 'p_name', 0);
        }
    }
    public function getBoxStat($row){
        
        $home_team = $row->team1_id;
        $away_team = $row->team2_id;
        $season_id = $this->season_id;
        
        $html = '';
        
        $this->db->setQuery('SELECT * FROM #__bl_box_fields WHERE parent_id="0" AND ftype="0" AND published="1" ORDER BY ordering,name') ;
        $complexBox = $this->db->loadObjectList();
        if(!count($complexBox)){
            //$html .= JText::sprintf('BLBE_MATCHEVENTS_NORECORDS','<a href="index.php?option=com_joomsport&task=boxfields_list">','</a>');
            //return $html;
        }
        $html .= '<h4>'.($this->_lists['teams1']).'</h4>';
        $res_html = '';
        
        $query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='boxExtraField'";
        $this->db->setQuery($query);

        $efbox = (int) $this->db->loadResult();
        
        $parentB = array();
        $parentInd = 0;
        for($intA=0;$intA<count($complexBox); $intA++){
            $complexBox[$intA]->extras = array();
            $childBox = array();
            if($complexBox[$intA]->complex == '1'){
                $this->db->setQuery('SELECT * FROM #__bl_box_fields WHERE parent_id="'.$complexBox[$intA]->id.'" AND published="1" AND ftype="0" ORDER BY ordering,name') ;
                $childBox = $this->db->loadObjectList();
                for($intB=0;$intB<count($childBox); $intB++){
                    $options = json_decode($childBox[$intB]->options,true);
                    $extras = isset($options['extraVals'])?$options['extraVals']:array();
                    $childBox[$intB]->extras = $extras;
                    if(count($extras)){
                        foreach($extras as $extr){
                            array_push($complexBox[$intA]->extras, $extr);
                        }
                    }
                }
                
                if(count($childBox)){
                    $parentB[$parentInd]['object'] = $complexBox[$intA];
                    $parentB[$parentInd]['childs'] = $childBox;
                    $parentInd++;
                }
            }else{
                $options = json_decode($complexBox[$intA]->options,true);
                $extras = isset($options['extraVals'])?$options['extraVals']:array();
                $complexBox[$intA]->extras =  $extras;
                $parentB[$parentInd]['object'] = $complexBox[$intA];
                $parentB[$parentInd]['childs'] = $childBox;
                $parentInd++;
            }
            
            
            
        }
        
        $th1 = '';
        $th2 = '';
        
        $all_players = $this->_lists['team1_players'];

        if($efbox){
            $this->db->setQuery('SELECT id, sel_value as name FROM #__bl_extra_select WHERE fid="'.$efbox.'" ORDER BY eordering,sel_value', 'OBJECT') ;
            $simpleBox = $this->db->loadObjectList();
            for($intS=0;$intS<count($simpleBox);$intS++){ 
                 $query = "SELECT p.id as id,CONCAT(p.first_name,' ',p.last_name) as p_name
			            FROM #__bl_players as p, #__bl_players_team as s
                                    , #__bl_extra_values as ev 
			            WHERE s.confirmed='0' AND s.player_join='0' AND s.player_id = p.id
			            AND s.team_id = ".$home_team.' AND s.season_id='.$season_id
                                    ." AND ev.uid=p.id AND f_id={$efbox} AND ev.fvalue={$simpleBox[$intS]->id}"
                        .' ORDER BY p.first_name,p.last_name';
                $this->db->setQuery($query);
                $players = $this->db->loadObjectList();
                
                $th1=$th2='';
                $boxtd = array();
                for($intA=0;$intA<count($parentB);$intA++){
                    $box = $parentB[$intA];
                    $intChld = 0;
                    for($intB=0;$intB<count($box['childs']); $intB++){
                        if(!count($box['childs'][$intB]->extras) || in_array($simpleBox[$intS]->id, $box['childs'][$intB]->extras)){
                            $intChld++;
                            $th2 .= "<th>".$box['childs'][$intB]->name."</th>";
                            $boxtd[] =  $box['childs'][$intB]->id;
                        }
                    }

                    if(!count($box['object']->extras) || in_array($simpleBox[$intS]->id, $box['object']->extras)){

                        if($intChld){
                            $th1 .= '<th colspan="'.$intChld.'">'.$box['object']->name.'</th>';
                        }else{
                            $th1 .= '<th rowspan="2">'.$box['object']->name.'</th>';
                            $boxtd[] =  $box['object']->id;
                        }
                    }
                }
                $res_html_head = $simpleBox[$intS]->name;
                $res_html_body  = '';
                
                $res_html_head .= '<table class="jsBoxStatDIv">
                                <thead>
                                    <tr>
                                        <th rowspan="2">#</th>
                                        '.$th1.'
                                    </tr>
                                    <tr>
                                        '.$th2.'
                                    </tr>
                                </thead>
                                <tbody>';
                                    
                                    for($intPP=0;$intPP<count($players);$intPP++){
                                        $res_html_body .=  '<tr>';
                                        $res_html_body .=  '<td>';
                                        $player = ($players[$intPP]);
                                        $res_html_body .=  $player->p_name;
                                        $res_html_body .=  '</td>';
                                        $this->db->setQuery("SELECT * FROM #__bl_box_matches WHERE match_id={$row->id} AND team_id={$home_team} AND player_id={$player->id}");
                                        $player_stat = $this->db->loadObject();
                                        for($intBox=0;$intBox<count($boxtd);$intBox++){
                                            $boxfield = 'boxfield_'.$boxtd[$intBox];
                                            $res = isset($player_stat->{$boxfield})?$player_stat->{$boxfield}:'';
                                            $res_html_body .=  '<td><input data-inputboxtype="float" type="text" name="boxstat_'.$home_team.'_'.$player->id.'['.$boxtd[$intBox].']" value="'.($res).'" /></td>';
                                        }

                                        $res_html_body .=  '</tr>';
                                    }
                                    
                              
                    if($res_html_body){    
                        $res_html .= $res_html_head.$res_html_body.'</tbody></table>'; 
                    }        

            }
        }else{
            $th1=$th2='';
            $boxtd = array();
            $players = $this->_lists['team1_players'];
            for($intA=0;$intA<count($parentB);$intA++){
                $box = $parentB[$intA];
                $intChld = 0;
                for($intB=0;$intB<count($box['childs']); $intB++){
                    $intChld++;
                    $th2 .= "<th>".$box['childs'][$intB]->name."</th>";
                    $boxtd[] =  $box['childs'][$intB]->id;
                    
                }

                if($intChld){
                    $th1 .= '<th colspan="'.$intChld.'">'.$box['object']->name.'</th>';
                }else{
                    $th1 .= '<th rowspan="2">'.$box['object']->name.'</th>';
                    $boxtd[] =  $box['object']->id;
                }
                
            }
                $res_html_head = $res_html_body  = '';
                $res_html_head .= '<table class="jsBoxStatDIv">
                                <thead>
                                    <tr>
                                        <th rowspan="2">#</th>
                                        '.$th1.'
                                    </tr>
                                    <tr>
                                        '.$th2.'
                                    </tr>
                                </thead>
                                <tbody>';

                                    for($intPP=0;$intPP<count($players);$intPP++){
                                        $res_html_body .=  '<tr>';
                                        $res_html_body .=  '<td>';
                                        $player = ($players[$intPP]);
                                        $res_html_body .=  $player->p_name;
                                        $res_html_body .=  '</td>';
                                        
                                        $this->db->setQuery("SELECT * FROM #__bl_box_matches WHERE match_id={$row->id} AND team_id={$home_team} AND player_id={$player->pid}");
                                        $player_stat = $this->db->loadObject();
                                        
                                        for($intBox=0;$intBox<count($boxtd);$intBox++){
                                            $boxfield = 'boxfield_'.$boxtd[$intBox];
                                            $res = isset($player_stat->{$boxfield})?$player_stat->{$boxfield}:'';
                                            $res_html_body .=  '<td><input data-inputboxtype="float" type="text" name="boxstat_'.$home_team.'_'.$player->pid.'['.$boxtd[$intBox].']" value="'.($res).'" /></td>';
                                        }

                                        $res_html_body .=  '</tr>';
                                    }
                    if($res_html_body){    
                        $res_html .= $res_html_head.$res_html_body.'</tbody></table>'; 
                    }

        }
        if($res_html){
            $html .= $res_html;
        }else{
            if(count($all_players)){
                //$html .= JText::sprintf('BLBE_BOXSCORE_NORECORDS','<a href="index.php?option=com_joomsport&task=boxfields_list">','</a>');
            
            }else{
                //$html .= JText::_('BLBE_BOXSCORE_ASSIGN_MESSAGE');
        
            }
        }
        //away
        
        $html .=  '<h4>'.($this->_lists['teams2']).'</h4>';
        $res_html = '';
        
        $th1 = '';
        $th2 = '';
        $all_players = $this->_lists['team2_players'];

        if($efbox){
            $this->db->setQuery('SELECT id, sel_value as name FROM #__bl_extra_select WHERE fid="'.$efbox.'" ORDER BY eordering,sel_value', 'OBJECT') ;
            $simpleBox = $this->db->loadObjectList();
            for($intS=0;$intS<count($simpleBox);$intS++){ 
                 $query = "SELECT p.id as id,CONCAT(p.first_name,' ',p.last_name) as p_name
			            FROM #__bl_players as p, #__bl_players_team as s
                                    , #__bl_extra_values as ev
			            WHERE s.confirmed='0' AND s.player_join='0' AND s.player_id = p.id
			            AND s.team_id = ".$away_team.' AND s.season_id='.$season_id
                         ." AND ev.uid=p.id AND f_id={$efbox} AND ev.fvalue={$simpleBox[$intS]->id}"
                        .' ORDER BY p.first_name,p.last_name';
                $this->db->setQuery($query);
                $players = $this->db->loadObjectList();$th1=$th2='';
                $boxtd = array();
                for($intA=0;$intA<count($parentB);$intA++){
                    $box = $parentB[$intA];
                    $intChld = 0;
                    for($intB=0;$intB<count($box['childs']); $intB++){
                        if(!count($box['childs'][$intB]->extras) || in_array($simpleBox[$intS]->id, $box['childs'][$intB]->extras)){
                            $intChld++;
                            $th2 .= "<th>".$box['childs'][$intB]->name."</th>";
                            $boxtd[] =  $box['childs'][$intB]->id;
                        }
                    }

                    if(!count($box['object']->extras) || in_array($simpleBox[$intS]->id, $box['object']->extras)){

                        if($intChld){
                            $th1 .= '<th colspan="'.$intChld.'">'.$box['object']->name.'</th>';
                        }else{
                            $th1 .= '<th rowspan="2">'.$box['object']->name.'</th>';
                            $boxtd[] =  $box['object']->id;
                        }
                    }
                }
                $res_html_head = $simpleBox[$intS]->name;
                $res_html_body  = '';
                $res_html_head .= '<table class="jsBoxStatDIv">
                                <thead>
                                    <tr>
                                        <th rowspan="2">#</th>
                                        '.$th1.'
                                    </tr>
                                    <tr>
                                        '.$th2.'
                                    </tr>
                                </thead>
                                <tbody>';
                                    
                                    for($intPP=0;$intPP<count($players);$intPP++){
                                        $res_html_body .= '<tr>';
                                        $res_html_body .= '<td>';
                                        $player = ($players[$intPP]);
                                        $res_html_body .= $player->p_name;
                                        $res_html_body .= '</td>';
                                        $this->db->setQuery("SELECT * FROM #__bl_box_matches WHERE match_id={$row->id} AND team_id={$away_team} AND player_id={$player->id}");
                                        $player_stat = $this->db->loadObject();

                                        for($intBox=0;$intBox<count($boxtd);$intBox++){
                                            $boxfield = 'boxfield_'.$boxtd[$intBox];
                                            
                                            $res = isset($player_stat->{$boxfield})?$player_stat->{$boxfield}:'';
                                            $res_html_body .= '<td><input type="text" data-inputboxtype="float" name="boxstat_'.$away_team.'_'.$player->id.'['.$boxtd[$intBox].']" value="'.($res).'" /></td>';
                                        }

                                        $res_html_body .= '</tr>';
                                    }
                    if($res_html_body){    
                        $res_html .= $res_html_head.$res_html_body.'</tbody></table>'; 
                    }           

            }
        }else{
            $th1=$th2='';
            $boxtd = array();
            $players = $this->_lists['team2_players'];
            for($intA=0;$intA<count($parentB);$intA++){
                $box = $parentB[$intA];
                $intChld = 0;
                for($intB=0;$intB<count($box['childs']); $intB++){
                    $intChld++;
                    $th2 .= "<th>".$box['childs'][$intB]->name."</th>";
                    $boxtd[] =  $box['childs'][$intB]->id;
                    
                }

                if($intChld){
                    $th1 .= '<th colspan="'.$intChld.'">'.$box['object']->name.'</th>';
                }else{
                    $th1 .= '<th rowspan="2">'.$box['object']->name.'</th>';
                    $boxtd[] =  $box['object']->id;
                }
                
            }
                $res_html_body = $res_html_head  = '';
                $res_html_head .= '<table class="jsBoxStatDIv">
                                <thead>
                                    <tr>
                                        <th rowspan="2">#</th>
                                        '.$th1.'
                                    </tr>
                                    <tr>
                                        '.$th2.'
                                    </tr>
                                </thead>
                                <tbody>';

                                    for($intPP=0;$intPP<count($players);$intPP++){
                                        $res_html_body .= '<tr>';
                                        $res_html_body .= '<td>';
                                        $player = ($players[$intPP]);
                                        $res_html_body .= $player->p_name;
                                        $res_html_body .= '</td>';
                                        $this->db->setQuery("SELECT * FROM #__bl_box_matches WHERE match_id={$row->id} AND team_id={$away_team} AND player_id={$player->pid}");
                                        $player_stat = $this->db->loadObject();
                                        
                                        for($intBox=0;$intBox<count($boxtd);$intBox++){
                                            $boxfield = 'boxfield_'.$boxtd[$intBox];
                                            $res = isset($player_stat->{$boxfield})?$player_stat->{$boxfield}:'';
                                            $res_html_body .= '<td><input data-inputboxtype="float" type="text" name="boxstat_'.$away_team.'_'.$player->pid.'['.$boxtd[$intBox].']" value="'.($res).'" /></td>';
                                        }

                                        $res_html_body .= '</tr>';
                                    }
                    if($res_html_body){    
                        $res_html .= $res_html_head.$res_html_body.'</tbody></table>'; 
                    } 
        }
        if($res_html){
            $html .=  $res_html;
        }else{
            if(count($all_players)){
                //$html .= JText::sprintf('BLBE_BOXSCORE_NORECORDS','<a href="index.php?option=com_joomsport&task=boxfields_list">','</a>');
            
            }else{
                //$html .= JText::_('BLBE_BOXSCORE_ASSIGN_MESSAGE');
        
            }
            
        }
        return $html;
    }
    public function saveAdmmatch()
    {
        $post = JRequest::get('post');
        $post['match_descr'] = JRequest::getVar('match_descr', '', 'post', 'string', JREQUEST_ALLOWRAW);
        if ($this->acl != 1) {
            $tid = JRequest::getVar('tid', 0, '', 'int');
            unset($post['m_id']);
            if (!$this->_lists['jsmr_mark_played']) {
                unset($post['m_played']);
            }
        }
        $row = new JTableMatch($this->db);
        if (!$row->bind($post)) {
            JError::raiseError(500, $row->getError());
        }

        if (isset($_POST['penwin']) && count($_POST['penwin'])) {
            //var_dump($_POST['penwin']);die();
            $row->p_winner = intval($_POST['penwin'][0]);
        } else {
            $row->p_winner = 0;
        }
        if (!$row->check()) {
            JError::raiseError(500, $row->getError());
        }
        if (!$row->store()) {
            JError::raiseError(500, $row->getError());
        }
        $row->checkin();
        $me_arr = array();
        $row->load($row->id);

        if ($this->acl == 2) {
            $edit_comp = $this->getJS_Config('moder_edit_competitor');
            $teams_season_moder = $this->teamsToModer();
        }

        $query = 'SELECT s_id FROM #__bl_matchday as md, #__bl_match as m  WHERE md.id=m.m_id AND m.id = '.$row->id;
        $this->db->setQuery($query);
        $season_id = $this->db->loadResult();
        $this->season_id = $season_id;

        if ($this->season_id != -1) {
            $query = "SELECT s.s_id as id, CONCAT(t.name,' ',s.s_name) as name,t.t_single FROM #__bl_tournament as t, #__bl_seasons as s WHERE s.s_id = ".($season_id).' AND s.t_id = t.id';
            $this->db->setQuery($query);
            $tourn = $this->db->loadObjectList();
            //$lt_type = $tourn[0]->t_type;
            $lt_type = $this->_lists['t_type'];
        } else {
            $lt_type = 0;
        }

        if ($lt_type == 1 || $lt_type == 2) {
            $team_win = ($row->score1 > $row->score2) ? $row->team1_id : $row->team2_id;
            $team_loose = ($row->score1 > $row->score2) ? $row->team2_id : $row->team1_id;

            $query = 'UPDATE #__bl_match SET team1_id='.$team_win.'  WHERE m_id = '.$row->m_id.' AND k_stage > '.$row->k_stage.' AND team1_id = '.$team_loose;
            $this->db->setQuery($query);
            $this->db->query();
            $error = $this->db->getErrorMsg();
            if ($error) {
                return JError::raiseError(500, $error);
            }
            $query = 'UPDATE #__bl_match SET team2_id='.$team_win.'  WHERE m_id = '.$row->m_id.' AND k_stage > '.$row->k_stage.' AND team2_id = '.$team_loose;
            $this->db->setQuery($query);
            $this->db->query();
            $error = $this->db->getErrorMsg();
            if ($error) {
                return JError::raiseError(500, $error);
            }

            if ($row->m_played == 0) {
                $query = "UPDATE #__bl_match SET m_played = '0' WHERE m_id = ".$row->m_id.' AND k_stage > '.$row->k_stage.' AND (team1_id = '.$row->team1_id.' OR team2_id = '.$row->team1_id.' OR team1_id = '.$row->team2_id.' OR team2_id = '.$row->team2_id.')';
                $this->db->setQuery($query);
                $this->db->query();
                $error = $this->db->getErrorMsg();
                if ($error) {
                    return JError::raiseError(500, $error);
                }
                $query = "UPDATE #__bl_match SET team1_id = '0' WHERE m_id = ".$row->m_id.' AND k_stage > '.$row->k_stage.' AND (team1_id = '.$row->team1_id.' OR team1_id = '.$row->team2_id.')';
                $this->db->setQuery($query);
                $this->db->query();
                $error = $this->db->getErrorMsg();
                if ($error) {
                    return JError::raiseError(500, $error);
                }
                $query = "UPDATE #__bl_match SET team2_id = '0' WHERE m_id = ".$row->m_id.' AND k_stage > '.$row->k_stage.' AND (team2_id = '.$row->team1_id.' OR team2_id = '.$row->team2_id.')';
                $this->db->setQuery($query);
                $this->db->query();
                $error = $this->db->getErrorMsg();
                if ($error) {
                    return JError::raiseError(500, $error);
                }
            }
        }
        $eordering = 0;
        $me_arr = array();
        if (isset($_POST['new_eventid']) && count($_POST['new_eventid'])) {
            for ($i = 0; $i < count($_POST['new_eventid']); ++$i) {
                if (!isset($_POST['em_id'][$i]) || !intval($_POST['em_id'][$i])) {
                    $new_event = $_POST['new_eventid'][$i];
                    $plis = explode('*', $_POST['new_player'][$i]);

                    $query = 'INSERT INTO #__bl_match_events(e_id,player_id,match_id,ecount,minutes,t_id,eordering) VALUES('.$new_event.','.intval($plis[0]).','.$row->id.','.intval($_POST['e_countval'][$i]).','.intval($_POST['e_minuteval'][$i]).','.intval($plis[1]).','.$eordering.')';

                    $this->db->setQuery($query);
                    $this->db->query();
                    $error = $this->db->getErrorMsg();
                    if ($error) {
                        return JError::raiseError(500, $error);
                    }

                    $me_arr[] = $this->db->insertid();
                } else {
                    $query = 'SELECT * FROM #__bl_match_events WHERE id='.intval($_POST['em_id'][$i]);
                    $this->db->setQuery($query);
                    $event_bl = $this->db->loadObjectList();
                    $error = $this->db->getErrorMsg();
                    if ($error) {
                        return JError::raiseError(500, $error);
                    }
                    if (count($event_bl)) {
                        $query = 'UPDATE #__bl_match_events SET minutes='.intval($_POST['e_minuteval'][$i]).', ecount='.intval($_POST['e_countval'][$i]).', eordering='.$eordering.' WHERE id='.intval($_POST['em_id'][$i]);
                        $this->db->setQuery($query);
                        $this->db->query();
                        $error = $this->db->getErrorMsg();
                        if ($error) {
                            return JError::raiseError(500, $error);
                        }

                        $me_arr[] = intval($_POST['em_id'][$i]);
                    }
                }
                ++$eordering;
            }
        }
        $eordering_t = 0;
        $me_arr_t = array();
        if (isset($_POST['new_teventid']) && count($_POST['new_teventid'])) {
            for ($i = 0; $i < count($_POST['new_teventid']); ++$i) {
                if (!isset($_POST['et_id'][$i]) || !intval($_POST['et_id'][$i])) {
                    $new_event = $_POST['new_teventid'][$i];
                    $query = 'INSERT INTO #__bl_match_events(e_id,t_id,match_id,ecount,minutes,eordering) VALUES('.$new_event.','.$_POST['new_tplayer'][$i].','.$row->id.','.intval($_POST['et_countval'][$i]).",'0',".$eordering_t.')';
                    $this->db->setQuery($query);
                    $this->db->query();
                    $error = $this->db->getErrorMsg();
                    if ($error) {
                        return JError::raiseError(500, $error);
                    }
                    $me_arr_t[] = $this->db->insertid();
                } else {
                    $query = 'SELECT * FROM #__bl_match_events WHERE id='.intval($_POST['et_id'][$i]);
                    $this->db->setQuery($query);
                    $event_bl = $this->db->loadObjectList();
                    $error = $this->db->getErrorMsg();
                    if ($error) {
                        return JError::raiseError(500, $error);
                    }
                    if (count($event_bl)) {
                        $query = 'UPDATE #__bl_match_events SET ecount='.intval($_POST['et_countval'][$i]).', eordering='.$eordering_t.' WHERE id='.intval($_POST['et_id'][$i]);
                        $this->db->setQuery($query);
                        $this->db->query();
                        $error = $this->db->getErrorMsg();
                        if ($error) {
                            return JError::raiseError(500, $error);
                        }

                        $me_arr_t[] = intval($_POST['et_id'][$i]);
                    }
                }
                ++$eordering_t;
            }
        }

    ///
        $me_arr_n = array();
        if (isset($_POST['em_id_n']) && count($_POST['em_id_n'])) {
            for ($i = 0;$i < count($_POST['em_id_n']);++$i) {
                $me_arr_n[] = $_POST['em_id_n'][$i];
            }
        }
        /////////////DELETE 
        $query = 'DELETE FROM #__bl_match_events WHERE match_id = '.$row->id;
        if (count($me_arr)) {
            $query .= ' AND id NOT IN ('.implode(',', $me_arr).')';
        }
        if (count($me_arr_t)) {
            $query .= ' AND id NOT IN ('.implode(',', $me_arr_t).')';
        }
        if (count($me_arr_n)) {
            $query .= ' AND id NOT IN ('.implode(',', $me_arr_n).')';
        }
        if ($this->acl == 2 && $edit_comp == 0 && !in_array($row->team1_id, $teams_season_moder)) {
            $query .= ' AND t_id != '.$row->team1_id;
        }
        if ($this->acl == 2 && $edit_comp == 0 && !in_array($row->team2_id, $teams_season_moder)) {
            $query .= ' AND t_id != '.$row->team2_id;
        }

        $this->db->setQuery($query);
        $this->db->query();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }

        $query = 'DELETE FROM #__bl_assign_photos WHERE cat_type = 3 AND cat_id = '.$row->id;
        $this->db->setQuery($query);
        $this->db->query();
        if (isset($_POST['photos_id']) && count($_POST['photos_id'])) {
            for ($i = 0; $i < count($_POST['photos_id']); ++$i) {
                $photo_id = intval($_POST['photos_id'][$i]);
                $photo_name = addslashes(strval($_POST['ph_names'][$i]));
                $query = 'INSERT INTO #__bl_assign_photos(photo_id,cat_id,cat_type) VALUES('.$photo_id.','.$row->id.',3)';
                $this->db->setQuery($query);
                $this->db->query();
                $query = "UPDATE #__bl_photos SET ph_name = '".($photo_name)."' WHERE id = ".$photo_id;
                $this->db->setQuery($query);
                $this->db->query();
            }
        }
        if (isset($_FILES['player_photo_1']['name']) && $_FILES['player_photo_1']['tmp_name'] != '' && isset($_FILES['player_photo_1']['tmp_name'])) {
            $bl_filename = strtolower($_FILES['player_photo_1']['name']);
            $ext = pathinfo($_FILES['player_photo_1']['name']);
            $bl_filename = 'bl'.time().rand(0, 3000).'.'.$ext['extension'];
            $bl_filename = str_replace(' ', '', $bl_filename);
            //echo $bl_filename;
             if ($this->uploadFile($_FILES['player_photo_1']['tmp_name'], $bl_filename)) {
                 $post1['ph_filename'] = $bl_filename;
                 $img1 = new JTablePhotos($this->db);
                 $img1->id = 0;
                 if (!$img1->bind($post1)) {
                     JError::raiseError(500, $img1->getError());
                 }
                 if (!$img1->check()) {
                     JError::raiseError(500, $img1->getError());
                 }
                // if new item order last in appropriate group
                if (!$img1->store()) {
                    JError::raiseError(500, $img1->getError());
                }
                 $img1->checkin();
                 $query = 'INSERT INTO #__bl_assign_photos(photo_id,cat_id,cat_type) VALUES('.$img1->id.','.$row->id.',3)';
                 $this->db->setQuery($query);
                 $this->db->query();
             }
        } else {
            if ($_FILES['player_photo_1']['error'] == 1) {
                if ($this->acl == 1) {
                    $this->mainframe->redirect('index.php?option=com_joomsport&task=edit_match&controller=admin&tid='.$tid.'&cid[]='.$row->id.'&sid='.$this->season_id, JText::_('BLBA_WRNGPHOTO'), 'warning');
                } elseif ($this->acl == 2) {
                    $this->mainframe->redirect('index.php?option=com_joomsport&task=edit_match&controller=moder&tid='.$tid.'&cid[]='.$row->id.'&sid='.$this->season_id, JText::_('BLBA_WRNGPHOTO'), 'warning');
                }
            }
        }
        if (isset($_FILES['player_photo_2']['name']) && $_FILES['player_photo_2']['tmp_name'] != ''  && isset($_FILES['player_photo_2']['tmp_name'])) {
            $bl_filename = strtolower($_FILES['player_photo_2']['name']);
            $ext = pathinfo($_FILES['player_photo_2']['name']);
            $bl_filename = 'bl'.time().rand(0, 3000).'.'.$ext['extension'];
            $bl_filename = str_replace(' ', '', $bl_filename);
            if ($this->uploadFile($_FILES['player_photo_2']['tmp_name'], $bl_filename)) {
                $post2['ph_filename'] = $bl_filename;
                $img2 = new JTablePhotos($this->db);
                $img2->id = 0;
                if (!$img2->bind($post2)) {
                    JError::raiseError(500, $img2->getError());
                }
                if (!$img2->check()) {
                    JError::raiseError(500, $img2->getError());
                }
                // if new item order last in appropriate group

                if (!$img2->store()) {
                    JError::raiseError(500, $img2->getError());
                }
                $img2->checkin();
                $query = 'INSERT INTO #__bl_assign_photos(photo_id,cat_id,cat_type) VALUES('.$img2->id.','.$row->id.',3)';
                $this->db->setQuery($query);
                $this->db->query();
            }
        } else {
            if ($_FILES['player_photo_2']['error'] == 1) {
                if ($this->acl == 1) {
                    $this->mainframe->redirect('index.php?option=com_joomsport&task=edit_match&controller=admin&tid='.$tid.'&cid[]='.$row->id.'&sid='.$this->season_id, JText::_('BLBA_WRNGPHOTO'), 'warning');
                } elseif ($this->acl == 2) {
                    $this->mainframe->redirect('index.php?option=com_joomsport&task=edit_match&controller=moder&tid='.$tid.'&cid[]='.$row->id.'&sid='.$this->season_id, JText::_('BLBA_WRNGPHOTO'), 'warning');
                }
            }
        }
        //-------extra fields-----------//
        if (isset($_POST['extraf']) && count($_POST['extraf'])) {
            foreach ($_POST['extraf'] as $p => $dummy) {
                $query = 'DELETE FROM #__bl_extra_values WHERE f_id = '.$_POST['extra_id'][$p].' AND uid = '.$row->id;
                $this->db->setQuery($query);
                $this->db->query();
                if ($_POST['extra_ftype'][$p] == '2') {
                    $query = 'INSERT INTO #__bl_extra_values(f_id,uid,fvalue_text) VALUES('.$_POST['extra_id'][$p].','.$row->id.",'".addslashes($_POST['extraf'][$p])."')";
                } else {
                    $query = 'INSERT INTO #__bl_extra_values(f_id,uid,fvalue) VALUES('.$_POST['extra_id'][$p].','.$row->id.",'".addslashes($_POST['extraf'][$p])."')";
                }
                $this->db->setQuery($query);
                $this->db->query();
                $error = $this->db->getErrorMsg();
                if ($error) {
                    return JError::raiseError(500, $error);
                }
            }
        }
        //-----SQUARD--------///
        if ($this->acl != 3) {
            if ($this->acl == 2 && $edit_comp == 0 && (($this->getJS_Config('jsmr_edit_squad_opposite') == 0 && !in_array($row->team1_id, $teams_season_moder)) || ($this->getJS_Config('jsmr_edit_squad_yours') == 0 && in_array($row->team1_id, $teams_season_moder)))) {
            } else {
                $query = 'DELETE FROM #__bl_squard WHERE team_id = '.$row->team1_id.' AND match_id = '.$row->id;
                $this->db->setQuery($query);
                $this->db->query();
                $error = $this->db->getErrorMsg();
                if ($error) {
                    return JError::raiseError(500, $error);
                }

                if (isset($_POST['t1_squard']) && count($_POST['t1_squard'])) {
                    for ($i = 0; $i < count($_POST['t1_squard']); ++$i) {
                        $new_event = $_POST['t1_squard'][$i];
                        $query = 'INSERT INTO #__bl_squard(match_id,team_id,player_id,mainsquard) VALUES('.$row->id.','.$row->team1_id.','.$new_event.",'1')";
                        $this->db->setQuery($query);
                        $this->db->query();
                        $error = $this->db->getErrorMsg();
                        if ($error) {
                            return JError::raiseError(500, $error);
                        }
                    }
                }

                if (isset($_POST['t1_squard_res']) && count($_POST['t1_squard_res'])) {
                    for ($i = 0; $i < count($_POST['t1_squard_res']); ++$i) {
                        $new_event = $_POST['t1_squard_res'][$i];
                        $query = 'INSERT INTO #__bl_squard(match_id,team_id,player_id,mainsquard) VALUES('.$row->id.','.$row->team1_id.','.$new_event.",'0')";
                        $this->db->setQuery($query);
                        $this->db->query();
                        $error = $this->db->getErrorMsg();
                        if ($error) {
                            return JError::raiseError(500, $error);
                        }
                    }
                }
                //subs in
                $query = 'DELETE FROM #__bl_subsin WHERE team_id = '.$row->team1_id.' AND match_id='.$row->id;
                $this->db->setQuery($query);
                $this->db->query();
                $error = $this->db->getErrorMsg();
                if ($error) {
                    return JError::raiseError(500, $error);
                }
                if (isset($_POST['playersq1_id_arr']) && count($_POST['playersq1_id_arr'])) {
                    for ($i = 0; $i < count($_POST['playersq1_id_arr']); ++$i) {
                        $player_in = intval($_POST['playersq1_id_arr'][$i]);
                        $player_out = intval($_POST['playersq1_out_id_arr'][$i]);
                        $minutes = intval($_POST['minutes1_arr'][$i]);
                        $query = 'INSERT INTO #__bl_subsin(match_id,team_id,player_in,player_out,minutes,season_id) VALUES('.$row->id.','.$row->team1_id.','.$player_in.','.$player_out.",'".$minutes."',".$season_id.')';
                        $this->db->setQuery($query);
                        $this->db->query();
                        $error = $this->db->getErrorMsg();
                        if ($error) {
                            return JError::raiseError(500, $error);
                        }
                    }
                }
            }
            if ($this->acl == 2 && $edit_comp == 0 && (($this->getJS_Config('jsmr_edit_squad_opposite') == 0 && !in_array($row->team2_id, $teams_season_moder)) || ($this->getJS_Config('jsmr_edit_squad_yours') == 0 && in_array($row->team2_id, $teams_season_moder)))) {
            } else {
                $query = 'DELETE FROM #__bl_squard WHERE team_id = '.$row->team2_id.' AND match_id = '.$row->id;
                $this->db->setQuery($query);
                $this->db->query();
                $error = $this->db->getErrorMsg();
                if ($error) {
                    return JError::raiseError(500, $error);
                }

                if (isset($_POST['t2_squard']) && count($_POST['t2_squard'])) {
                    for ($i = 0; $i < count($_POST['t2_squard']); ++$i) {
                        $new_event = $_POST['t2_squard'][$i];
                        $query = 'INSERT INTO #__bl_squard(match_id,team_id,player_id,mainsquard) VALUES('.$row->id.','.$row->team2_id.','.$new_event.",'1')";
                        $this->db->setQuery($query);
                        $this->db->query();
                        $error = $this->db->getErrorMsg();
                        if ($error) {
                            return JError::raiseError(500, $error);
                        }
                    }
                }

                if (isset($_POST['t2_squard_res']) && count($_POST['t2_squard_res'])) {
                    for ($i = 0; $i < count($_POST['t2_squard_res']); ++$i) {
                        $new_event = $_POST['t2_squard_res'][$i];
                        $query = 'INSERT INTO #__bl_squard(match_id,team_id,player_id,mainsquard) VALUES('.$row->id.','.$row->team2_id.','.$new_event.",'0')";
                        $this->db->setQuery($query);
                        $this->db->query();
                        $error = $this->db->getErrorMsg();
                        if ($error) {
                            return JError::raiseError(500, $error);
                        }
                    }
                }
                //subs in
                $query = 'DELETE FROM #__bl_subsin WHERE team_id = '.$row->team2_id.' AND match_id='.$row->id;
                $this->db->setQuery($query);
                $this->db->query();
                $error = $this->db->getErrorMsg();
                if ($error) {
                    return JError::raiseError(500, $error);
                }

                if (isset($_POST['playersq2_id_arr']) && count($_POST['playersq2_id_arr'])) {
                    for ($i = 0; $i < count($_POST['playersq2_id_arr']); ++$i) {
                        $player_in = intval($_POST['playersq2_id_arr'][$i]);
                        $player_out = intval($_POST['playersq2_out_id_arr'][$i]);
                        $minutes = intval($_POST['minutes2_arr'][$i]);
                        $query = 'INSERT INTO #__bl_subsin(match_id,team_id,player_in,player_out,minutes,season_id) VALUES('.$row->id.','.$row->team2_id.','.$player_in.','.$player_out.",'".$minutes."',".$season_id.')';
                        $this->db->setQuery($query);
                        $this->db->query();
                        $error = $this->db->getErrorMsg();
                        if ($error) {
                            return JError::raiseError(500, $error);
                        }
                    }
                }
            }
        }
        $query = 'DELETE  FROM #__bl_mapscore WHERE m_id = '.$row->id;
        $this->db->setQuery($query);
        $this->db->query();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
        if (isset($_POST['mapid']) && count($_POST['mapid'])) {
            for ($i = 0; $i < count($_POST['mapid']); ++$i) {
                $new_event = $_POST['mapid'][$i];
                $query = 'INSERT INTO #__bl_mapscore(m_id,map_id,m_score1,m_score2) VALUES('.$row->id.','.$new_event.','.intval($_POST['t1map'][$i]).','.intval($_POST['t2map'][$i]).')';
                $this->db->setQuery($query);
                $this->db->query();
                $error = $this->db->getErrorMsg();
                if ($error) {
                    return JError::raiseError(500, $error);
                }
            }
        }
        
        //boxscore
        $query = "SELECT p.id
			            FROM #__bl_players as p, #__bl_players_team as s
			            WHERE s.confirmed='0' AND s.player_join='0' AND s.player_id = p.id
			            AND s.team_id = ".$row->team1_id.' AND s.season_id='.$season_id
                        .' ORDER BY p.first_name,p.last_name';
        $this->db->setQuery($query);
        $h_players = $this->db->loadObjectList();
        
       for($intA=0;$intA<count($h_players);$intA++){
           $insert_field = '';
           $insert_vals = '';
           $update_vals = '';
           $box_data = filter_input(INPUT_POST, 'boxstat_'.$row->team1_id.'_'.$h_players[$intA]->id, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
           if(count($box_data)){
               foreach($box_data as $key=>$value){
                   $insert_field .= ',boxfield_'.intval($key);
                   $insert_vals .= ",".($value != ''?floatval($value):'NULL');
                   if($update_vals){
                       $update_vals .= ",";
                   }
                   $update_vals .= "boxfield_".intval($key)."=".($value != ''?floatval($value):'NULL');
               }
           }
           $this->db->setQuery("SELECT id FROM #__bl_box_matches WHERE match_id={$row->id} AND player_id={$h_players[$intA]->id} AND team_id={$row->team1_id}");
           $dobl = $this->db->loadResult();

           if($dobl){
               if($update_vals){
                    $this->db->setQuery("UPDATE #__bl_box_matches SET $update_vals"
                       . " WHERE id={$dobl}");
                    $this->db->query();   
               }
           }else{
               $this->db->setQuery("INSERT INTO #__bl_box_matches(match_id,season_id,team_id,player_id".$insert_field.")"
                       . " VALUES({$row->id},{$season_id},{$row->team1_id},{$h_players[$intA]->id}".$insert_vals.")");
           
                $this->db->query(); 
           }
       }
       
       $query = "SELECT p.id
			            FROM #__bl_players as p, #__bl_players_team as s
			            WHERE s.confirmed='0' AND s.player_join='0' AND s.player_id = p.id
			            AND s.team_id = ".$row->team2_id.' AND s.season_id='.$season_id
                        .' ORDER BY p.first_name,p.last_name';
        $this->db->setQuery($query);
        $a_players = $this->db->loadObjectList();
       for($intA=0;$intA<count($a_players);$intA++){
           $insert_field = '';
           $insert_vals = '';
           $update_vals = '';
           $box_data = filter_input(INPUT_POST, 'boxstat_'.$row->team2_id.'_'.$a_players[$intA]->id, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
           if(count($box_data)){
               foreach($box_data as $key=>$value){
                   $insert_field .= ',boxfield_'.intval($key);
                   $insert_vals .= ",".($value != ''?floatval($value):'NULL');
                   if($update_vals){
                       $update_vals .= ",";
                   }
                   $update_vals .= "boxfield_".intval($key)."=".($value != ''?floatval($value):'NULL');
               }
           }
           
           $this->db->setQuery("SELECT id FROM #__bl_box_matches WHERE match_id={$row->id} AND player_id={$a_players[$intA]->id} AND team_id={$row->team2_id}");
           $dobl = $this->db->loadResult();
           if($dobl){
               if($update_vals){
                    $this->db->setQuery("UPDATE #__bl_box_matches SET $update_vals"
                       . " WHERE id={$dobl}");
                    $this->db->query();   
               }
           }else{
               $this->db->setQuery("INSERT INTO #__bl_box_matches(match_id,season_id,team_id,player_id".$insert_field.")"
                       . " VALUES({$row->id},{$season_id},{$row->team2_id},{$a_players[$intA]->id}".$insert_vals.")");
               $this->db->query();  
           }
       }
        
        require_once JPATH_COMPONENT_SITE.DIRECTORY_SEPARATOR.'sportleague'.DIRECTORY_SEPARATOR.'sportleague.php';
            
        if ($lt_type == '0') {
            //update season table
            classJsportPlugins::get('generateTableStanding', array('season_id' => $season_id));
        }
        classJsportPlugins::get('generatePlayerList', array('season_id' => $season_id));

        $this->id = $row->id;
        $this->m_id = $row->m_id;
        $this->s_id = $season_id;
        if ($this->acl == 2) {
            $this->tid = $tid;
        }
    }

    public function inviteModerMatch()
    {
        $is_minv = JRequest::getVar('is_minv', 0, '', 'int');
        $inv_mtitle = JRequest::getVar('inv_mtitle', '', '', 'string');
        $inv_mtext = JRequest::getVar('inv_mtext', '', 'post', 'string', JREQUEST_ALLOWRAW);
        $config = JFactory::getConfig();
        $fromname = $config->get('fromname');
        $mailfrom = $config->get('mailfrom');
        $sitename = $config->get('sitename');

        if ($is_minv) {
            $query = 'SELECT u.email,p.id FROM #__bl_squard as s, #__bl_players as p, #__users as u WHERE s.player_id = p.id AND p.usr_id = u.id AND s.match_id = '.$this->id.' AND s.team_id = '.$this->tid." AND s.mainsquard = '1'";
        } else {
            $query = "SELECT u.email,p.id FROM #__bl_players as p, #__bl_players_team as t, #__users as u WHERE t.confirmed = '0' AND u.id = p.usr_id AND p.id=t.player_id AND t.team_id = ".$this->tid.' AND t.season_id='.$this->s_id.' ORDER BY p.first_name,p.last_name';
        }

        $this->db->setQuery($query);
        $pl_inv = $this->db->loadObjectList();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
        if (count($pl_inv)) {
            foreach ($pl_inv as $pl) {
                if ($pl->email) {
                    $accept_lnk = JUri::base().'index.php?option=com_joomsport&task=match_inviting&do=accept&tid='.$this->tid.'&mid='.$this->id.'&key='.md5($pl->id);
                    $reject_lnk = JUri::base().'index.php?option=com_joomsport&task=match_inviting&do=reject&tid='.$this->tid.'&mid='.$this->id.'&key='.md5($pl->id);

                    $accept = "<a href='".$accept_lnk."'>".JText::_('BLFA_ACCEPT').'</a>';
                    $reject = "<a href='".$reject_lnk."'>".JText::_('BLFA_PLREJECT').'</a>';

                    $inv_mtext = str_replace('{accept}', $accept, $inv_mtext);
                    $inv_mtext = str_replace('{reject}', $reject, $inv_mtext);

                    $return = JFactory::getMailer()->sendMail($mailfrom, $fromname, $pl->email, $inv_mtitle, $inv_mtext, 1);

                    if ($pl->id && $is_minv) {
                        $query = "UPDATE #__bl_squard SET accepted = '0' WHERE player_id=".$pl->id.' AND team_id = '.$this->tid.' AND match_id='.$this->id;
                        $this->db->setQuery($query);
                        $this->db->query();
                        $error = $this->db->getErrorMsg();
                        if ($error) {
                            return JError::raiseError(500, $error);
                        }
                    }

                    // Check for an error.
                    if ($return !== true) {
                        $this->setError(JText::_('ERROR'));

                        return false;
                    }
                }
            }
        }
    }

    protected function getMatchDayType($row)
    {
        $query = 'SELECT t_type FROM #__bl_matchday WHERE id='.$row->m_id;
        $this->db->setQuery($query);

        return $t_type = $this->db->loadResult();
    }
}
