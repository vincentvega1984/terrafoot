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

class adlist_playerJSModel extends JSPRO_Models
{
    public $_data = null;
    public $_lists = null;
    public $_total = null;

    public $_pagination = null;
    public $limit = null;
    public $limitstart = null;
    public $season_id = null;

    public $t_single = null;
    public $t_type = null;

    public function __construct()
    {
        parent::__construct();
        $mainframe = JFactory::getApplication();

        // Get the pagination request variables
        $this->season_id = $mainframe->getUserStateFromRequest('com_joomsport.sid', 'sid', 0, 'int');
        $this->limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
        $this->limitstart = $mainframe->getUserStateFromRequest('com_joomsport.limitstart_pl'.$this->season_id, 'limitstart', 0, 'int');

        // In case limit has been changed, adjust limitstart accordingly
        $this->limitstart = ($this->limit != 0 ? (floor($this->limitstart / $this->limit) * $this->limit) : 0);
        $user = JFactory::getUser();
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
        if (!$this->season_id) {
            JError::raiseError(403, JText::_('Access Forbidden'));

            return;
        }
    }
    public function admAccess()
    {
        $this->_lists['jssa_editplayer'] = $this->getJS_Config('jssa_editplayer');
        $this->_lists['jssa_deleteplayers'] = $this->getJS_Config('jssa_deleteplayers');
    }
    public function getData()
    {
        $user = &JFactory::getUser();
        $this->admAccess();
        $this->getPagination();
        $this->_params = $this->JS_PageTitle('');

        $tourn = $this->getTournOpt($this->season_id);
        $this->t_single = $tourn->t_single;
        $this->t_type = $tourn->t_type;
        $this->_lists['t_single'] = $this->t_single;
        $this->_lists['tournname'] = $tourn->name;

        if ($this->t_single) {
            $query = 'SELECT DISTINCT(p.id),p.* FROM #__bl_players as p LEFT JOIN #__bl_season_players as sp ON sp.player_id = p.id'
                    .' WHERE sp.season_id = '.$this->season_id.' OR p.created_by ='.$user->id
                    .'  ORDER BY p.first_name, p.last_name';
        } else {
            $query = 'SELECT DISTINCT(p.id),p.first_name, p.last_name'
                    .' FROM #__bl_players as p LEFT JOIN #__bl_players_team as pt ON pt.player_id = p.id LEFT JOIN #__bl_season_teams as st ON pt.team_id = st.team_id'
                    .' WHERE  (st.season_id = '.$this->season_id.' '.($this->season_id ? ' AND pt.season_id='.$this->season_id : '').') OR p.created_by ='.$user->id
                    .' ORDER BY p.first_name, p.last_name';
        }
        $this->db->setQuery($query, $this->limitstart, $this->limit);
        $rows = $this->db->loadObjectList();
        $this->_data = $rows;

        $this->_lists['teams_season'] = $this->teamsToModer();
        $this->_lists['panel'] = $this->getePanel($this->_lists['teams_season'], 0, null, 0);
    }
    public function getPagination()
    {
        if (empty($this->_pagination)) {
            jimport('joomla.html.pagination');
            $this->_pagination = new JPagination($this->getTotal(), $this->limitstart, $this->limit);
        }

        return $this->_pagination;
    }
    public function getTotal()
    {
        $user = &JFactory::getUser();
        if ($this->t_single) {
            $query = 'SELECT COUNT(DISTINCT(p.id)) FROM #__bl_players as p LEFT JOIN #__bl_season_players as sp ON sp.player_id = p.id'
                    .' WHERE sp.season_id = '.$this->season_id.' OR p.created_by ='.$user->id
                    .'  ORDER BY p.first_name, p.last_name';
        } else {
            $query = 'SELECT COUNT(DISTINCT(p.id))'
                    .' FROM #__bl_players as p LEFT JOIN #__bl_players_team as pt ON pt.player_id = p.id LEFT JOIN #__bl_season_teams as st ON pt.team_id = st.team_id'
                    .' WHERE  (st.season_id = '.$this->season_id.' '.($this->season_id ? ' AND pt.season_id='.$this->season_id : '').') OR p.created_by ='.$user->id;
        }

        $this->db->setQuery($query);

        $this->_total = $this->db->loadResult();

        return $this->_total;
    }
}
