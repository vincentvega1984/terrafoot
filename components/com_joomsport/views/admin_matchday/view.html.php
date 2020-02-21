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
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class joomsportViewadmin_matchday extends JViewLegacy
{
    public $_model = null;
    public function __construct(&$model)
    {
        $this->_model = $model;
    }
    public function display($tpl = null)
    {
        $this->_model->getData();
        $lists = $this->_model->_lists;

        $params = $this->_model->_params;
        $row = $this->_model->_data;
        $page = $this->_model->_pagination;
        $s_id = $this->_model->season_id;

        $this->assignRef('params',        $params);
        $this->assignRef('rows',        $row);
        $this->assignRef('page',        $page);
        $this->assignRef('s_id',        $s_id);
        $this->assignRef('lists', $lists);

        require_once dirname(__FILE__).'/tmpl/default'.$tpl.'.php';
    }
}
