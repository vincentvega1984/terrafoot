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
defined('_JEXEC') or die;

class JS_Utils
{
    protected static $instance;
    protected static $customFields;
    protected static $isFieldsCacheActual = false;

    protected $db;

    public function __construct(JDatabase $db)
    {
        $this->db = $db;
    }

    public static function instance(JDatabase $db)
    {
        if (null === self::$instance) {
            self::$instance = new self($db);
        }

        return self::$instance;
    }

    public function getCustomField($field, $data = array())
    {
        $param = array(
            'title' => $field,
            'enabled' => true,
            'required' => false,
            'input_name' => 'cf_'.$field,
            'value' => '',
        );

        $customFields = self::getCustomFields();
        $config = isset($customFields[$field])
        ? $customFields[$field]
        : null;

        if (empty($config)) {
            return $param;
        }
        $param['enabled'] = $config['enabled'];
        $param['required'] = $config['required'];
        $param['title'] = JText::_($config['title']);

        switch ($field) {
            case 'team_city':
                if (!empty($data['team_id'])) {
                    $row = new JTableTeams($this->db);
                    $row->load((int) $data['team_id']);
                    $param['value'] = $row->t_city;
                }
                break;
            default:
                $param['value'] = '';
        }

        return $param;
    }

    public static function getCustomFields()
    {
        if (is_null(self::$customFields) || !self::$isFieldsCacheActual) {
            $db = JFactory::getDBO();
            $db->setQuery("SELECT cfg_value FROM #__bl_config WHERE cfg_name = 'custom_fields'");
            $result = $db->loadResult();
            if (empty($result)) {
                self::$customFields = array();
            } else {
                self::$customFields = unserialize($result);
            }
            self::$isFieldsCacheActual = true;
        }

        return self::$customFields;
    }

    public static function invalidateFieldsCache()
    {
        self::$isFieldsCacheActual = false;
    }

    public static function isFieldsCacheActual()
    {
        return self::$isFieldsCacheActual;
    }
}
