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

    require dirname(__FILE__).'/../../includes/pagination_mobile.php';

class admin_playerJSModel extends JSPRO_Models
{
    public $_data = null;
    public $_lists = null;
    public $_total = null;
    public $_user = null;
    public $_pagination = null;
    public $limit = null;
    public $limitstart = null;
    public $season_id = null;
    public $tid = null;
    public $t_single = null;
    public $t_type = null;
    /*moder rights  1 - season admin, 2 - team moderator, 3 - registered player*/
    public $acl = null;

    public function __construct($acl)
    {
        parent::__construct();
        $mainframe = JFactory::getApplication();

        $this->acl = $acl;
        $this->t_type = new stdClass();
        // Get the pagination request variables
        $this->limit = $mainframe->getUserStateFromRequest('com_joomsport.pl_jslimit', 'jslimit', 20, 'int');
        $this->limitstart = JRequest::getVar('page', 1, '', 'int');
        $this->limitstart = intval($this->limitstart) > 1 ? $this->limitstart : 1;
        $this->season_id = $mainframe->getUserStateFromRequest('com_joomsport.sid', 'sid', 0, 'int');
        if ($this->acl == 2) {
            $this->season_id = $this->mainframe->getUserStateFromRequest('com_joomsport.moderseason', 'moderseason', 0, 'int');
        }

        if ($this->acl == 2) {
            $this->tid = JRequest::getVar('tid', 0, '', 'int');
            $this->t_single = 0;
            $query = "SELECT CONCAT(tr.name,' ',s.s_name) as t_name,s.s_id as id FROM #__bl_season_teams as t,#__bl_seasons as s,#__bl_tournament as tr WHERE s.published=1 AND tr.id=s.t_id AND s.s_id=t.season_id AND t.team_id=".$this->tid.' ORDER BY s.s_id desc';
            $this->db->setQuery($query);
            $seass = $this->db->loadObjectList();
            $error = $this->db->getErrorMsg();
            if ($error) {
                return JError::raiseError(500, $error);
            }
            if (!$this->season_id) {
                $this->season_id = (isset($seass[0]->id)) ? ($seass[0]->id) : ('');
            };
            $isinseas = false;
            for ($j = 0;$j < count($seass);++$j) {
                if ($this->season_id == $seass[$j]->id) {
                    $isinseas = true;
                }
            }
            if (!$isinseas && count($seass)) {
                $this->season_id = $seass[0]->id;
            }
        }

        $user = JFactory::getUser();
        $this->_user = $user;

        if ($user->get('guest')) {
            $return_url = $_SERVER['REQUEST_URI'];
            $return_url = base64_encode($return_url);
            if (getVer() >= '1.6') {
                $uopt = 'com_users';
            } else {
                $uopt = 'com_user';
            }
            $return = 'index.php?option='.$uopt.'&view=login&return='.$return_url;
            $this->mainframe->redirect($return, JText::_('BLMESS_NOT_LOGIN'));
        }

        if ($this->acl == 2) {
            $this->_lists['moder_addplayer'] = $this->getJS_Config('moder_addplayer');

            $query = 'SELECT COUNT(*) FROM #__bl_teams as t, #__bl_moders as m WHERE m.tid=t.id AND m.uid='.$user->id.' AND t.id='.$this->tid;
            $this->db->setQuery($query);
            if (!$this->db->loadResult() || !$this->_lists['moder_addplayer']) {
                JError::raiseError(403, JText::_('Access Forbidden'));

                return;
            }
        } elseif ($this->acl == 1) {
            if (!$this->season_id) {
                JError::raiseError(403, JText::_('Access Forbidden'));

                return;
            }
        }
    }
    public function admAccess()
    {
        $this->_lists['jssa_editplayer'] = $this->t_single ? $this->getJS_Config('jssa_editplayer_single') : $this->getJS_Config('jssa_editplayer');
        $this->_lists['jssa_deleteplayers'] = $this->t_single ? $this->getJS_Config('jssa_deleteplayers_single') : $this->getJS_Config('jssa_deleteplayers');
    }
    public function getData()
    {
        if ($this->season_id) {
            $tourn = $this->getTournOpt($this->season_id);
            $this->t_single = isset($tourn->t_single) ? $tourn->t_single : '';
            $this->t_type = 0;
            $this->_lists['t_single'] = $this->t_single;
            $this->_lists['tournname'] = isset($tourn->name) ? $tourn->name : '';
        }

        if ($this->acl == 1) {
            $this->admAccess();
        } elseif ($this->acl == 2) {
            $this->getGlobFilters();
        }
        $this->getPagination();
        $this->_params = $this->JS_PageTitle(JText::_('BLFA_PLAYER_EDIT'));

        if ($this->acl == 2) {
            $query = 'SELECT p.* FROM #__bl_players as p WHERE p.created_by = '.$this->_user->id.'  ORDER BY p.first_name,p.last_name';
        } else {
            if ($this->t_single) {
                $query = 'SELECT DISTINCT(p.id),p.* FROM #__bl_players as p LEFT JOIN #__bl_season_players as sp ON sp.player_id = p.id'
                    .' WHERE sp.season_id = '.$this->season_id
                    .'  ORDER BY p.first_name, p.last_name';
            } else {
                $query = 'SELECT DISTINCT(p.id),p.first_name, p.last_name, p.def_img'
                        .' FROM #__bl_players as p LEFT JOIN #__bl_players_team as pt ON pt.player_id = p.id LEFT JOIN #__bl_season_teams as st ON pt.team_id = st.team_id'
                        .' WHERE  (st.season_id = '.$this->season_id.' '.($this->season_id ? ' AND pt.season_id='.$this->season_id : '').') '
                        .' ORDER BY p.first_name, p.last_name';
            }
        }

        $this->db->setQuery($query, ($this->limitstart - 1) * $this->limit, $this->limit);
        $rows = $this->db->loadObjectList();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }

        ////////////photo
        for ($z = 0;$z < count($rows);++$z) {
            $def_img2 = '';
            if ($rows[$z]->def_img) {
                $query = 'SELECT ph_filename FROM  #__bl_photos as p WHERE p.id = '.$rows[$z]->def_img;
                $this->db->setQuery($query);
                $def_img2 = $this->db->loadResult();
            }
            if (!$def_img2) {
                $query = 'SELECT p.ph_name as name,p.id as id,p.ph_filename as filename FROM #__bl_assign_photos as ap, #__bl_photos as p WHERE ap.photo_id = p.id AND cat_type = 1 AND cat_id = '.$rows[$z]->id;
                $this->db->setQuery($query);
                $photos2 = $this->db->loadObjectList();
                if (isset($photos2[0])) {
                    $def_img2 = $photos2[0]->filename;
                }
            }
            $rows[$z]->photo = $def_img2;
        }
        $this->_data = $rows;
        //////////////////////////////
        if ($this->acl == 2) {
            $query = 'SELECT COUNT(*) FROM #__bl_season_teams as sp, #__bl_matchday as m WHERE m.s_id=sp.season_id AND sp.team_id = '.$this->tid;
            $this->db->setQuery($query);
            $this->_lists['enmd'] = $this->db->loadResult();
            $error = $this->db->getErrorMsg();
            if ($error) {
                return JError::raiseError(500, $error);
            }
            $query = 'SELECT m.* FROM #__bl_season_teams as t,#__bl_seasons as s,#__bl_matchday as m'
                    .' WHERE s.published=1 AND m.s_id=s.s_id AND s.s_id=t.season_id AND t.team_id='.$this->tid." AND s.s_id='".$this->season_id."'"
                    .' ORDER BY m.ordering';
            $this->db->setQuery($query);
            $mdays = $this->db->loadObjectList();
            $error = $this->db->getErrorMsg();
            if ($error) {
                return JError::raiseError(500, $error);
            }
            if (!count($mdays)) {
                $this->_lists['enmd'] = 0;
            }
        } else {
            $this->_lists['jssa_addexteam_single'] = $this->getJS_Config('jssa_addexteam_single');

            if ($this->_lists['jssa_addexteam_single'] == 1 && $this->t_single == 1) {
                $query = "SELECT DISTINCT(t.id),CONCAT(t.first_name,' ',t.last_name) as t_name"
                        .' FROM #__bl_players as t '
                        .' WHERE t.id NOT IN (SELECT player_id FROM #__bl_season_players WHERE season_id='.$this->season_id.')'
                        .' ORDER BY t.first_name,t.last_name';

                $this->db->setQuery($query);
                $teams_ex = $this->db->loadObjectList();
                $is_data[] = JHTML::_('select.option', '0', JText::_('BLFA_SELPLAYER'), 'id', 't_name');
                if (count($teams_ex)) {
                    $is_data = array_merge($is_data, $teams_ex);
                }

                $this->_lists['players_ex'] = JHTML::_('select.genericlist',   $is_data, 'players_ex', 'class="selectpicker" size="1"', 'id', 't_name', 0);
            }
        }

        $this->_lists['teams_season'] = $this->teamsToModer();
        $this->_lists['panel'] = $this->getePanel($this->_lists['teams_season'], 0, null, 0);
    }
    public function getPagination()
    {
        if (empty($this->_pagination)) {
            $this->_pagination = new JS_Pagination($this->getTotal(), $this->limitstart, $this->limit);
        }

        return $this->_pagination;
    }
    public function getTotal()
    {
        if ($this->acl == 1) {
            if ($this->t_single) {
                $query = 'SELECT COUNT(DISTINCT(p.id)) FROM #__bl_players as p LEFT JOIN #__bl_season_players as sp ON sp.player_id = p.id'
                        .' WHERE sp.season_id = '.$this->season_id
                        .'  ORDER BY p.first_name, p.last_name';
            } else {
                $query = 'SELECT COUNT(DISTINCT(p.id))'
                        .' FROM #__bl_players as p LEFT JOIN #__bl_players_team as pt ON pt.player_id = p.id LEFT JOIN #__bl_season_teams as st ON pt.team_id = st.team_id'
                        .' WHERE  (st.season_id = '.$this->season_id.' '.($this->season_id ? ' AND pt.season_id='.$this->season_id : '').')';
            }
        } else {
            $query = 'SELECT COUNT(*) FROM #__bl_players as p WHERE p.created_by = '.$this->_user->id;
        }

        $this->db->setQuery($query);

        $this->_total = $this->db->loadResult();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }

        return $this->_total;
    }

    public function SaveAdmExPl()
    {
        if ($this->acl == 1) {
            $players_ex = JRequest::getVar('players_ex', 0, 'post', 'int');
            if ($players_ex) {
                $query = 'INSERT INTO #__bl_season_players(season_id,player_id) VALUES('.$this->season_id.','.intval($players_ex).')';
                $this->db->setQuery($query);
                $this->db->query();
                $error = $this->db->getErrorMsg();
                if ($error) {
                    return JError::raiseError(500, $error);
                }
                classJsportPlugins::get('generateTableStanding', array('season_id' => $this->season_id));

                //update player list
                classJsportPlugins::get('generatePlayerList', array('season_id' => $this->season_id));
             
            }
        }
    }
}
