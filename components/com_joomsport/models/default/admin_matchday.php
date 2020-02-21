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

class admin_matchdayJSModel extends JSPRO_Models
{
    public $_data = null;
    public $_lists = null;
    public $_total = null;

    public $_pagination = null;
    public $limit = null;
    public $limitstart = null;
    public $season_id = null;

    public function __construct()
    {
        parent::__construct();
        $mainframe = JFactory::getApplication();

        // Get the pagination request variables
        $this->season_id = $mainframe->getUserStateFromRequest('com_joomsport.sid', 'sid', 0, 'int');
        $this->limit = $mainframe->getUserStateFromRequest('com_joomsport.md_jslimit', 'jslimit', 20, 'int');
        $this->limitstart = JRequest::getVar('page', 1, '', 'int');
        $this->limitstart = intval($this->limitstart) > 1 ? $this->limitstart : 1;
    }

    public function getData()
    {
        $this->_params = $this->JS_PageTitle(JText::_('BLFA_MDAY_EDIT'));
        $tourn = $this->getTournOpt($this->season_id);
        $this->_lists['t_single'] = $tourn->t_single;
        $this->_lists['tournname'] = $tourn->name;
        $this->getPagination();

        $query = 'SELECT m.*, t.name as tourn,s.s_name'
                .' FROM #__bl_matchday as m , #__bl_tournament as t LEFT JOIN #__bl_seasons as s ON s.t_id = t.id'
                .' WHERE m.s_id = s.s_id '.($this->season_id ? ' AND s.s_id='.$this->season_id : '')
                .'  ORDER BY s.ordering,m.ordering';
        $this->db->setQuery($query, ($this->limitstart - 1) * $this->limit, $this->limit);
        $rows = $this->db->loadObjectList();
        $this->_data = $rows;
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }

        $this->_lists['teams_season'] = $this->teamsToModer();
        $this->_lists['panel'] = $this->getePanel($this->_lists['teams_season'], 0, null, 0);

        /////type
        $type_tourn = array();
        $type_tourn[] = JHTML::_('select.option',  0, JText::_('BLFA_GROUP'), 'id', 'name');
        $type_tourn[] = JHTML::_('select.option',  1, JText::_('BLFA_KNOCK'), 'id', 'name');
        $type_tourn[] = JHTML::_('select.option',  2, JText::_('BLFA_DOUBLE'), 'id', 'name');
        $this->_lists['t_type'] = JHTML::_('select.genericlist',   $type_tourn, 't_type', 'class="selectpicker" size="1"', 'id', 'name');
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
        $query = 'SELECT COUNT(*) FROM #__bl_matchday as m , #__bl_tournament as t LEFT JOIN #__bl_seasons as s ON s.t_id = t.id'
                .' WHERE m.s_id = s.s_id '.($this->season_id ? ' AND s.s_id='.$this->season_id : '')
                .'  ORDER BY m.m_name';

        $this->db->setQuery($query);

        $this->_total = $this->db->loadResult();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }

        return $this->_total;
    }
}
