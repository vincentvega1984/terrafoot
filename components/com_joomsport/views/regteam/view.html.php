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
/**
 * HTML View class for the Registration component.
 *
 * @since 1.0
 */
class joomsportViewregteam extends JViewLegacy
{
    public $_model = null;
    public function __construct(&$model)
    {
        $this->_model = $model;
        parent::__construct();
    }
    public function display($tpl = null)
    {
        $this->_model->getData();
        $lists = $this->_model->_lists;
        $lists['enmd'] = $this->_model->_enmd;
        $p_title = $this->_model->p_title;
        $params = $this->_model->_params;
        $editor = JFactory::getEditor();
        $this->assignRef('params',        $params);

        $this->assignRef('ptitle', $p_title);
        $this->assignRef('lists', $lists);
        $this->assignRef('editor', $editor);
        parent::display($tpl);
        //require_once(dirname(__FILE__).'/tmpl/default'.$tpl.'.php');
    }
}
