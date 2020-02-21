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
require_once JS_PATH_CLASSES.'class-jsport-database.php';

class classJsportDatabaseBase extends classJsportDatabase
{
    public $db = null;

    public function __construct()
    {
        $this->db = $db = JFactory::getDbo();
    }
    public function select($query, $vars = array())
    {
        $this->db->setQuery($query, $vars);

        return $this->db->loadObjectList();
    }
    public function insert($query, $vars = array())
    {
        $this->db->setQuery($query, $vars);

        return $this->db->query();
    }
    public function update($query, $vars = array())
    {
        $this->db->setQuery($query, $vars);

        return $this->db->query();
    }
    public function delete($query, $vars = array())
    {
        $this->db->setQuery($query, $vars);

        return $this->db->query();
    }
    public function insertedId()
    {
        return $this->db->insertid();
    }
    public function selectObject($query, $vars = array())
    {
        $this->db->setQuery($query, $vars);

        return $this->db->loadObject();
    }
    public function selectValue($query, $vars = array())
    {
        $this->db->setQuery($query, $vars);

        return $this->db->loadResult();
    }
    public function selectColumn($query, $vars = array())
    {
        $this->db->setQuery($query, $vars);

        return $this->db->loadColumn();
    }
    public function selectArray($query, $vars = array())
    {
        $this->db->setQuery($query, $vars);

        return $this->db->loadAssoc();
    }
    public function selectKeyPair($query, $vars = array())
    {
        $this->db->setQuery($query);
        $result = $this->db->loadAssocList();

        $return = array();
        foreach ($result as $res) {
            $return[$res['name']] = $res['value'];
        }

        return $return;
    }

    private function query($query, $args = array())
    {
        $sth = $this->db->prepare($query);
        if (!is_array($args)) {
            $args = explode(',', $args);
        }
        $sth->execute($args);

        return $sth;
    }
}
