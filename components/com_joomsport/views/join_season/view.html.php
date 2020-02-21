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

class joomsportViewjoin_season extends JViewLegacy
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

        $lists['t_single'] = $this->_model->t_single;
        $options = $this->_model->options;
        $lists['s_id'] = $this->_model->s_id;
        $user = $this->_model->_user;
        $this->assignRef('params',        $params);
        $this->assignRef('options',        $options);

        $this->assignRef('lists', $lists);
        $this->assignRef('user', $user);

        require_once dirname(__FILE__).'/tmpl/default'.$tpl.'.php';
    }
}
