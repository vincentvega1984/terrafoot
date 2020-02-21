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

class joinseasonJSModel extends JSPRO_Models
{
    public $_lists = null;
    public $s_id = null;
    public $_user = null;
    public $t_single = null;

    public function __construct()
    {
        parent::__construct();

        $this->s_id = JRequest::getVar('sid', 0, '', 'int');
        $this->_user = JFactory::getUser();
        if (!$this->s_id) {
            JError::raiseError(403, JText::_('Access Forbidden'));

            return;
        }
    }

    public function getData()
    {
        if ($this->_user->get('guest')) {
            $return_url = $_SERVER['REQUEST_URI'];
            $return_url = base64_encode($return_url);

            if (getVer() == '1.6') {
                $uopt = 'com_users';
            } else {
                $uopt = 'com_user';
            }
            $return = 'index.php?option='.$uopt.'&view=login&return='.$return_url;

            // Redirect to a login form
            $this->mainframe->redirect($return, JText::_('BLMESS_NOT_LOGIN'));
        }
        //title
        $tourn = $this->getTournOpt($this->s_id);
        $this->t_single = $tourn->t_single;
        $this->_params = $this->JS_PageTitle($tourn->name);
        $season_par = $this->getSParametrs($this->s_id);
        $this->_lists['season_par'] = $season_par;
        $reg_start = mktime(substr($season_par->reg_start, 11, 2), substr($season_par->reg_start, 14, 2), 0, substr($season_par->reg_start, 5, 2), substr($season_par->reg_start, 8, 2), substr($season_par->reg_start, 0, 4));
        $reg_end = mktime(substr($season_par->reg_end, 11, 2), substr($season_par->reg_end, 14, 2), 0, substr($season_par->reg_end, 5, 2), substr($season_par->reg_end, 8, 2), substr($season_par->reg_end, 0, 4));
        $unable_reg = 0;
        if ($this->t_single) {
            $query = 'SELECT COUNT(*) FROM #__bl_players as t , #__bl_season_players as st WHERE st.player_id = t.id AND st.season_id = '.$this->s_id;
        } else {
            $query = 'SELECT COUNT(*) FROM #__bl_teams as t , #__bl_season_teams as st WHERE st.team_id = t.id AND st.season_id = '.$this->s_id;
        }
        $this->db->setQuery($query);
        $part_count = $this->db->loadResult();

        if ($season_par->s_reg && ($part_count < $season_par->s_participant || $season_par->s_participant == 0) && ($reg_start <= time() && (time() <= $reg_end || $season_par->reg_end == '0000-00-00 00:00:00'))) {
            $unable_reg = 1;
        }
        $this->_lists['part_count'] = $part_count;
        $this->_lists['unable_reg'] = $unable_reg;
        $this->getcaplist();
        $this->_lists['teams_season'] = $this->teamsToModer();
        $this->_lists['panel'] = $this->getePanel($this->_lists['teams_season'], 0, null, 0);
    }
    public function getcaplist()
    {
        $query = 'SELECT t.* FROM #__bl_teams as t, #__bl_moders as m'
                .' WHERE m.tid=t.id AND m.uid='.$this->_user->id
                .' ORDER BY t.t_name';
        $this->db->setQuery($query);
        $cap = $this->db->loadObjectList();

        $this->_lists['no_team'] = 0;
        if (!count($cap) && !$this->t_single) {
            $this->_lists['no_team'] = 1;
        }

        if (!$this->_user->id) {
            $this->message = JText::_('BLMESS_NOT_REG');
            $this->_lists['unable_reg'] = 0;
        }

        $this->_lists['cap'] = JHTML::_('select.genericlist',   $cap, 'reg_team', 'class="inputbox selectpicker" size="1"', 'id', 't_name', 0);
    }
    public function joinSave()
    {
        $user = JFactory::getUser();

        if ($user->get('guest')) {
            JError::raiseError(403, JText::_('Access Forbidden'));

            return;
        }

        $is_team = JRequest::getVar('is_team', 0, 'post', 'int');
        $reg_team = JRequest::getVar('reg_team', 0, 'post', 'int');
        $sid = JRequest::getVar('sid', 0, 'post', 'int');
        $unable_reg = 0;
        $message = '';

        $tourn = $this->getTournOpt($sid);
        $t_single = $tourn->t_single;

        $season_par = $this->getSParametrs($sid);

        $reg_start = mktime(substr($season_par->reg_start, 11, 2), substr($season_par->reg_start, 14, 2), 0, substr($season_par->reg_start, 5, 2), substr($season_par->reg_start, 8, 2), substr($season_par->reg_start, 0, 4));
        $reg_end = mktime(substr($season_par->reg_end, 11, 2), substr($season_par->reg_end, 14, 2), 0, substr($season_par->reg_end, 5, 2), substr($season_par->reg_end, 8, 2), substr($season_par->reg_end, 0, 4));

        if ($t_single) {
            $query = 'SELECT COUNT(*) FROM #__bl_players as t , #__bl_season_players as st WHERE st.player_id = t.id AND st.season_id = '.$sid;
        } else {
            $query = 'SELECT COUNT(*) FROM #__bl_teams as t , #__bl_season_teams as st WHERE st.team_id = t.id AND st.season_id = '.$sid;
        }
        $this->db->setQuery($query);
        $part_count = $this->db->loadResult();

        if ($season_par->s_reg && ($part_count < $season_par->s_participant || $season_par->s_participant == 0) && ($reg_start <= time() && (time() <= $reg_end || $season_par->reg_end == '0000-00-00 00:00:00'))) {
            $unable_reg = 1;
        }

        if ($unable_reg && $sid) {
            if ($is_team) {
                $query = 'INSERT INTO #__bl_season_teams(season_id,team_id,regtype) VALUES('.$sid.','.$reg_team.",'1')";
                $this->db->setQuery($query);
                $this->db->query();
                $message = JText::_('BLFA_JOINSEASON');
            } else {
                $query = 'SELECT id FROM #__bl_players WHERE usr_id = '.$reg_team;
                $this->db->setQuery($query);
                $bluid = $this->db->loadResult();
                if ($bluid) {
                    $query = 'INSERT INTO #__bl_season_players(season_id,player_id,regtype) VALUES('.$sid.','.$bluid.',1)';
                    $this->db->setQuery($query);
                    $this->db->query();
                    $message = JText::_('BLFA_JOINSEASON');
                } else {
                    $message = 'Register in component first';
                }
            }
        } else {
            $message = 'CCCCC';
        }

        return $message;
    }
}
