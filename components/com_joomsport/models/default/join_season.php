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

class join_seasonJSModel extends JSPRO_Models
{
    public $_lists = null;
    public $s_id = null;
    public $_user = null;
    public $t_single = null;
    public $reg_team = null;
    public $pay = null;
    public $options = null;
    public $is_v = null;

    public function __construct()
    {
        parent::__construct();

        $this->s_id = JRequest::getVar('sid', 0, '', 'int');
        $this->reg_team = JRequest::getVar('reg_team', 0, '', 'int');
        $this->pay = JRequest::getVar('pay', 0, '', 'int');

        $this->_user = JFactory::getUser();
        if (!$this->s_id) {
            JError::raiseError(403, JText::_('Access Forbidden'));

            return;
        }
    }

    public function getData()
    {
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
        //title
        $tourn = $this->getTournOpt($this->s_id);
        $this->t_single = $tourn->t_single;

        $this->_lists['bluid'] = ($this->t_single) ? $this->regBluid($this->_user->id) : 0;

        if ($this->pay) {
            //if(!$this->t_single && $this->reg_team){
               // $this->savePayStatus($this->reg_team,$this->_user->id,$this->s_id);
            //}else{ $this->savePayStatus(0,$this->_user->id,$this->s_id);}
            $this->savePayStatus($this->reg_team, $this->_user->id, $this->s_id);
        }
        $this->_params = $this->JS_PageTitle($tourn->name);
        $season_par = $this->getSParametrs($this->s_id);
        $this->_lists['season_par'] = $season_par;
        $reg_start = mktime(substr($season_par->reg_start, 11, 2), substr($season_par->reg_start, 14, 2), 0, substr($season_par->reg_start, 5, 2), substr($season_par->reg_start, 8, 2), substr($season_par->reg_start, 0, 4));
        $reg_end = mktime(substr($season_par->reg_end, 11, 2), substr($season_par->reg_end, 14, 2), 0, substr($season_par->reg_end, 5, 2), substr($season_par->reg_end, 8, 2), substr($season_par->reg_end, 0, 4));
        $unable_reg = 0;
        if ($this->t_single) {
            $query = 'SELECT COUNT(*) FROM #__bl_players as t , #__bl_season_players as st WHERE st.player_id = t.id AND st.season_id = '.$this->s_id;
        } else {
            $query = 'SELECT COUNT(*) FROM #__bl_teams as t , #__bl_season_teams as st WHERE st.team_id = t.id AND st.season_id = '.$this->s_id;
        }
        $this->db->setQuery($query);
        $part_count = $this->db->loadResult();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
        if ($season_par->s_reg && ($part_count < $season_par->s_participant || $season_par->s_participant == 0) && ($reg_start <= time() && (time() <= $reg_end || $season_par->reg_end == '0000-00-00 00:00:00'))) {
            $unable_reg = 1;
        }
        $this->_lists['part_count'] = $part_count;
        $this->_lists['unable_reg'] = $unable_reg;
        $this->getcaplist();
        $this->_lists['teams_season'] = $this->teamsToModer();
        $this->_lists['panel'] = $this->getePanel($this->_lists['teams_season'], 0, null, 0);

        //payments
        $this->options = new StdClass();

        $this->options->paypal_email = getJS_Config('paypal_acc');
        $this->options->paypalcur_val = getJS_Config('paypalcur_val');
        $this->options->paypalval_val = getJS_Config('paypalval_val');
        $this->options->paypalvalleast_val = getJS_Config('paypalvalleast_val');
        $this->options->paypal_org = getJS_Config('paypal_org');

        $query = "SELECT is_pay FROM #__bl_seasons WHERE published='1' AND s_id = ".$this->s_id;
        $this->db->setQuery($query);
        $this->options->paypal_on = $this->db->loadResult();
        //$this->options->paypal_on = getJS_Config('paypal_on');
    }
    public function getcaplist()
    {
        $query = 'SELECT t.* FROM #__bl_teams as t, #__bl_moders as m'
                .' WHERE m.tid=t.id AND m.uid='.$this->_user->id
                .' ORDER BY t.t_name';
        $this->db->setQuery($query);
        $cap = $this->db->loadObjectList();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
        $this->_lists['no_team'] = 0;
        if (!count($cap) && !$this->t_single) {
            $this->_lists['no_team'] = 1;
        }

        if (!$this->_user->id) {
            $this->message = JText::_('BLMESS_NOT_REG');
            $this->_lists['unable_reg'] = 0;
        }

        $this->_lists['cap'] = JHTML::_('select.genericlist',   $cap, 'reg_team', 'class="selectpicker" size="1"', 'id', 't_name', 0);
    }
    public function joinSave()
    {
        $user = JFactory::getUser();

        if ($user->get('guest')) {
            JError::raiseError(403, JText::_('Access Forbidden'));

            return;
        }

        $is_team = JRequest::getVar('is_team', 0, 'post', 'int');
        $reg_team = JRequest::getVar('reg_team', 0, 'post', 'int');
        $sid = JRequest::getVar('sid', 0, 'post', 'int');
        $unable_reg = 0;
        $message = '';

        $tourn = $this->getTournOpt($sid);
        $t_single = $tourn->t_single;

        $season_par = $this->getSParametrs($sid);

        $reg_start = mktime(substr($season_par->reg_start, 11, 2), substr($season_par->reg_start, 14, 2), 0, substr($season_par->reg_start, 5, 2), substr($season_par->reg_start, 8, 2), substr($season_par->reg_start, 0, 4));
        $reg_end = mktime(substr($season_par->reg_end, 11, 2), substr($season_par->reg_end, 14, 2), 0, substr($season_par->reg_end, 5, 2), substr($season_par->reg_end, 8, 2), substr($season_par->reg_end, 0, 4));

        if ($t_single) {
            $query = 'SELECT COUNT(*) FROM #__bl_players as t , #__bl_season_players as st WHERE st.player_id = t.id AND st.season_id = '.$sid;
        } else {
            $query = 'SELECT COUNT(*) FROM #__bl_teams as t , #__bl_season_teams as st WHERE st.team_id = t.id AND st.season_id = '.$sid;
        }
        $this->db->setQuery($query);
        $part_count = $this->db->loadResult();

        if ($season_par->s_reg && ($part_count < $season_par->s_participant || $season_par->s_participant == 0) && ($reg_start <= time() && (time() <= $reg_end || $season_par->reg_end == '0000-00-00 00:00:00'))) {
            $unable_reg = 1;
        }

        if ($unable_reg && $sid) {
            if ($is_team) { //////this
                $query = "SELECT COUNT(*) FROM  #__bl_season_teams WHERE season_id='".$sid."' AND team_id = '".$reg_team."'";
                $this->db->setQuery($query);
                if (!$this->db->loadResult()) {
                    $query = 'INSERT INTO #__bl_season_teams(season_id,team_id,regtype) VALUES('.$sid.','.$reg_team.",'1')";
                    $this->db->setQuery($query);
                    $this->db->execute();
                    $error = 0;  //exception ??/  does not work with getErrorMsg
                } else {
                    $error = 1;
                }

                //$error = $this->db->getErrorMsg();
                if ($error) {
                    $message[0] = JText::_('BL_ALRREG');
                    $message[1] = 3;
                } else {
                    $message[0] = JText::_('BL_JOIN');
                    $message[1] = 1;
                    
                }
            } else {
                $query = 'SELECT id FROM #__bl_players WHERE usr_id = '.$reg_team;
                $this->db->setQuery($query);
                $bluid = $this->db->loadResult();
            //print_r($bluid);die;
                $error = $this->db->getErrorMsg();
                if ($error) {
                    return JError::raiseError(500, $error);
                }
                if ($bluid) {
                    $query = 'INSERT INTO #__bl_season_players(season_id,player_id,regtype) VALUES('.$sid.','.$bluid.',1)';
                    $this->db->setQuery($query);

                    $message[0] = JText::_('BL_JOIN');
                    $message[1] = 1;
                    try {
                        $result = $this->db->execute();
                    } catch (Exception $e) {
                        $message[0] = JText::_('BL_ALRREG');
                        $message[1] = 3;
                    }
                } else {
                    $message[0] = 'Register in component first';
                    $message[1] = 3;
                }
            }
            require_once JPATH_COMPONENT_SITE.DIRECTORY_SEPARATOR.'sportleague'.DIRECTORY_SEPARATOR.'sportleague.php';
            classJsportPlugins::get('generateTableStanding', array('season_id' => $sid));
            classJsportPlugins::get('generatePlayerList', array('season_id' => $sid));
        } else {
            $message[0] = 'Error';
            $message[1] = 2;
        }

        return $message;
    }

    public function joinSavePaypl($sid, $is_team, $payment_status, $usr_j, $txn_id, $mc_gross, $payment_date)
    {
        $user = JFactory::getUser();

        if ($user->get('guest')) {
            JError::raiseError(403, JText::_('Access Forbidden'));

            return;
        }

        //$is_team = JRequest::getVar('is_team',0,'post','int');
        //$reg_team = JRequest::getVar('reg_team',0,'post','int');
        //$sid = JRequest::getVar('sid',0,'post','int');
        $unable_reg = 0;
        $message = '';

        $tourn = $this->getTournOpt($sid);
        $t_single = $tourn->t_single;

        $query = "SELECT p_rteam_id FROM #__bl_payments WHERE p_user_id = '".$usr_j."' AND p_sid = '".$sid."' AND p_status = 'In process'";
        $this->db->setQuery($query);
        $reg_team = ($t_single) ? $usr_j : $this->db->loadResult();

        $season_par = $this->getSParametrs($sid);

        $reg_start = mktime(substr($season_par->reg_start, 11, 2), substr($season_par->reg_start, 14, 2), 0, substr($season_par->reg_start, 5, 2), substr($season_par->reg_start, 8, 2), substr($season_par->reg_start, 0, 4));
        $reg_end = mktime(substr($season_par->reg_end, 11, 2), substr($season_par->reg_end, 14, 2), 0, substr($season_par->reg_end, 5, 2), substr($season_par->reg_end, 8, 2), substr($season_par->reg_end, 0, 4));

        if ($t_single) {
            $query = 'SELECT COUNT(*) FROM #__bl_players as t , #__bl_season_players as st WHERE st.player_id = t.id AND st.season_id = '.$sid;
        } else {
            $query = 'SELECT COUNT(*) FROM #__bl_teams as t , #__bl_season_teams as st WHERE st.team_id = t.id AND st.season_id = '.$sid;
        }
        $this->db->setQuery($query);
        $part_count = $this->db->loadResult();

        if ($season_par->s_reg && ($part_count < $season_par->s_participant || $season_par->s_participant == 0) && ($reg_start <= time() && (time() <= $reg_end || $season_par->reg_end == '0000-00-00 00:00:00'))) {
            $unable_reg = 1;
        }

        if ($unable_reg && $sid && $payment_status == 'Completed') {
            if ($is_team) { //////this
                $query = "SELECT COUNT(*) FROM  #__bl_season_teams WHERE season_id='".$sid."' AND team_id = '".$reg_team."'";
                $this->db->setQuery($query);
                if (!$this->db->loadResult()) {
                    $query = 'INSERT INTO #__bl_season_teams(season_id,team_id,regtype) VALUES('.$sid.','.$reg_team.",'1')";
                    $this->db->setQuery($query);
                    $this->db->execute();
                    $error = 0;  //exception ??/  does not work with getErrorMsg
                } else {
                    $error = 1;
                }

                //$error = $this->db->getErrorMsg();
                if ($error) {
                    $message[0] = JText::_('BL_ALRREG');
                    $message[1] = 3;
                } else {
                    $message[0] = JText::_('BL_JOIN');
                    $message[1] = 1;
                }
            } else {
                /*$query = "SELECT id FROM #__bl_players WHERE usr_id = ".$reg_team;
                $this->db->setQuery($query);
                $bluid = $this->db->loadResult();
                $error = $this->db->getErrorMsg();
                if ($error)
                {
                    return JError::raiseError(500, $error);
                }*/
                $bluid = $this->regBluid($reg_team);

                if ($bluid) {
                    $query = 'INSERT INTO #__bl_season_players(season_id,player_id,regtype) VALUES('.$sid.','.$bluid.',1)';
                    $this->db->setQuery($query);
                    $this->db->query();
                    $error = $this->db->getErrorMsg();
                    if ($error) {
                        $message[0] = JText::_('BL_ALRREG');
                        $message[1] = 3;
                    } else {
                        $message[0] = JText::_('BL_JOIN');
                        $message[1] = 1;
                    }
                } else {
                    $message[0] = 'Register in component first';
                    $message[1] = 3;
                }
            }
            require_once JPATH_COMPONENT_SITE.DIRECTORY_SEPARATOR.'sportleague'.DIRECTORY_SEPARATOR.'sportleague.php';
                    classJsportPlugins::get('generateTableStanding', array('season_id' => $sid));
                    classJsportPlugins::get('generatePlayerList', array('season_id' => $sid));
            $query = "UPDATE #__bl_payments SET p_txn_id = '".$txn_id."', p_mc_gross = '".$mc_gross."', p_date = '".$payment_date."', p_status = '".$payment_status."'
                        WHERE p_user_id = '".$usr_j."' AND p_sid = '".$sid."' AND p_status = 'In process'";
            $this->db->setQuery($query);
            $this->db->query();
        } else {
            $message[0] = 'Error';
            $message[1] = 2;
        }

        return $message;
    }

    public function savePayStatus($reg_team, $user, $s_id)
    {
        if ($reg_team) {
            $query = 'SELECT COUNT(*) FROM #__bl_season_teams WHERE season_id = '.$s_id.' AND team_id = '.$reg_team;
        } else {
            $query = 'SELECT id FROM #__bl_players WHERE usr_id = '.$user;
            $this->db->setQuery($query);
            $p_id = $this->db->loadResult();

            $query = 'SELECT COUNT(*) FROM #__bl_season_players WHERE player_id = '.$p_id.' AND season_id = '.$s_id;
        }
        $this->db->setQuery($query);
        $this->is_v = $this->db->loadResult();
        if (!$this->is_v) {
            $query = "DELETE FROM #__bl_payments WHERE p_user_id = '".$user."' AND p_status = 'In process'";
            $this->db->setQuery($query);
            $this->db->query();
                //echo $reg_team."---".$user."----".$s_id;die;
                $query = "INSERT INTO #__bl_payments(p_sid,p_user_id,p_rteam_id, p_status) VALUES('".$s_id."','".$user."','".$reg_team."', 'In process')";
            $this->db->setQuery($query);
            $this->db->query();
        } else {
            $app = JFactory::getApplication();

            $this->session = JFactory::getSession();
            $this->session->set('errMess', JText::_('BL_ALRREG'));
            $this->session->set('typeMess', 3);

            $app->redirect('index.php?option=com_joomsport&view=table&sid='.$s_id);
        }
    }

    public function regBluid($reg_team)
    {
        $query = 'SELECT id FROM #__bl_players WHERE usr_id = '.$reg_team;
        $this->db->setQuery($query);
        $bluid = $this->db->loadResult();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }

        return $bluid;
    }
}
