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
//require(JPATH_SITE.'/administrator/components/com_joomsport/models/default/knockout.php');
require dirname(__FILE__).'/knockout.php';

class edit_matchdayJSModel extends JSPRO_Models
{
    public $_data = null;
    public $_lists = null;
    public $mid = null;
    public $season_id = null;
    public $t_single = null;
    public $t_type = null;
    public $id = null;
    public $tid = null;
    public $_user = null;
    public $knock_type = null;
    /*moder rights  1 - season admin, 2 - team moderator, 3 - registered player*/
    public $acl = null;

    public function __construct($acl)
    {
        parent::__construct();
        $this->acl = $acl;
        $this->mid = JRequest::getVar('mid', 0, '', 'int');
        if ($this->acl == 2) {
            $this->tid = JRequest::getVar('tid', 0, '', 'int');
            $this->season_id = $this->mainframe->getUserStateFromRequest('com_joomsport.moderseason', 'moderseason', 0, 'int');
            $query = "SELECT CONCAT(tr.name,' ',s.s_name) as t_name,s.s_id as id FROM #__bl_season_teams as t,#__bl_seasons as s,#__bl_tournament as tr WHERE s.published=1 AND tr.id=s.t_id AND s.s_id=t.season_id AND t.team_id=".$this->tid.' ORDER BY s.s_id desc';
            $this->db->setQuery($query);
            $seass = $this->db->loadObjectList();
            if (!$this->season_id) {
                $this->season_id = $seass[0]->id;
            };
            $isinseas = false;
            for ($j = 0;$j < count($seass);++$j) {
                if ($this->season_id == $seass[$j]->id) {
                    $isinseas = true;
                }
            }
            if ($this->season_id == -1) {
                $isinseas = true;
            }
            if (!$isinseas && count($seass)) {
                $this->season_id = $seass[0]->id;
            }
        } elseif ($this->acl == 3) {
            $this->_user = JFactory::getUser();
            if ($this->_user->get('guest')) {
                $return_url = $_SERVER['REQUEST_URI'];
                $return_url = base64_encode($return_url);

                if ($this->getVer() >= '1.6') {
                    $uopt = 'com_users';
                } else {
                    $uopt = 'com_user';
                }
                $return = 'index.php?option='.$uopt.'&view=login&return='.$return_url;

                // Redirect to a login form
                $this->mainframe->redirect($return, JText::_('BLMESS_NOT_LOGIN'));
            }
            $this->season_id = $this->mainframe->getUserStateFromRequest('com_joomsport.sid', 'sid', $this->season_id, 'int');

            $query = "SELECT s.s_id as id,CONCAT(tr.name,' ',s.s_name) as t_name"
                    .' FROM #__bl_season_players as t,#__bl_players as p,#__bl_seasons as s,#__bl_tournament as tr'
                    .' WHERE s.published=1 AND tr.id=s.t_id AND s.s_id=t.season_id AND t.player_id=p.id AND p.usr_id='.$this->_user->id
                    .' ORDER BY s.s_id desc';
            $this->db->setQuery($query);
            $seass = $this->db->loadObjectList();
            $error = $this->db->getErrorMsg();
            if ($error) {
                return JError::raiseError(500, $error);
            }
            if (!$this->season_id) {
                $this->season_id = $seass[0]->id;
            };
            $isinseas = false;
            for ($j = 0;$j < count($seass);++$j) {
                if ($this->season_id == $seass[$j]->id) {
                    $isinseas = true;
                }
            }
            if ($this->season_id == -1) {
                $isinseas = true;
            }
            if (!$isinseas && count($seass)) {
                $this->season_id = $seass[0]->id;
            }
        }
    }

    public function getData()
    {
        $this->t_type = JRequest::getVar('t_type', 0, '', 'int');

        $this->knock_type = new JS_Knockout();

        $this->_params = $this->JS_PageTitle(JText::_('BLFA_MDAY_EDIT'));
        if ($this->acl == 1) {
            $is_id = 0;
            $cid = JRequest::getVar('cid', array(0), '', 'array');
            JArrayHelper::toInteger($cid, array(0));
            if ($cid[0]) {
                $is_id = $cid[0];
            }
            $this->mid = $is_id ? $is_id : $this->mid;

            $query = "SELECT s.s_id as id, CONCAT(t.name,' ',s.s_name) as name"
                .' FROM #__bl_tournament as t, #__bl_seasons as s'
                .' WHERE s.t_id = t.id'
                .' ORDER BY t.name, s.s_name';
            $this->db->setQuery($query);
            $tourns = $this->db->loadObjectList();
            $error = $this->db->getErrorMsg();
            if ($error) {
                return JError::raiseError(500, $error);
            }

            $this->season_id = $this->mainframe->getUserStateFromRequest('com_joomsport.sid', 'sid', $tourns[0]->id, 'int');
        } elseif ($this->acl == 2) {
            $this->getGlobFilters(true);
            $this->SeasModerfilter();
            $this->_lists['moder_addplayer'] = $this->getJS_Config('moder_addplayer');
            $this->_lists['moder_create_match'] = $this->getJS_Config('moder_create_match');
        } else {
            $this->getFilterseas();
            $this->getFiltermday();
        }
        $this->_lists['jsmr_mark_played'] = $this->getJS_Config('jsmr_mark_played');
        $this->_lists['jsmr_editresult_yours'] = $this->getJS_Config('jsmr_editresult_yours');
        $this->_lists['jsmr_editresult_opposite'] = $this->getJS_Config('jsmr_editresult_opposite');

        $tmp_arr[] = JHTML::_('select.option',  0, JText::_('BLBE_NOT_PLAYED'), 'id', 'value');
        $tmp_arr[] = JHTML::_('select.option',  1, JText::_('BLFA_PLAYED'), 'id', 'value');
        $query = 'SELECT id, stName as value FROM #__bl_match_statuses'
                        .' ORDER BY ordering';
        $this->db->setQuery($query);
        $selvals = $this->db->loadObjectList();
        $selvals = $this->_lists['m_played_i'] = array_merge($tmp_arr, $selvals);
        $this->_lists['m_played'] = JHTML::_('select.genericlist',   $selvals, 'tm_played', 'class="inputbox" size="1"', 'id', 'value', 0);

        $row = new JTableMday($this->db);
        $row->load($this->mid);
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
        if ($this->acl == 1 && $is_id) {
            $query = 'SELECT COUNT(*) FROM #__bl_seasons as s LEFT JOIN #__bl_tournament as t ON t.id = s.t_id WHERE  s.s_id = '.($row->s_id ? $row->s_id : $this->season_id);
            $this->db->setQuery($query);

            if (!$this->db->loadResult()) {
                JError::raiseError(403, JText::_('Access Forbidden'));

                return;
            }
            //$tpl="edit";
        }
        $this->season_id = $row->s_id ? $row->s_id : $this->season_id;
        $tourn = $this->getTournOpt($this->season_id);
        ////
        if (empty($this->t_type) && $row->id) {
            $this->t_type = $this->getMatchDayType($row);
        }

        if ($this->season_id != -1) {
            $this->t_single = $tourn->t_single;
            //$this->t_type = $tourn->t_type;
            $this->_lists['t_type'] = $this->t_type;
            $this->_lists['s_enbl_extra'] = $tourn->s_enbl_extra;
            $this->_lists['tourn'] = $tourn->name;
        } else {
            $this->t_single = 0;
            //$this->t_type = 0;
            $this->t_type = 0;
            $this->_lists['t_type'] = 0;
            ////
            $this->_lists['s_enbl_extra'] = 1;
            $this->_lists['tourn'] = '';
        }

        $this->_data = $row;
        $this->_lists['is_team'] = $this->getteamsSeas($this->season_id);
        $this->_lists['is_playoff'] = JHTML::_('select.booleanlist',  'is_playoff', '', $row->is_playoff);
        if ($this->acl == 1) {
            $this->getMatchesFMD($row->id);
            $this->getMatchesFMDDE($row->id);

            $cfg = new stdClass();
            $cfg->wdth = 150;
            $cfg->height = 50;
            $cfg->step = 70;
            $cfg->top_next = 50;

            $this->_lists['format'] = $this->knock_type->getKnockFormat($row, $this->_lists['t_type']);
            if ($this->t_type == 2 && $row->k_format) { ///
              $this->_lists['knock_layout'] = $this->knock_type->getKnockDE($row, $tourn, $this->_lists['match'], $this->season_id, $this->_lists['matchDE'], $cfg, 1);
            } elseif ($this->t_type == 1 && $row->k_format) {
                //$this->_lists['format'] = $this->knock_type->getKnockFormat($row, $this->_lists['t_type']);
                $this->_lists['knock_layout'] = $this->knock_type->getKnock($row, $tourn, $this->_lists['match'], $this->season_id, $cfg, 1);
            }
            //season admin can edit played match

            $this->_lists['jsmr_mark_played'] = 1;
        } elseif ($this->acl == 2) {
            $this->getMatchesModer($row->id);
        } else {
            $this->getMdMatch();
            //$this->getlTeams();

            $query = 'Select * FROM #__bl_players WHERE usr_id='.$this->_user->id;
            $this->db->setQuery($query);
            $usr = $this->db->loadObject();
            $error = $this->db->getErrorMsg();
            if ($error) {
                return JError::raiseError(500, $error);
            }
            $this->_lists['usr'] = $usr;
            $this->tid = $usr->id;
            //---------------------------//

            $this->_lists['msg'] = JRequest::getVar('msg', '', 'get', 'string', JREQUEST_ALLOWRAW);
        }
        $this->_lists['teams_season'] = $this->teamsToModer();
        $this->_lists['panel'] = $this->getePanel($this->_lists['teams_season'], 0, null, 0);
    }
    public function getMatchDayType($row)
    {
        $query = 'SELECT t_type FROM #__bl_matchday WHERE id='.$row->id.' AND s_id = '.$row->s_id.'';
     //print_r($query);   
        $this->db->setQuery($query);

        return $t_type = $this->db->loadResult();
    }
    public function getFiltermday()
    {
        $query = 'SELECT m.* FROM #__bl_season_players as t,#__bl_players as p,#__bl_seasons as s,#__bl_matchday as m'
                .' WHERE s.published=1 AND m.s_id=s.s_id AND s.s_id=t.season_id AND t.player_id=p.id AND p.usr_id='.$this->_user->id.' AND s.s_id='.$this->season_id
                .' ORDER BY m.id desc';
        if ($this->season_id == -1) {
            $query = 'SELECT m.* FROM #__bl_matchday as m'
                .' WHERE m.s_id='.$this->season_id
                .' ORDER BY m.ordering';
        }
        $this->db->setQuery($query);
        $mdays = $this->db->loadObjectList();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }

        $query = 'SELECT COUNT(*) FROM #__bl_season_players as t,#__bl_players as p,#__bl_seasons as s,#__bl_matchday as m'
                .' WHERE s.published=1 AND m.s_id=s.s_id AND s.s_id=t.season_id AND t.player_id=p.id AND p.usr_id='.$this->_user->id.' AND s.s_id='.$this->season_id.' AND m.id='.$this->mid
                .' ORDER BY m.id desc';
        if ($this->season_id == -1) {
            $query = 'SELECT COUNT(*) FROM #__bl_matchday as m'
                .' WHERE m.s_id='.$this->season_id.' AND m.id='.$this->mid;
        }
        $this->db->setQuery($query);
        if (!$this->db->loadResult()) {
            $this->mid = isset($mdays[0]->id) ? $mdays[0]->id : 0;
        }

        if (!$this->mid && count($mdays)) {
            $this->mid = $mdays[0]->id;
        }
        $javascript = "onchange='document.filtrForm.submit();'";
        if (count($mdays)) {
            $this->_lists['md_filtr'] = JHTML::_('select.genericlist',   $mdays, 'mid', 'class="selectpicker" size="1"'.$javascript, 'id', 'm_name', $this->mid);
        } else {
            $this->_lists['md_filtr'] = '';
        }
    }
    public function getMatchesFMD($id)
    {
        $orderby = $this->t_type ? 'm.k_stage,m.k_ordering' : 'm.m_date,m.m_time,m.id';
        if ($this->t_single) {
            $query = "SELECT m.*,CONCAT(t.first_name,' ',t.last_name) as home_team, CONCAT(t2.first_name,' ',t2.last_name) as away_team,IF(m.score1>m.score2,CONCAT(t.first_name,' ',t.last_name),CONCAT(t2.first_name,' ',t2.last_name)) as winner, IF(m.score1>m.score2,t.id,t2.id) as winnerid,IF(m.score1<m.score2,CONCAT(t.first_name,' ',t.last_name),CONCAT(t2.first_name,' ',t2.last_name)) as looser, IF(m.score1<m.score2,t.id,t2.id) as looserid"
                    .' FROM #__bl_match as m LEFT JOIN #__bl_players as t ON t.id = m.team1_id  LEFT JOIN #__bl_players as t2 ON t2.id = m.team2_id'
                    ." WHERE m.m_id = '".$id."' AND m.k_type = '0'"
                    .'  ORDER BY '.$orderby;
        } else {
            $query = 'SELECT m.*,t.t_name as home_team, t2.t_name as away_team,IF(m.score1>m.score2,t.t_name,t2.t_name) as winner, IF(m.score1>m.score2,t.id,t2.id) as winnerid,IF(m.score1<m.score2,t.t_name,t2.t_name) as looser, IF(m.score1<m.score2,t.id,t2.id) as looserid'
                    .' FROM #__bl_match as m LEFT JOIN #__bl_teams as t ON t.id = m.team1_id LEFT JOIN #__bl_teams as t2 ON t2.id = m.team2_id'
                    ." WHERE m.m_id = '".$id."' AND m.k_type = '0'"
                    .' ORDER BY '.$orderby;
        }
        $this->db->setQuery($query);
        $match = $this->db->loadObjectList();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }

        ///venue
        $is_venue[] = JHTML::_('select.option',  0, JText::_('BLFA_SELVENUE'), 'id', 'v_name');
        $query = 'SELECT * FROM #__bl_venue ORDER BY v_name';
        $this->db->setQuery($query);
        $venue = $this->db->loadObjectList();
        if (count($venue)) {
            $is_venue = array_merge($is_venue, $venue);
        }

        //$row->venue_name = JHTML::_('select.genericlist',   $is_venue, 'venue_id[]', 'class="inputbox" size="1"', 'id', 'v_name', $row->venue_id);
        if ($match) {
            foreach ($match as $m) {
                //$m->venue_name = '';
                $query = "SELECT v_name FROM #__bl_venue WHERE id = {$m->venue_id}";
                $this->db->setQuery($query);
                $m->venue_name = $this->db->loadResult();
                //$m->venue_name = JHTML::_('select.genericlist',   $is_venue, 'venue_id[]', 'class="selectpicker form-control" size="1"', 'id', 'v_name', $m->venue_id);;
            }
        }

        $this->_lists['match'] = $match;

        $this->_lists['venue_name'] = JHTML::_('select.genericlist',   $is_venue, 'venue_id_new[]', 'class="selectpicker" size="1"', 'id', 'v_name');
    }
    public function getMatchesFMDDE($id)
    {
        $orderby = $this->t_type ? 'm.k_stage,m.k_ordering' : 'm.id';
        if ($this->t_single) {
            $query = "SELECT m.*,CONCAT(t.first_name,' ',t.last_name) as home_team, CONCAT(t2.first_name,' ',t2.last_name) as away_team,IF(m.score1>m.score2,CONCAT(t.first_name,' ',t.last_name),CONCAT(t2.first_name,' ',t2.last_name)) as winner, IF(m.score1>m.score2,t.id,t2.id) as winnerid"
                .' FROM #__bl_match as m LEFT JOIN #__bl_players as t ON t.id = m.team1_id  LEFT JOIN #__bl_players as t2 ON t2.id = m.team2_id'
                ." WHERE m.m_id = '".$id."' AND m.k_type = '1'"
                .'  ORDER BY '.$orderby;
        } else {
            $query = 'SELECT m.*,t.t_name as home_team, t2.t_name as away_team,IF(m.score1>m.score2,t.t_name,t2.t_name) as winner, IF(m.score1>m.score2,t.id,t2.id) as winnerid'
                .' FROM #__bl_match as m LEFT JOIN #__bl_teams as t ON t.id = m.team1_id LEFT JOIN #__bl_teams as t2 ON t2.id = m.team2_id'
                ." WHERE m.m_id = '".$id."' AND m.k_type = '1'"
                .' ORDER BY '.$orderby;
        }
        $this->db->setQuery($query);
        $this->_lists['matchDE'] = $this->db->loadObjectList();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
    }
    public function getMatchesModer($id)
    {
        $query = 'SELECT m.*,t.t_name as home_team, t2.t_name as away_team, t.id as t1id, t2.id as t2id'
                .' FROM #__bl_match as m, #__bl_teams as t, #__bl_teams as t2'
                ." WHERE m.m_id = '".$id."' ".($this->season_id == -1 ? " AND m.m_single='0'" : '').' AND t.id = m.team1_id AND t2.id = m.team2_id AND (t.id = '.$this->tid.' OR t2.id = '.$this->tid.')'
                .' ORDER BY m.id';
        $this->db->setQuery($query);
        $match = $this->db->loadObjectList();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }

        ///venue
        $is_venue[] = JHTML::_('select.option',  0, JText::_('BLFA_SELVENUE'), 'id', 'v_name');
        $query = 'SELECT * FROM #__bl_venue ORDER BY v_name';
        $this->db->setQuery($query);
        $venue = $this->db->loadObjectList();
        if (count($venue)) {
            $is_venue = array_merge($is_venue, $venue);
        }

        //$row->venue_name = JHTML::_('select.genericlist',   $is_venue, 'venue_id[]', 'class=inputbox" size="1"', 'id', 'v_name', $row->venue_id);
        if ($match) {
            foreach ($match as $m) {
                //$m->venue_name = '';
                $query = "SELECT v_name FROM #__bl_venue WHERE id = {$m->venue_id}";
                $this->db->setQuery($query);
                $m->venue_name = $this->db->loadResult();
                //$m->venue_name = JHTML::_('select.genericlist',   $is_venue, 'venue_id[]', 'class="selectpicker form-control" size="1"', 'id', 'v_name', $m->venue_id);;
            }
        }

        $this->_lists['venue_name'] = JHTML::_('select.genericlist',   $is_venue, 'venue_id_new[]', 'class="selectpicker" size="1"', 'id', 'v_name');

        $this->_lists['match'] = $match;
    }
    public function getFilterseas()
    {
        $query = "SELECT s.*,CONCAT(tr.name,' ',s.s_name) as t_name,tr.t_single"
                .' FROM #__bl_season_players as t,#__bl_players as p,#__bl_seasons as s,#__bl_tournament as tr'
                .' WHERE s.published=1 AND tr.id=s.t_id AND s.s_id=t.season_id AND t.player_id=p.id AND p.usr_id='.$this->_user->id
                .' ORDER BY s.ordering,s.s_id desc';
        $this->db->setQuery($query);
        $seasons = $this->db->loadObjectList();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
        $query = 'SELECT COUNT(*) FROM #__bl_matchday WHERE s_id=-1';
        $this->db->setQuery($query);
        $fr_md = $this->db->loadResult();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
        $seasons_as = $seasons;

        if ($fr_md) {
            $is_seas[] = JHTML::_('select.option',  -1, JText::_('BLFA_FRIENDLY_MATCHES'), 's_id', 't_name');
            if (count($seasons_as)) {
                $seasons_as = array_merge($is_seas, $seasons_as);
            }
        }

        $javascript = "onchange='document.filtrForm.submit();'";
        $this->_lists['seas_filtr'] = JHTML::_('select.genericlist',   $seasons_as, 'sid', 'class="selectpicker" size="1"'.$javascript, 's_id', 't_name', $this->season_id);
        if (!$this->season_id && count($seasons)) {
            $this->season_id = $seasons[0]->s_id;
        }
    }
    public function getteamsSeas($sid)
    {
        if ($this->t_single || ($sid == -1 && $this->acl == 3)) {
            $query = "SELECT CONCAT(t.first_name,' ',t.last_name) as t_name,t.id"
                    .' FROM #__bl_players as t , #__bl_season_players as st'
                    .' WHERE st.player_id = t.id AND st.season_id = '.$sid
                    .' ORDER BY t.first_name,t.last_name';
            if ($sid == -1) {
                $query = "SELECT t.*,CONCAT(t.first_name,' ',t.last_name) as t_name"
                .' FROM #__bl_players as t'
                .' ORDER BY t.first_name,t.last_name';
            }
        } else {
            $query = 'SELECT * FROM #__bl_teams as t , #__bl_season_teams as st'
                    .' WHERE st.team_id = t.id AND st.season_id = '.$sid
                    .' ORDER BY t.t_name';
            if ($sid == -1) {
                $query = 'SELECT * FROM #__bl_teams ORDER BY t_name';
            }
        }

        $this->db->setQuery($query);
        $team = $this->db->loadObjectList();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
        $is_team[] = JHTML::_('select.option',  0, (($this->t_single || ($sid == -1 && $this->acl == 3)) ? JText::_('BLFA_SELPLAYER') : JText::_('BLFA_SELTEAM')), 'id', 't_name');
        if ($this->t_type == 1 && $this->t_type == 2) {
            $is_team[] = JHTML::_('select.option',  -1, JText::_('BLBE_BYE'), 'id', 't_name');
        }
        if (count($team)) {
            $is_team = array_merge($is_team, $team);
        }
        $this->_lists['teams1'] = JHTML::_('select.genericlist',   $is_team, 'teams1', 'class="selectpicker" size="1" id="teams1" ', 'id', 't_name', 0);
        $this->_lists['teams2'] = JHTML::_('select.genericlist',   $is_team, 'teams2', 'class="selectpicker" size="1" id="teams2" ', 'id', 't_name', 0);

        return $is_team;
    }
    public function getMdMatch()
    {
        $query = "SELECT m.*,md.m_name,md.id as mdid, CONCAT(t1.first_name,' ',t1.last_name) as home_team, CONCAT(t2.first_name,' ',t2.last_name) as away_team, t1.id as hm_id,t2.id as aw_id"
                .' FROM #__bl_matchday as md, #__bl_match as m LEFT JOIN #__bl_players as t1 ON m.team1_id = t1.id LEFT JOIN #__bl_players as t2 ON m.team2_id = t2.id'
                .' WHERE m.m_id = md.id AND md.s_id='.$this->season_id.' AND (t1.usr_id = '.$this->_user->id.' OR t2.usr_id = '.$this->_user->id.')'
                .' AND md.id='.$this->mid
                .' ORDER BY m.m_date,m.m_time,m.id';

        $this->db->setQuery($query);

        $match = $this->db->loadObjectList();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }

        ///venue
        $is_venue[] = JHTML::_('select.option',  0, JText::_('BLFA_SELVENUE'), 'id', 'v_name');
        $query = 'SELECT * FROM #__bl_venue ORDER BY v_name';
        $this->db->setQuery($query);
        $venue = $this->db->loadObjectList();
        if (count($venue)) {
            $is_venue = array_merge($is_venue, $venue);
        }

        if ($match) {
            foreach ($match as $m) {
                //$m->venue_name = '';
                $query = "SELECT v_name FROM #__bl_venue WHERE id = {$m->venue_id}";
                $this->db->setQuery($query);
                $m->venue_name = $this->db->loadResult();

                //$m->venue_name = JHTML::_('select.genericlist',   $is_venue, 'venue_id[]', 'class="selectpicker form-control" size="1"', 'id', 'v_name', $m->venue_id);;
            }
        }
        $this->_lists['venue_name'] = JHTML::_('select.genericlist',   $is_venue, 'venue_id_new[]', 'class="selectpicker" size="1"', 'id', 'v_name');

        $this->_lists['match'] = $match;
    }

    public function getMBy($match)
    {
        if (isset($match) && $match->team1_id == -1 && $match->away_team) {
            $match->winner = $match->away_team;
            $match->looser = JText::_('BLBE_BYE');
            $match->looserid = -1;
            $match->winnerid = $match->team2_id;
            $match->m_played = 1;
        }
        if (isset($match) && $match->team2_id == -1 && $match->home_team) {
            $match->winner = $match->home_team;
            $match->winnerid = $match->team1_id;
            $match->looser = JText::_('BLBE_BYE');
            $match->looserid = -1;
            $match->m_played = 1;
        }
        if (isset($match) && $match->team1_id == -1 && $match->team2_id == -1) {
            $match->winner = JText::_('BLBE_BYE');
            $match->winnerid = -1;
            $match->m_played = 1;
        }

        return $match;
    }

    public function SeasModerfilter()
    {
        $javascript = "onchange='document.chg_team.submit();'";

        $query = 'SELECT m.* FROM #__bl_season_teams as t,#__bl_seasons as s,#__bl_matchday as m'
                .' WHERE s.published=1 AND m.s_id=s.s_id AND s.s_id=t.season_id AND t.team_id='.$this->tid.' AND s.s_id='.$this->season_id
                .' ORDER BY m.ordering';
        if ($this->season_id == -1) {
            $query = 'SELECT m.* FROM #__bl_matchday as m'
                .' WHERE m.s_id='.$this->season_id
                .' ORDER BY m.ordering';
        }
        $this->db->setQuery($query);
        $mdays = $this->db->loadObjectList();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
        $query = 'SELECT COUNT(*) FROM #__bl_season_teams as t,#__bl_seasons as s,#__bl_matchday as m'
                .' WHERE s.published=1 AND m.s_id=s.s_id AND s.s_id=t.season_id AND t.team_id='.$this->tid
                .' AND s.s_id='.$this->season_id.' AND m.id='.$this->mid;
        if ($this->season_id == -1) {
            $query = 'SELECT COUNT(*) FROM #__bl_matchday as m'
                .' WHERE m.s_id='.$this->season_id.' AND m.id='.$this->mid;
        }
        $this->db->setQuery($query);
        if (!$this->db->loadResult()) {
            $mid = isset($mdays[0]->id) ? $mdays[0]->id : 0;
        }
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
        if (!$this->mid && count($mdays)) {
            $this->mid = $mdays[0]->id;
        }

        $this->_lists['md_filtr'] = JHTML::_('select.genericlist',   $mdays, 'mid', 'class="selectpicker" size="1"'.$javascript, 'id', 'm_name', $this->mid);
        if (!count($mdays)) {
            $this->_lists['md_filtr'] = '';
        }
    }

    public function AdmMDSave()
    {
        $tid = JRequest::getVar('tid', 0, '', 'int');
        $sid = JRequest::getVar('sid', 0, '', 'int');
        $mid = JRequest::getVar('mid', 0, '', 'int');

        $user = JFactory::getUser();
        $t_single = JRequest::getVar('t_single', 0, 'post', 'int');
        $t_knock = JRequest::getVar('t_knock', 0, 'post', 'int');

        ///

        $s_id = JRequest::getVar('sid', 0, 'post', 'int');
        $this->season_id = $s_id;
        $post = JRequest::get('post');
        $post['s_id'] = $s_id;
        $post['k_format'] = JRequest::getVar('format_post', 0, 'post', 'int'); //format_fe
        $post['m_descr'] = JRequest::getVar('m_descr', '', 'post', 'string', JREQUEST_ALLOWRAW);
        $post['t_type'] = JRequest::getVar('t_knock', 0, 'post', 'int');
        if ($this->acl == 2 || $this->acl == 3) {
            unset($post['m_descr']);
            unset($post['m_name']);
            $jsmr_mark_played = $this->getJS_Config('jsmr_mark_played');
        }
        if ($this->acl == 3) {
            unset($post['s_id']);
        }
        $row = new JTableMday($this->db);
        if (!$row->bind($post)) {
            JError::raiseError(500, $row->getError());
        }
        if (!$row->check()) {
            JError::raiseError(500, $row->getError());
        }
        if (!$row->store()) {
            JError::raiseError(500, $row->getError());
        }
        $row->checkin();
        $row->load($row->id);
        $mj = 0;

        if ($row->s_id == -1) {
            $t_type = 0;
        } else {
            //$query = "SELECT tr.t_type FROM #__bl_seasons as s,#__bl_tournament as tr WHERE s.published=1 AND tr.id=s.t_id AND s.s_id=".$row->s_id;
            //$this->db->setQuery($query);

            $t_type = $post['t_type'];
        }

        if ($this->acl == 3) {
            $query = 'SELECT id FROM #__bl_players WHERE usr_id='.$this->_user->id;
            $this->db->setQuery($query);
            $playerid = $this->db->LoadResult();
        }

        $prevarr = array();

        if ($this->acl == 1 && $t_knock) {
            if (isset($_POST['teams_kn']) && count($_POST['teams_kn'])) {
                foreach ($_POST['teams_kn'] as $home_team) {
                    $match = new JTableMatch($this->db);

                    $match->load(isset($_POST['match_id'][$mj]) ? $_POST['match_id'][$mj] : 0);

                    $match->m_id = $row->id;

                    $match->team1_id = intval($home_team);
//update	
                    $match->team2_id = intval($_POST['teams_kn_aw'][$mj]);
                    if ($_POST['res_kn_1'][$mj] != '') {
                        $match->score1 = intval($_POST['res_kn_1'][$mj]);
                    }
                    if ($_POST['res_kn_1_aw'][$mj] != '') {
                        $match->score2 = intval($_POST['res_kn_1_aw'][$mj]);
                    }
                    $match->k_ordering = $mj;
                    $match->k_stage = 1;

                    if (isset($_POST['kn_match_played_'.$mj])) {
                        $match->m_played = intval($_POST['kn_match_played_'.$mj]);
                    } else {
                        $match->m_played = 0;
                    }

                    if (!isset($_POST['res_kn_1'][$mj]) || !isset($_POST['res_kn_1_aw'][$mj]) || $_POST['res_kn_1'][$mj] == '' || $_POST['res_kn_1_aw'][$mj] == '') {
                        $match->m_played = 0;
                    }

                    if (!$match->id) {
                        $query = 'SELECT venue_id FROM #__bl_teams WHERE id='.$match->team1_id;
                        $this->db->setQuery($query);
                        $venue = $this->db->loadResult();
                        if ($venue) {
                            $match->venue_id = $venue;
                        }
                    }

                    $match->published = 1;
                    if (!$match->check()) {
                        JError::raiseError(500, $match->getError());
                    }

                    if (!$match->store()) {
                        JError::raiseError(500, $match->getError());
                    }

                    $match->checkin();

                    $prevarr[] = $match->id;

                    ++$mj;
                }
            }

            $levcount = 2;
            //$lev = isset($_POST['teams_kn1'])?$_POST['teams_kn1']:0;
            while (isset($_POST['teams_kn_'.$levcount])) {
                $mj = 0;
                foreach ($_POST['teams_kn_'.$levcount] as $home_team) {
                    if ($levcount == 2) {
                        $match_1 = new JTableMatch($this->db);

                        $match_1->load(isset($_POST['match_id'][$mj * 2]) ? $_POST['match_id'][$mj * 2] : 0);
                        $match_2 = new JTableMatch($this->db);

                        $match_2->load(isset($_POST['match_id'][$mj * 2 + 1]) ? $_POST['match_id'][$mj * 2 + 1] : 0);
                    } else {
                        if ($_POST['final']) {
                            $match_1 = new JTableMatch($this->db);

                            $match_1->load(isset($_POST['matches_'.($levcount - 1)][$mj * 2]) ? $_POST['matches_'.($levcount - 1)][$mj * 2] : 0);
                            $match_2 = new JTableMatch($this->db);

                            $match_2->load(isset($_POST['lmatches_'.($levcount - 1)][$mj * 2]) ? $_POST['lmatches_'.($levcount - 1)][$mj * 2] : 0);
                        } else {
                            $match_1 = new JTableMatch($this->db);

                            $match_1->load(isset($_POST['matches_'.($levcount - 1)][$mj * 2]) ? $_POST['matches_'.($levcount - 1)][$mj * 2] : 0);
                            $match_2 = new JTableMatch($this->db);

                            $match_2->load(isset($_POST['matches_'.($levcount - 1)][$mj * 2 + 1]) ? $_POST['matches_'.($levcount - 1)][$mj * 2 + 1] : 0);
                        }
                    }

                    //if(($match_1->m_played && $match_1->team1_id !=0 && $match_1->team2_id !=0) || ($match_2->m_played && $match_1->team1_id !=0 && $match_1->team2_id !=0) || ($match_1->team1_id == -1 && $match_1->team2_id != 0) || ($match_1->team2_id == -1 && $match_1->team1_id != 0) || ($match_2->team1_id == -1 && $match_2->team2_id != 0) || ($match_2->team2_id == -1 && $match_2->team1_id != 0)){

                        $match = new JTableMatch($this->db);

                    $match->load(isset($_POST['matches_'.$levcount][$mj]) ? $_POST['matches_'.$levcount][$mj] : 0);

                    $match->m_id = $row->id;

                    $match->team1_id = intval($home_team);

                    if (!$match->team1_id && (($match_1->m_played && $match_1->team1_id != 0 && $match_1->team2_id != 0) || $match_1->team1_id == -1 || $match_1->team2_id == -1)) {
                        if ($match_1->team1_id == -1) {
                            $match->team1_id = $match_1->team2_id;
                        } elseif ($match_1->team2_id == -1) {
                            $match->team1_id = $match_1->team1_id;
                        }

                        if ($match_1->score1 > $match_1->score2) {
                            $match->team1_id = $match_1->team1_id;
                        } elseif ($match_1->score1 < $match_1->score2) {
                            $match->team1_id = $match_1->team2_id;
                        } else {
                            if ($match_1->aet1 > $match_1->aet2) {
                                $match->team1_id = $match_1->team1_id;
                            } elseif ($match_1->aet1 < $match_1->aet2) {
                                $match->team1_id = $match_1->team2_id;
                            } else {
                                if ($match_1->p_winner == $match_1->team1_id) {
                                    $match->team1_id = $match_1->team1_id;
                                } elseif ($match_1->p_winner == $match_1->team2_id) {
                                    $match->team1_id = $match_1->team2_id;
                                }
                            }
                        }
                    }

                    $match->team2_id = intval($_POST['teams_kn_aw_'.$levcount][$mj]);

                    if (!$match->team2_id && (($match_2->m_played && $match_2->team1_id != 0 && $match_2->team2_id != 0) || $match_2->team1_id == -1 || $match_2->team2_id == -1)) {
                        if ($match_2->team1_id == -1) {
                            $match->team2_id = $match_2->team2_id;
                        } elseif ($match_2->team1_id == -1) {
                            $match->team2_id = $match_2->team1_id;
                        }

                        if ($match_2->score1 > $match_2->score2) {
                            $match->team2_id = $match_2->team1_id;
                        } elseif ($match_2->score1 < $match_2->score2) {
                            $match->team2_id = $match_2->team2_id;
                        } else {
                            if ($match_2->aet1 > $match_2->aet2) {
                                $match->team2_id = $match_2->team1_id;
                            } elseif ($match_2->aet1 < $match_2->aet2) {
                                $match->team2_id = $match_2->team2_id;
                            } else {
                                if ($match_2->p_winner == $match_2->team1_id) {
                                    $match->team2_id = $match_2->team1_id;
                                } elseif ($match_2->p_winner == $match_2->team2_id) {
                                    $match->team2_id = $match_2->team2_id;
                                }
                            }
                        }
                    }

                    if ($_POST['res_kn_'.$levcount][$mj] != '') {
                        $match->score1 = intval($_POST['res_kn_'.$levcount][$mj]);
                    }
                    if ($_POST['res_kn_'.$levcount.'_aw'][$mj] != '') {
                        $match->score2 = intval($_POST['res_kn_'.$levcount.'_aw'][$mj]);
                    }
                    $match->k_ordering = $mj;
                    $match->k_stage = $levcount;

                    if (isset($_POST['kn_match_played_'.$mj.'_'.$levcount])) {
                        $match->m_played = intval($_POST['kn_match_played_'.$mj.'_'.$levcount]);
                    } else {
                        $match->m_played = 0;
                    }

                    if ($_POST['res_kn_'.$levcount.'_aw'][$mj] == '' || $_POST['res_kn_'.$levcount][$mj] == '') {
                        $match->m_played = 0;
                    }

                    if (!$match->id) {
                        $query = 'SELECT venue_id FROM #__bl_teams WHERE id='.$match->team1_id;
                        $this->db->setQuery($query);
                        $venue = $this->db->loadResult();
                        if ($venue) {
                            $match->venue_id = $venue;
                        }
                    }

                    if (!$_POST['res_kn_'.$levcount][$mj] && !$_POST['res_kn_'.$levcount.'_aw'][$mj]) {
                        $match->m_played = isset($match->m_played) ? $match->m_played : 1;
                    } else {
                        $match->m_played = isset($match->m_played) ? $match->m_played : 1;
                    }

                    $match->published = 1;
                    if (!$match->check()) {
                        JError::raiseError(500, $match->getError());
                    }

                    if (!$match->store()) {
                        JError::raiseError(500, $match->getError());
                    }

                    $match->checkin();
                    ++$mj;

                    $prevarr[] = $match->id;
                    //}
                }
                ++$levcount;
            }
            ////////////////////////////////////////
            $levcount = 1;
            while (isset($_POST['lteams_kn_'.$levcount])) {
                $mj = 0;
                foreach ($_POST['lteams_kn_'.$levcount] as $home_team) {
                    if ($levcount == 1) {
                        $match_1 = new JTableMatch($this->db);

                        $match_1->load(isset($_POST['match_id'][$mj * 2]) ? $_POST['match_id'][$mj * 2] : 0);
                        $match_2 = new JTableMatch($this->db);

                        $match_2->load(isset($_POST['match_id'][$mj * 2 + 1]) ? $_POST['match_id'][$mj * 2 + 1] : 0);
                    } elseif (($levcount % 2) == 0) {
                        $match_1 = new JTableMatch($this->db);
                        $num = intval($levcount / 4) ? ceil($levcount / 4) : floor($levcount / 4);
                        if ($levcount == 8 || $levcount == 10) {
                            $num += 1;
                        }
                        $match_1->load(isset($_POST['lmatches_'.($levcount - 1)][$mj]) ? $_POST['lmatches_'.($levcount - 1)][$mj] : 0);
                        $match_2 = new JTableMatch($this->db);

                        $match_2->load(isset($_POST['matches_'.($levcount - $num)][$mj]) ? $_POST['matches_'.($levcount - $num)][$mj] : 0);
                    } else {
                        $match_1 = new JTableMatch($this->db);

                        $match_1->load(isset($_POST['lmatches_'.($levcount - 1)][$mj]) ? $_POST['lmatches_'.($levcount - 1)][$mj] : 0);
                        $match_2 = new JTableMatch($this->db);

                        $match_2->load(isset($_POST['lmatches_'.($levcount - 1)][$mj + 1]) ? $_POST['lmatches_'.($levcount - 1)][$mj + 1] : 0);
                    }

//////////////&&&&
                    //if(($match_1->m_played && $match_1->team1_id !=0 && $match_1->team2_id !=0) || ($match_2->m_played && $match_1->team1_id !=0 && $match_1->team2_id !=0) || ($match_1->team1_id == -1 && $match_1->team2_id != 0) || ($match_1->team2_id == -1 && $match_1->team1_id != 0) || ($match_2->team1_id == -1 && $match_2->team2_id != 0) || ($match_2->team2_id == -1 && $match_2->team1_id != 0)){

                        $match = new JTableMatch($this->db);

                    $match->load(isset($_POST['lmatches_'.$levcount][$mj]) ? $_POST['lmatches_'.$levcount][$mj] : 0);

                    $match->m_id = $row->id;

                    $match->team1_id = intval($home_team);

                    if (!$match->team1_id && (($match_1->m_played && $match_1->team1_id != 0 && $match_1->team2_id != 0) || $match_1->team1_id == -1 || $match_1->team2_id == -1)) {
                        if ($match_1->team1_id == -1) {
                            $match->team1_id = $match_1->team2_id;
                        } elseif ($match_1->team2_id == -1) {
                            $match->team1_id = $match_1->team1_id;
                        }

                        if ($match_1->score1 > $match_1->score2) {
                            $match->team1_id = $match_1->team1_id;
                        } elseif ($match_1->score1 < $match_1->score2) {
                            $match->team1_id = $match_1->team2_id;
                        } else {
                            if ($match_1->aet1 > $match_1->aet2) {
                                $match->team1_id = $match_1->team1_id;
                            } elseif ($match_1->aet1 < $match_1->aet2) {
                                $match->team1_id = $match_1->team2_id;
                            } else {
                                if ($match_1->p_winner == $match_1->team1_id) {
                                    $match->team1_id = $match_1->team1_id;
                                } elseif ($match_1->p_winner == $match_1->team2_id) {
                                    $match->team1_id = $match_1->team2_id;
                                }
                            }
                        }
                    }

                    $match->team2_id = intval($_POST['lteams_kn_aw_'.$levcount][$mj]);
                    $match->k_type = intval($_POST['lk_type_'.$levcount][$mj]);

                    if (!$match->team2_id && (($match_2->m_played && $match_2->team1_id != 0 && $match_2->team2_id != 0) || $match_2->team1_id == -1 || $match_2->team2_id == -1)) {
                        if ($match_2->team1_id == -1) {
                            $match->team2_id = $match_2->team2_id;
                        } elseif ($match_2->team1_id == -1) {
                            $match->team2_id = $match_2->team1_id;
                        }

                        if ($match_2->score1 > $match_2->score2) {
                            $match->team2_id = $match_2->team1_id;
                        } elseif ($match_2->score1 < $match_2->score2) {
                            $match->team2_id = $match_2->team2_id;
                        } else {
                            if ($match_2->aet1 > $match_2->aet2) {
                                $match->team2_id = $match_2->team1_id;
                            } elseif ($match_2->aet1 < $match_2->aet2) {
                                $match->team2_id = $match_2->team2_id;
                            } else {
                                if ($match_2->p_winner == $match_2->team1_id) {
                                    $match->team2_id = $match_2->team1_id;
                                } elseif ($match_2->p_winner == $match_2->team2_id) {
                                    $match->team2_id = $match_2->team2_id;
                                }
                            }
                        }
                    }
                        //var_dump($match);die();

                        if ($_POST['lres_kn_'.$levcount][$mj] != '') {
                            $match->score1 = intval($_POST['lres_kn_'.$levcount][$mj]);
                        }
                    if ($_POST['lres_kn_'.$levcount.'_aw'][$mj] != '') {
                        $match->score2 = intval($_POST['lres_kn_'.$levcount.'_aw'][$mj]);
                    }
                    $match->k_ordering = $mj;
                    $match->k_stage = $levcount;

                    if (isset($_POST['lkn_match_played_'.$mj.'_'.$levcount])) {
                        $match->m_played = intval($_POST['lkn_match_played_'.$mj.'_'.$levcount]);
                    } else {
                        $match->m_played = 0;
                    }

                    if ($_POST['lres_kn_'.$levcount.'_aw'][$mj] == '' || $_POST['lres_kn_'.$levcount][$mj] == '') {
                        $match->m_played = 0;
                    }

                    if (!$match->id) {
                        $query = 'SELECT venue_id FROM #__bl_teams WHERE id='.$match->team1_id;
                        $this->db->setQuery($query);
                        $venue = $this->db->loadResult();
                        if ($venue) {
                            $match->venue_id = $venue;
                        }
                    }

                    if (!$_POST['lres_kn_'.$levcount][$mj] && !$_POST['lres_kn_'.$levcount.'_aw'][$mj]) {
                        $match->m_played = isset($match->m_played) ? $match->m_played : 1;
                    } else {
                        $match->m_played = isset($match->m_played) ? $match->m_played : 1;
                    }
                        //////////////////
                       // if($match_1->m_played == 0 || $match_2->m_played == 0){
                            //$match->m_played = 0;
                        //}
                        //else{
                        // $match->m_played = 1;
                        //}
                        if (($match_1->team1_id == -1 || $match_1->team2_id == -1 || $match_2->team1_id == -1 or $match_2->team2_id == -1) && intval($_POST['lkn_match_played_'.$mj.'_'.$levcount])) {
                            $match->m_played = 1;
                        }
                    $match->published = 1;
                    if (!$match->check()) {
                        JError::raiseError(500, $match->getError());
                    }

                    if (!$match->store()) {
                        JError::raiseError(500, $match->getError());
                    }

                    $match->checkin();
                    ++$mj;

                    $prevarr[] = $match->id;
                    //}
                }
                ++$levcount;
            }

            ////////////////////////////////////////

            $this->db->setQuery('DELETE FROM #__bl_match WHERE m_id = '.$row->id.' AND id NOT IN ('.implode(',', $prevarr).')');

            $this->db->query();
            $query = 'SELECT id FROM #__bl_match WHERE m_id = '.$row->id.' AND id NOT IN ('.implode(',', $prevarr).')';
            $this->db->setQuery($query);
            $mcids = $this->db->loadColumn();

            if (count($mcids)) {
                $cids = implode(',', $mcids);
                $this->db->setQuery('DELETE FROM #__bl_squard WHERE match_id IN ('.$cids.')');
                $this->db->query();
                $this->db->setQuery('DELETE FROM #__bl_match_events WHERE match_id IN ('.$cids.')');
                $this->db->query();
                $this->db->setQuery('DELETE FROM #__bl_subsin WHERE match_id IN ('.$cids.')');
                $this->db->query();
                $this->db->setQuery('DELETE FROM #__bl_mapscore WHERE m_id IN ('.$cids.')');
                $this->db->query();
            }
        } else {
            $arr_match = array();

            if (isset($_POST['home_team']) && count($_POST['home_team'])) {
                foreach ($_POST['home_team'] as $home_team) {
                    $match = new JTableMatch($this->db);

                    $match->load($_POST['match_id'][$mj]);
                    if (!$this->t_type) {  /////!!!!!!!!!!!!!!!!!!!!!!!!!!!WARNING
                        $match->m_id = $row->id;

                        $match->team1_id = intval($home_team);

                        $match->team2_id = intval($_POST['away_team'][$mj]);

                        if ($this->acl != 1 && $jsmr_mark_played == 0 && $match->m_played == 1) {
                        } else {
                            $match->score1 = isset($_POST['home_score'][$mj]) ? intval($_POST['home_score'][$mj]) : $match->score1;
                            $match->score2 = isset($_POST['away_score'][$mj]) ? intval($_POST['away_score'][$mj]) : $match->score2;
                        }

                        $match->is_extra = isset($_POST['extra_time'][$mj]) ? intval($_POST['extra_time'][$mj]) : 0;
                        $match->published = 1;
                    }
                    if ($this->acl != 1 && $jsmr_mark_played == 0 && $match->m_played == 1) {
                    } else {
                        $match->m_played = intval($_POST['match_played'][$mj]);
                    }
                    $match->m_date = strval($_POST['match_data'][$mj]);
                    $match->venue_id = intval($_POST['venue_id'][$mj]);
                    $match->m_time = strval($_POST['match_time'][$mj]);

                    if ($this->acl == 3 && $row->s_id == -1) {
                        $match->m_single = 1;
                    }
                    if ($this->acl == 3) {
                        $query = 'SELECT COUNT(*) FROM #__bl_players WHERE usr_id='.$this->_user->id.' AND (id='.$match->team1_id.' OR id='.$match->team2_id.')';
                        $this->db->setQuery($query);

                        if (!$this->db->loadResult()) {
                            JError::raiseError(500, $match->getError());
                        }
                    }

                    if (!$match->id) {
                        $query = 'SELECT venue_id FROM #__bl_teams WHERE id='.intval($home_team);
                        $this->db->setQuery($query);
                        $venue = $this->db->loadResult();
                        if ($venue) {
                            $match->venue_id = $venue;
                        }
                    }
                    if ($this->acl == 2) {
                        $query = 'SELECT COUNT(*) FROM #__bl_teams as t, #__bl_moders as m WHERE m.tid=t.id AND m.uid='.$user->id.' AND (t.id='.$match->team1_id.' OR t.id='.$match->team2_id.')';
                        $this->db->setQuery($query);

                        if (!$this->db->loadResult()) {
                            JError::raiseError(500, $match->getError());
                        }
                    }
                    if (!$match->check()) {
                        JError::raiseError(500, $match->getError());
                    }

                    if (!$match->store()) {
                        JError::raiseError(500, $match->getError());
                    }

                    $match->checkin();

                    $arr_match[] = $match->id;

                    ++$mj;
                }
                if ($this->acl == 1) {
                    $this->db->setQuery('DELETE FROM #__bl_match WHERE id NOT IN ('.implode(',', $arr_match).') AND m_id = '.$row->id);

                    $this->db->query();

                    $query = 'SELECT id FROM #__bl_match WHERE id NOT IN ('.implode(',', $arr_match).') AND m_id = '.$row->id;
                    $this->db->setQuery($query);
                    $mcids = $this->db->loadColumn();
                    if (count($mcids)) {
                        $cids = implode(',', $mcids);
                        $this->db->setQuery('DELETE FROM #__bl_squard WHERE match_id IN ('.$cids.')');
                        $this->db->query();
                        $this->db->setQuery('DELETE FROM #__bl_match_events WHERE match_id IN ('.$cids.')');
                        $this->db->query();
                        $this->db->setQuery('DELETE FROM #__bl_subsin WHERE match_id IN ('.$cids.')');
                        $this->db->query();
                        $this->db->setQuery('DELETE FROM #__bl_mapscore WHERE m_id IN ('.$cids.')');
                        $this->db->query();
                    }
                } else {
                    if ($this->acl == 2) {
                        $this->db->setQuery('DELETE FROM #__bl_match WHERE id NOT IN ('.implode(',', $arr_match).') AND (team1_id = '.$tid.' OR team2_id='.$tid.') AND m_id = '.$row->id);
                    } else {
                        $this->db->setQuery('DELETE FROM #__bl_match WHERE id NOT IN ('.implode(',', $arr_match).') AND (team1_id = '.$playerid.' OR team2_id='.$playerid.') AND m_id = '.$row->id);
                    }
                    $this->db->query();
                    $error = $this->db->getErrorMsg();
                    if ($error) {
                        return JError::raiseError(500, $error);
                    }

                    if ($this->acl == 2) {
                        $this->db->setQuery('SELECT id FROM  #__bl_match WHERE id NOT IN ('.implode(',', $arr_match).') AND (team1_id = '.$tid.' OR team2_id='.$tid.') AND m_id = '.$row->id);
                    } else {
                        $this->db->setQuery('SELECT id FROM #__bl_match  WHERE id NOT IN ('.implode(',', $arr_match).') AND (team1_id = '.$playerid.' OR team2_id='.$playerid.') AND m_id = '.$row->id);
                    }
                    $mcids = $this->db->loadColumn();
                    $error = $this->db->getErrorMsg();
                    if ($error) {
                        return JError::raiseError(500, $error);
                    }

                    if (count($mcids)) {
                        $cids = implode(',', $mcids);
                        $this->db->setQuery('DELETE FROM #__bl_squard WHERE match_id IN ('.$cids.')');
                        $this->db->query();
                        $this->db->setQuery('DELETE FROM #__bl_match_events WHERE match_id IN ('.$cids.')');
                        $this->db->query();
                        $this->db->setQuery('DELETE FROM #__bl_subsin WHERE match_id IN ('.$cids.')');
                        $this->db->query();
                        $this->db->setQuery('DELETE FROM #__bl_mapscore WHERE m_id IN ('.$cids.')');
                        $this->db->query();
                        $this->db->setQuery("SELECT id FROM #__bl_extra_filds WHERE type='2'");
                        $efcid = $this->db->loadColumn();
                        if (count($efcid)) {
                            $efcids = implode(',', $efcid);
                            $this->db->setQuery('DELETE FROM #__bl_extra_values WHERE f_id IN ('.$efcids.') AND uid IN ('.$cids.')');
                            $this->db->query();
                        }
                    }
                }
            } else {
                if ($this->acl != 1 && !$this->t_type) { ///WARNING!!!!!!!!!!!!!!!!!!!!!
                    if ($this->acl == 2) {
                        $this->db->setQuery('DELETE FROM #__bl_match WHERE (team1_id = '.$tid.' OR team2_id='.$tid.') AND m_id = '.$row->id);
                    } else {
                        $this->db->setQuery('DELETE FROM #__bl_match WHERE (team1_id = '.$playerid.' OR team2_id='.$playerid.') AND m_id = '.$row->id);
                    }
                    $this->db->query();
                    $error = $this->db->getErrorMsg();
                    if ($error) {
                        return JError::raiseError(500, $error);
                    }
                    if ($this->acl == 2) {
                        $this->db->setQuery('SELECT id FROM #__bl_match WHERE (team1_id = '.$tid.' OR team2_id='.$tid.') AND m_id = '.$row->id);
                    } else {
                        $this->db->setQuery('SELECT id FROM #__bl_match WHERE (team1_id = '.$playerid.' OR team2_id='.$playerid.') AND m_id = '.$row->id);
                    }
                    $mcids = $this->db->loadColumn();
                    $error = $this->db->getErrorMsg();
                    if ($error) {
                        return JError::raiseError(500, $error);
                    }

                    if (count($mcids)) {
                        $cids = implode(',', $mcids);
                        $query = 'SELECT id FROM #__bl_match WHERE m_id IN ('.$cids.')';
                        $this->db->setQuery($query);
                        $mcids = $this->db->loadColumn();
                        $this->db->setQuery('DELETE FROM #__bl_squard WHERE match_id IN ('.$cids.')');
                        $this->db->query();
                        $this->db->setQuery('DELETE FROM #__bl_match_events WHERE match_id IN ('.$cids.')');
                        $this->db->query();
                        $this->db->setQuery('DELETE FROM #__bl_subsin WHERE match_id IN ('.$cids.')');
                        $this->db->query();
                        $this->db->setQuery('DELETE FROM #__bl_mapscore WHERE m_id IN ('.$cids.')');
                        $this->db->query();
                        $this->db->setQuery("SELECT id FROM #__bl_extra_filds WHERE type='2'");
                        $efcid = $this->db->loadColumn();
                        if (count($efcid)) {
                            $efcids = implode(',', $efcid);
                            $mcids = implode(',', $mcids);
                            $this->db->setQuery('DELETE FROM #__bl_extra_values WHERE f_id IN ('.$efcids.') AND uid IN ('.$cids.')');
                            $this->db->query();
                        }
                    }
                } elseif ($this->acl == 1) {
                    $query = 'SELECT id FROM #__bl_match WHERE m_id = '.$row->id;
                    $this->db->setQuery($query);
                    $mcids = $this->db->loadColumn();

                    $this->db->setQuery('DELETE FROM #__bl_match WHERE m_id = '.$row->id);

                    $this->db->query();
                    $this->db->setQuery('DELETE FROM #__bl_squard WHERE match_id = '.$row->id);
                    $this->db->query();
                    $this->db->setQuery('DELETE FROM #__bl_match_events WHERE match_id = '.$row->id);
                    $this->db->query();
                    $this->db->setQuery('DELETE FROM #__bl_subsin WHERE match_id = '.$row->id);
                    $this->db->query();
                    $this->db->setQuery('DELETE FROM #__bl_mapscore WHERE m_id = '.$row->id);
                    $this->db->query();
                    $this->db->setQuery("SELECT id FROM #__bl_extra_filds WHERE type='2'");
                    $efcid = $this->db->loadColumn();
                    if (count($efcid) && count($mcids)) {
                        $efcids = implode(',', $efcid);
                        $mcids = implode(',', $mcids);

                        $this->db->setQuery('DELETE FROM #__bl_extra_values WHERE f_id IN ('.$efcids.') AND uid IN ('.$mcids.')');

                        $this->db->query();
                    }
                }
            }
                        //update season table
                        require_once JPATH_COMPONENT_SITE.DIRECTORY_SEPARATOR.'sportleague'.DIRECTORY_SEPARATOR.'sportleague.php';
            classJsportPlugins::get('generateTableStanding', array('season_id' => $sid));
        }
        $this->id = $row->id;
        $this->tid = $tid;
        $this->mid = $mid;
        $this->season_id = $sid;
    }
    public function delAdmMD()
    {
        $cid = JRequest::getVar('cid', array(0), '', 'array');

        JArrayHelper::toInteger($cid, array(0));

        if (count($cid)) {
            $cids = implode(',', $cid);

            $query = 'SELECT id FROM #__bl_match WHERE m_id IN ('.$cids.')';
            $this->db->setQuery($query);
            $mcids = $this->db->loadColumn();

            $this->db->setQuery('DELETE FROM #__bl_matchday WHERE id IN ('.$cids.')');

            $this->db->query();
            $error = $this->db->getErrorMsg();
            if ($error) {
                return JError::raiseError(500, $error);
            }
            $this->db->setQuery('DELETE FROM #__bl_match WHERE m_id IN ('.$cids.')');
            $this->db->query();
            $this->db->setQuery('DELETE FROM #__bl_squard WHERE match_id IN ('.$cids.')');
            $this->db->query();
            $this->db->setQuery('DELETE FROM #__bl_match_events WHERE match_id IN ('.$cids.')');
            $this->db->query();
            $this->db->setQuery('DELETE FROM #__bl_subsin WHERE match_id IN ('.$cids.')');
            $this->db->query();
            $this->db->setQuery('DELETE FROM #__bl_mapscore WHERE m_id IN ('.$cids.')');
            $this->db->query();
            $this->db->setQuery("SELECT id FROM #__bl_extra_filds WHERE type='2'");
            $efcid = $this->db->loadColumn();
            if (count($efcid)) {
                $efcids = implode(',', $efcid);
                $mcids = implode(',', $mcids);
                //$this->db->setQuery("DELETE FROM #__bl_extra_values WHERE f_id IN (".$efcids.") AND uid IN (".$cids.")");
                $this->db->setQuery('DELETE FROM #__bl_extra_values WHERE f_id IN ('.$efcids.') AND uid IN ('.$mcids.')');
                $this->db->query();
            }
        }
    }
}
