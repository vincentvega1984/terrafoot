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
require_once realpath(dirname(__FILE__).'/../../').'/includes/utils.php';

class regteamJSModel extends JSPRO_Models
{
    public $_lists = null;
    public $_user = null;
    public $_usrjs = null;
    public $_enmd = null;
    public $title = null;
    public $p_title = null;

    public function __construct()
    {
        parent::__construct();
        $this->_user = JFactory::getUser();
        $this->_enmd = 0;
        $this->session = JFactory::getSession();
        $this->title = JFactory::getDocument()->getTitle();
    }

    public function getData()
    {
        $this->getTeamReg();
        //title
        $this->p_title = JText::_('BLFA_NTEAM');
        //$this->_params = $this->JS_PageTitle(JText::_('BLFA_NTEAM'));
        $this->_params = $this->JS_PageTitle($this->title ? $this->title : $this->p_title);
        $team_reg = $this->getJS_Config('team_reg');

        if (!$team_reg) {
            //echo JText::_('BLFA_OPTDISAB');
                                JError::raiseError(403, JText::_('BLFA_OPTDISAB'));

            return;
                //exit();
        }
        //return
        if ($return = JRequest::getVar('return', '', 'method', 'base64')) {
            $return = $return;
            if (!JURI::isInternal($return)) {
                $return = '';
            }
        }
        if ($this->_user->get('guest')) {
            $return_url = $_SERVER['REQUEST_URI'];
            $return_url = base64_encode($return_url);

            if (getVer() >= '1.6') {
                $uopt = 'com_users';
            } else {
                $uopt = 'com_user';
            }
            $return = 'index.php?option='.$uopt.'&view=login&return='.$return_url;

            // Redirect to a login form
            $this->session->set('errMess', JText::_('BLMESS_NOT_LOGIN'));
            $this->session->set('typeMess', 3);
            $this->mainframe->redirect($return);
        }
        $this->_lists['return'] = $return;
        $this->getJSreg();

        $this->_lists['teams_season'] = $this->teamsToModer();
        $this->_lists['panel'] = $this->getePanel($this->_lists['teams_season'], 0, 0, 1);
    }

    public function set_sess($msg, $typeMess)
    {
        $this->session->set('errMess', $msg);
        $this->session->set('typeMess', $typeMess);
    }

    public function getTeamReg()
    {
        $query = 'Select t.t_name FROM #__bl_teams as t GROUP BY t.t_name';
        $this->db->setQuery($query);
        $team_reg = $this->db->loadColumn();
        $this->_lists['team_reg'] = $team_reg;
    }

    public function getJSreg()
    {
        $Itemid = JRequest::getInt('Itemid');
        $query = 'Select * FROM #__bl_players WHERE usr_id='.$this->_user->id;
        $this->db->setQuery($query);
        $usr = $this->db->loadObject();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
        if ($this->getJS_Config('player_team_reg') == '1' && (!$usr || $usr->registered != '1')) {
            $return_url = $_SERVER['REQUEST_URI'];
            $return_url = base64_encode($return_url);
            $return = 'index.php?option=com_joomsport&task=regplayer&Itemid='.$Itemid.'&return='.$return_url;
            $msg = JText::_('BLFA_PLEASE_FILL_PROF');
            $typeMess = 3;
            $this->set_sess(JText::_('BLFA_PLEASE_FILL_PROF'), $typeMess);
            $this->mainframe->redirect($return);
        }

        $query = 'SELECT ef.*,ev.fvalue,ev.fvalue_text'
                .' FROM #__bl_extra_filds as ef LEFT JOIN #__bl_extra_values as ev ON ef.id=ev.f_id AND ev.uid = '.(isset($usr->id) ? $usr->id : 0)
                ."  WHERE reg_exist='1' AND type='1' ORDER BY ef.ordering";
        $this->db->setQuery($query);
        $adf = $this->db->loadObjectList();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
        $this->_lists['canmore'] = false;
        $query = 'SELECT COUNT(*) FROM #__bl_teams WHERE created_by='.$this->_user->id;
        $this->db->setQuery($query);
        $curcap = $this->db->loadResult();

        $query = 'SELECT COUNT(*) FROM #__bl_moders as m JOIN #__bl_teams as t ON t.id=m.tid WHERE m.uid='.$this->_user->id;
        $this->db->setQuery($query);
        $teamcap = $this->db->loadResult();

        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
        $teams_per_account = $this->getJS_Config('teams_per_account');

        if ($curcap < $teams_per_account) {
            $this->_lists['canmore'] = true;
        }

        if ($teamcap < $teams_per_account) {
            $this->_lists['canmore'] = true;
        }

        $mj = 0;
        if (isset($adf)) {
            foreach ($adf as $extr) {
                if ($extr->field_type == '3') {
                    $query = 'SELECT * FROM #__bl_extra_select WHERE fid='.$extr->id;
                    $this->db->setQuery($query);
                    $selvals = $this->db->loadObjectList();
                    if (count($selvals)) {
                        $adf[$mj]->selvals = JHTML::_('select.genericlist',   $selvals, 'extraf['.$extr->id.']', 'class="selectpicker inputbox'.($extr->reg_require ? ' required' : '').'" size="1"', 'id', 'sel_value', $extr->fvalue);
                    }
                }
                if ($extr->field_type == '1') {
                    $adf[$mj]->selvals = JHTML::_('select.booleanlist',  'extraf['.$extr->id.']', 'class="inputbox"', $extr->fvalue);
                }
                ++$mj;
            }
        }

        $this->_lists['cap'] = $this->_user->username;

        $this->_lists['adf'] = $adf;
    }
    public function regTeamSave()
    {
        $post = JRequest::get('post');
        unset($post['id']);
        $user = &JFactory::getUser();
        $row = new JTableTeams($this->db);
        $row->created_by = $user->id;

        $canmore = false;
        $query = 'SELECT COUNT(*) FROM #__bl_teams WHERE created_by='.$user->id;
        $this->db->setQuery($query);
        $curcap = $this->db->loadResult();

        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
        $teams_per_account = $this->getJS_Config('teams_per_account');

        $query = 'SELECT COUNT(*) FROM #__bl_moders WHERE uid='.$user->id;
        $this->db->setQuery($query);
        $teamcap = $this->db->loadResult();

        if ($curcap < $teams_per_account) {
            $canmore = true;
        }
        if ($teamcap < $teams_per_account) {
            $canmore = true;
        }
        if ($user->get('guest') && !$canmore) {
            JError::raiseError(403, JText::_('Access Forbidden'));

            return;
        }
        //$post['captain_id'] = $user->id;
        if (!$row->bind($post)) {
            JError::raiseError(500, $row->getError());
        }

        // Validate and store custom field
        $customFields = JS_Utils::getCustomFields();
        if (!empty($customFields['team_city']['enabled'])) {
            if (!empty($post['cf_team_city'])) {
                $row->t_city = addslashes($post['cf_team_city']);
            } elseif (!empty($customFields['team_city']['required'])) {
                // FIXME: Set a correct error message and redraw form with errors.
                $this->set_sess(JText::_('BLBE_REQUIRED'), 'error');

                return;
            }
        }

        if (!$row->check()) {
            JError::raiseError(500, $row->getError());
        }
        // if new item order last in appropriate group
        if (!$row->store()) {
            JError::raiseError(500, $row->getError());
        }
        $row->checkin();
        if ($row->id) {
            //-------extra fields-----------//
            if (isset($_POST['extraf']) && count($_POST['extraf'])) {
                foreach ($_POST['extraf'] as $p => $dummy) {
                    $query = 'DELETE FROM #__bl_extra_values WHERE f_id = '.$_POST['extra_id'][$p].' AND uid = '.$row->id;
                    $this->db->setQuery($query);
                    $this->db->query();
                    $error = $this->db->getErrorMsg();
                    if ($error) {
                        return JError::raiseError(500, $error);
                    }
                    if ($_POST['extra_ftype'][$p] == '2') {
                        $query = 'INSERT INTO #__bl_extra_values(f_id,uid,fvalue_text) VALUES('.$_POST['extra_id'][$p].','.$row->id.",'".addslashes($_POST['extraf'][$p])."')";
                    } else {
                        $query = 'INSERT INTO #__bl_extra_values(f_id,uid,fvalue) VALUES('.$_POST['extra_id'][$p].','.$row->id.",'".$_POST['extraf'][$p]."')";
                    }
                    $this->db->setQuery($query);
                    $this->db->query();
                    $error = $this->db->getErrorMsg();
                    if ($error) {
                        return JError::raiseError(500, $error);
                    }
                }
            }
            $query = "INSERT INTO #__bl_moders(uid,tid) VALUES({$user->id},{$row->id})";
            $this->db->setQuery($query);
            $this->db->query();
            $error = $this->db->getErrorMsg();
            if ($error) {
                return JError::raiseError(500, $error);
            }
        }
    }

    public function getCustomField($field, $data = array())
    {
        return JS_Utils::instance($this->db)->getCustomField($field, $data);
    }
}
