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
defined('_JEXEC') or die('Restricted access');

abstract class classJsportDatabase
{
    abstract public function select($query, $vars);
    abstract public function insert($query, $vars);
    abstract public function update($query, $vars);
    abstract public function delete($query, $vars);
    abstract public function insertedId();
    abstract public function selectObject($query, $vars);
    abstract public function selectValue($query, $vars);
    abstract public function selectColumn($query, $vars);
}
