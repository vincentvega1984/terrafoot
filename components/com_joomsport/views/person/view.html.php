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
class joomsportViewperson extends JViewLegacy
{
    public $slObject = null;
    public function display($tpl = null)
    {
        $this->slObject = $this->get('Object');
        parent::display($tpl);
    }
}
