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

class invitingJSModel extends JSPRO_Models
{
    public $_user = null;
    public function __construct()
    {
        parent::__construct();
        $this->_user = JFactory::getUser();
        $this->session = JFactory::getSession();
    }

    public function getData()
    {
        $messaga[0] = JText::_('BLFA_MESS_STWRONG');
        $messaga[1] = 2;
        //title
        $this->_params = $this->JS_PageTitle(JText::_('BLFA_EDITFIPROF'));
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
            $this->mainframe->redirect($return, JText::_('BLMESS_NOT_LOGIN'));
        }
        $Itemid = JRequest::getInt('Itemid');
        $query = 'Select * FROM #__bl_players WHERE usr_id='.$this->_user->id;
        $this->db->setQuery($query);
        $usr = $this->db->loadObject();

        if (!$usr || $usr->registered != '1') {
            $return_url = $_SERVER['REQUEST_URI'];
            $return_url = base64_encode($return_url);
            $return = 'index.php?option=com_joomsport&task=regplayer&Itemid='.$Itemid.'&return='.$return_url;

            $this->session->set('errMess', JText::_('BLMESS_FILL_PROFILE'));
            $this->session->set('typeMess', 3);

            $this->mainframe->redirect($return);
        }

        $key = JRequest::getVar('key', '', '', 'string');
        if ($key != '') {
            $query = "SELECT COUNT(*) FROM  #__bl_players_team WHERE invitekey='".$key."' AND player_id = ".$usr->id;
            $this->db->setQuery($query);
            if ($this->db->loadResult()) {
                $query = "UPDATE #__bl_players_team SET invitekey='',confirmed='0' WHERE invitekey='".$key."' AND player_id = ".$usr->id;
                $this->db->setQuery($query);
                $this->db->query();
                $messaga[0] = JText::_('BLFA_YOUINTEAM');
                $messaga[1] = 1;
            }
        }

        return $messaga;
    }

    public function rejectInv()
    {
        $messaga[0] = JText::_('BLFA_MESS_STWRONG');
        $messaga[1] = 2;

        $key = JRequest::getVar('key', '', '', 'string');
        if ($key != '') {
            $query = "SELECT COUNT(*) FROM  #__bl_players_team WHERE invitekey='".$key."'";
            $this->db->setQuery($query);
            if ($this->db->loadResult()) {
                $query = "DELETE FROM #__bl_players_team WHERE invitekey='".$key."'";
                $this->db->setQuery($query);
                $this->db->query();
                $messaga[0] = JText::_('BLFA_YOUINREJ');
                $messaga[1] = 2;
            }
        }

        return $messaga;
    }

    public function unregInvite()
    {
        $messaga[0] = JText::_('BLFA_MESS_STWRONG');
        $messaga[1] = 2;
        //title
        $this->_params = $this->JS_PageTitle(JText::_('BLFA_EDITFIPROF'));
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
            $this->mainframe->redirect($return, JText::_('BLMESS_NOT_LOGIN'));
        }
        $Itemid = JRequest::getInt('Itemid');
        $query = 'Select * FROM #__bl_players WHERE usr_id='.$this->_user->id;
        $this->db->setQuery($query);
        $usr = $this->db->loadObject();

        if (!$usr || $usr->registered != '1') {
            $return_url = $_SERVER['REQUEST_URI'];
            $return_url = base64_encode($return_url);
            $return = 'index.php?option=com_joomsport&task=regplayer&Itemid='.$Itemid.'&return='.$return_url;

            $this->session->set('errMess', JText::_('BLMESS_FILL_PROFILE'));
            $this->session->set('typeMess', 3);

            $this->mainframe->redirect($return);
        }

        $key = JRequest::getVar('key', '', '', 'string');
        if ($key != '') {
            $query = "SELECT COUNT(*) FROM  #__bl_players_team WHERE invitekey='".$key."'";
            $this->db->setQuery($query);
            if ($this->db->loadResult()) {
                $query = "UPDATE #__bl_players_team SET invitekey='',confirmed='0',player_id = ".$usr->id." WHERE invitekey='".$key."'";
                $this->db->setQuery($query);
                $this->db->query();
                $messaga[0] = JText::_('BLFA_YOUINTEAM');
                $messaga[1] = 1;
            }
        }

        return $messaga;
    }

    public function JoinTeam()
    {
        $team_id = JRequest::getVar('tid', 0, '', 'int');
        $s_id = JRequest::getVar('sid', 0, '', 'int');

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
            $this->mainframe->redirect($return, JText::_('BLMESS_NOT_LOGIN'));
        }
        $Itemid = JRequest::getInt('Itemid');
        $query = 'Select * FROM #__bl_players WHERE usr_id='.$this->_user->id;
        $this->db->setQuery($query);
        $usr = $this->db->loadObject();

        if (!$usr || $usr->registered != '1') {
            $return_url = JRoute::_('index.php?option=com_joomsport&task=jointeam&sid='.$s_id.'&tid='.$team_id.'&Itemid='.$Itemid, true, -1);
            $return_url = base64_encode($return_url);
            $return = 'index.php?option=com_joomsport&task=regplayer&Itemid='.$Itemid.'&return='.$return_url;

            $this->session->set('errMess', JText::_('BLMESS_FILL_PROFILE'));
            $this->session->set('typeMess', 3);

            $this->mainframe->redirect($return);
        }

        $messaga[0] = JText::_('BLFA_MESS_STWRONG');
        $messaga[1] = 2;
        if ($team_id && $s_id && $this->getJS_Config('esport_join_team')) {
            // check the data
        $query = "SELECT COUNT(*) FROM  #__bl_players_team WHERE team_id='".$team_id."' AND player_id = '".$usr->id."' AND season_id = '".$s_id."'";
            $this->db->setQuery($query);
            if (!$this->db->loadResult()) {
                $query = "INSERT INTO #__bl_players_team(team_id,player_id,season_id,confirmed,player_join) VALUES('".$team_id."','".$usr->id."','".$s_id."','1','1')";
                $this->db->setQuery($query);
                $this->db->query();
            }
    /////
            /*$query = "INSERT INTO #__bl_players_team(team_id,player_id,season_id,confirmed,player_join) VALUES('".$team_id."','".$usr->id."','".$s_id."','1','1')";	
            $this->db->setQuery($query);
            $this->db->query();*/

            $messaga[0] = JText::_('BLFA_WAIT_FOR_APPROVAL');
            $messaga[1] = 3;

            $config = JFactory::getConfig();

            $fromname = $config->get('fromname');
            $mailfrom = $config->get('mailfrom');
            $sitename = $config->get('sitename');

            $query = 'SELECT u.email FROM #__users as u, #__bl_moders as m WHERE m.uid = u.id AND m.tid = '.$team_id;
            $this->db->setQuery($query);
            $mail = $this->db->loadResult();

            if ($mail) {
                $emailSubject = JText::_('BLFA_MAIL_MODERAPRTITLE');

                $emailBody = JText::_('BLFA_MAIL_MODERAPBODY');
                $emailBody = str_replace('{usr}', $usr->first_name.' '.$usr->last_name, $emailBody);

                try {
                    if (!$mailer = JFactory::getMailer()) {
                        throw new ErrorException('Mail server is not configured properly.');
                    }
                    $return = $mailer->sendMail($mailfrom, $fromname, $mail, $emailSubject, $emailBody, 1);
                    // Check for an error.
                    if ($return !== true) {
                        $this->setError(JText::_('ERROR'));

                        return false;
                    }
                } catch (Exception $e) {
                    JLog::add(JText::_('BLFA_JOINTEAM_MAIL_MODER_ERROR'), JLog::WARNING);
                }
            }
        }

        return $messaga;
    }

    public function matchInvite()
    {
        $mid = JRequest::getVar('mid', 0, '', 'int');
        $tid = JRequest::getVar('tid', 0, '', 'int');
        $do = JRequest::getVar('do', 'reject', '', 'string');
        $key = JRequest::getVar('key', '', '', 'string');
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
            $this->mainframe->redirect($return, JText::_('BLMESS_NOT_LOGIN'));
        }

        $Itemid = JRequest::getInt('Itemid');
        $query = 'Select * FROM #__bl_players WHERE usr_id='.$this->_user->id;
        $this->db->setQuery($query);
        $usr = $this->db->loadObject();

        if ($key != md5($usr->id)) {
            return $messaga;
            die();
        }

        if (!$usr || $usr->registered != '1') {
            $return_url = $_SERVER['REQUEST_URI'];
            $return_url = base64_encode($return_url);
            $return = 'index.php?option=com_joomsport&task=regplayer&Itemid='.$Itemid.'&return='.$return_url;

            $this->session->set('errMess', JText::_('BLMESS_FILL_PROFILE'));
            $this->session->set('typeMess', 3);

            $this->mainframe->redirect($return);
        }

        $messaga[0] = JText::_('BLFA_MESS_STWRONG');
        $messaga[1] = 2;

        if ($mid && $this->getJS_Config('esport_invite_match')) {
            if ($do == 'accept') {
                $query = "INSERT INTO #__bl_squard(match_id,team_id,player_id,mainsquard) VALUES({$mid},{$tid},{$usr->id},'1')";
                $query .= " ON DUPLICATE KEY UPDATE accepted='1'";
                $this->db->setQuery($query);
                $this->db->query();
                $messaga[0] = JText::_('BLFA_INVITETOMATCHACCEPT');
                $messaga[1] = 1;
            } else {
                $query = "DELETE FROM #__bl_squard WHERE match_id={$mid} AND team_id = {$tid} AND player_id = {$usr->id}";
                $this->db->setQuery($query);
                $this->db->query();
                $messaga[0] = JText::_('BLFA_INVITETOMATCHREJECT');
                $messaga[1] = 3;
            }
        }

        return $messaga;
    }
}
