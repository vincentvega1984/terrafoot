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
jimport('joomla.application.component.controller');
require_once dirname(__FILE__).'/../includes/func.php';
$mainframe = JFactory::getApplication();
$db = JFactory::getDBO();
$user = JFactory::getUser();

if (isset($_GET['tmpl']) && $_GET['tmpl'] == 'component') {
} else {
    $doc = JFactory::getDocument();

    JHtml::_('behavior.framework', true);

    $doc->addCustomTag('<link rel="stylesheet" type="text/css"  href="components/com_joomsport/sportleague/assets/css/select2.min.css" />');

    $doc->addCustomTag('<script type="text/javascript" src="components/com_joomsport/js/joomsport.js"></script>');

    $doc->addCustomTag('<script type="text/javascript" src="components/com_joomsport/sportleague/assets/js/select2.min.js"></script>');

    $doc->addCustomTag('<script type="text/javascript" src="components/com_joomsport/sportleague/assets/js/joomsport.js"></script>');
}
?>
<?php

$sid = JRequest::getVar('sid', 0, 'request', 'int');
$tid = JRequest::getVar('tid', 0, 'request', 'int');
$task = JRequest::getVar('task', null, 'default', 'cmd');

    if ($user->get('guest') && $task != 'add_comment' && $task != 'del_comment') {
        $return_url = $_SERVER['REQUEST_URI'];
        $return_url = base64_encode($return_url);

        $uopt = 'com_users';

        $return = 'index.php?option='.$uopt.'&view=login&return='.$return_url;

            // Redirect to a login form
            $mainframe->redirect($return, JText::_('BLMESS_NOT_LOGIN'));
    }

$Itemid = JRequest::getInt('Itemid');

class JoomsportControllerUsers extends JControllerLegacy
{
    protected $js_prefix = '';
    protected $mainframe = null;
    protected $option = 'com_joomsport';

    public function __construct()
    {
        parent::__construct();
        $this->mainframe = JFactory::getApplication();
        $this->js_SetPrefix();
        $this->js_GetDBTables();
        $this->session = JFactory::getSession();
    }
    private function js_SetPrefix()
    {
        $this->js_prefix = '';
        $db = JFactory::getDBO();
        $query = "SELECT name FROM #__bl_addons WHERE published='1'";
        $db->setQuery($query);
        $addon = $db->loadResult();
        if ($addon) {
            $this->js_prefix = $addon;
        }
    }
    private function js_GetDBTables()
    {
        $path = JPATH_SITE.'/components/com_joomsport/tables/';
        if ($this->js_prefix) {
            if (is_file($path.$this->js_prefix.'.php')) {
                require $path.$this->js_prefix.'.php';
            } else {
                require $path.'default.php';
            }
        } else {
            require $path.'default.php';
        }
    }
    private function js_Model($name)
    {
        $path = dirname(__FILE__).'/../models/';
        if ($this->js_prefix) {
            if (is_file($path.$this->js_prefix.'/'.$name.'.php')) {
                require $path.$this->js_prefix.'/'.$name.'.php';
            } else {
                require $path.'default/'.$name.'.php';
            }
        } else {
            require $path.'default/'.$name.'.php';
        }
    }
    private function js_Layout($task)
    {
        $path = dirname(__FILE__).'/../views/'.$task;

        require $path.'/view.html.php';
    }
//update	
    public function set_sess($msg, $typeMess)
    {
        $this->session->set('errMess', $msg);
        $this->session->set('typeMess', $typeMess);
    }
    public function display($cachable = false, $urlparams = false)
    {
        $view = JRequest::getCmd('view');
        $task = JRequest::getCmd('task');
        if (!$view) {
            //if($task){
                //$view = $task;
            //}else{
                $view = 'edit_team';
            //}	
        }

        $vName = JRequest::getCmd('view', 'edit_team');

        if ($vName == 'moderedit_umatchday' || $vName == 'edit_matchday') {
            $vName = 'edit_matchday';
            $this->js_Model($vName);
            $classname = $vName.'JSModel';
            $model = new $classname(3);
        } elseif ($vName == 'moderedit_umatch' || $vName == 'edit_match') {
            $vName = 'edit_match';
            $this->js_Model($vName);
            $classname = $vName.'JSModel';
            $model = new $classname(3);
        } else {
            $this->js_Model($vName);
            $classname = $vName.'JSModel';
            $unviews = array('admin_player', 'edit_team', 'edit_matchday', 'edit_match', 'adplayer_edit');
            if (in_array($vName, $unviews)) {
                $model = new $classname(2);
            } else {
                $model = new $classname();
            }
        }

        $this->js_Layout($vName);
        $classname_l = 'JoomsportView'.$vName;

        $layout = new $classname_l($model);

        $tpl = null;

        $this->mobile();

        $layout->display($tpl);

        return $this;
    }
    public function matchday_save()
    {
        $vName = 'edit_matchday';
        $this->js_Model($vName);
        $classname = $vName.'JSModel';
        $model = new $classname(3);
        $message = $model->AdmMDSave();

        $msg = JText::_('BLFA_MSG_ADDSCHED');

        $Itemid = JRequest::getInt('Itemid');

        $link = 'index.php?option=com_joomsport&view=moderedit_umatchday&mid='.$model->mid.'&sid='.$model->season_id.'&Itemid='.$Itemid;

        $this->setRedirect($link);
    }
    public function match_save()
    {
        $vName = 'edit_match';
        $this->js_Model($vName);
        $classname = $vName.'JSModel';
        $model = new $classname(3);
        $model->saveAdmmatch();

        $s_id = JRequest::getVar('sid', 0, '', 'int');

        $Itemid = JRequest::getInt('Itemid');
        $isapply = JRequest::getVar('isapply', 0, '', 'int');
        if (!$isapply) {
            $this->setRedirect('index.php?option=com_joomsport&view=moderedit_umatchday&mid='.$model->m_id.'&sid='.$s_id.'&Itemid='.$Itemid);
        } else {
            $this->setRedirect('index.php?option=com_joomsport&view=moderedit_umatch&cid[]='.$model->id.'&Itemid='.$Itemid);
        }
    }
    public function regplayer()
    {
        JRequest::setVar('view', 'regplayer');
        $this->display();
    }
    public function playerreg_save()
    {
        $Itemid = JRequest::getInt('Itemid');

        $vName = 'regplayer';
        $this->js_Model($vName);
        $classname = $vName.'JSModel';
        $model = new $classname();
        $model->SaveRegPlayer();

        $link = 'index.php?option=com_joomsport&task=regplayer&Itemid='.$Itemid;

        if ($return = JRequest::getVar('return', '', 'method', 'base64')) {
            $return = base64_decode($return);
            if (!JURI::isInternal($return)) {
                $return = '';
            }
        }

        $message = $model->usrnew ? JText::_('BLMESS_UPDSUCC') : JText::_('BLFA_REGSUCC');
        $typeMess = 1;
        $this->set_sess($message, $typeMess);
        $this->setRedirect($return ? $return : $link, $message);
    }

    public function regteam()
    {
        JRequest::setVar('view', 'regteam');
        $this->display();
    }

    public function teamreg_save()
    {
        $Itemid = JRequest::getInt('Itemid');
        $vName = 'regteam';
        $this->js_Model($vName);
        $classname = $vName.'JSModel';
        $model = new $classname();
        $model->regTeamSave();

        $link = 'index.php?option=com_joomsport&view=seasonlist&oreg=1&Itemid='.$Itemid;
        //$link = "index.php/tour?oreg=1&Itemid=".$Itemid;
        $msg = JText::_('BLFA_NEWTEAMMSG');
        $typeMess = 1;
        $this->set_sess($msg, $typeMess);
        //
        $this->setRedirect($link, $msg);
    }

    public function add_comment()
    {
        require_once 'components/com_joomsport/sportleague/sportleague.php';
        $match_id = classJsportRequest::get('mid', 'request', 'int');
        require_once JS_PATH_MODELS.'model-jsport-comments.php';
        $commentObj = new modelJsportComments($match_id);
        echo $commentObj->add_comment();
        exit();
    }
    public function del_comment()
    {
        $c_id = JRequest::getVar('cid', 0, 'get', 'int');

        $user = JFactory::getUser();
        $dend = false;
        $db = JFactory::getDBO();

        $query = 'SELECT group_id FROM #__user_usergroup_map WHERE user_id='.$user->id;
        $db->setQuery($query);
        if ($db->loadresult() == 8) {
            $dend = true;
        }
        $query = 'SELECT user_id FROM  `#__bl_comments` WHERE `id` = '.$c_id;
        $db->setQuery($query);
        if ($db->loadResult() == $user->id) {
            $dend = true;
        }

        $query = "SELECT s_id FROM #__bl_matchday as md, #__bl_match as m,#__bl_comments as c  WHERE c.match_id = m.id AND md.id=m.m_id AND c.id = '".$c_id."'";
        $db->setQuery($query);
        $sid = $db->loadResult();
        if ($sid) {
            $query = 'SELECT COUNT(*) FROM #__users as u, #__bl_feadmins as f WHERE f.user_id = u.id AND f.season_id='.$sid.' AND u.id = '.intval($user->id);
            $db->setQuery($query);
            if ($db->loadResult()) {
                $dend = true;
            }
        }

        if ($user->get('guest') || !$dend) {
            echo 'Denide';

            return false;
            //return;
        }
        $query = 'DELETE FROM  #__bl_comments WHERE id = '.$c_id;
        $db->setQuery($query);
        $db->query();
        exit(); // Clean output in a dirty way.
    }

    public function join_season()
    {
        JRequest::setVar('view', 'join_season');
        $this->display();
    }
    public function joinme()
    {
        $vName = 'join_season';
        $this->js_Model($vName);
        $classname = $vName.'JSModel';
        $model = new $classname();
        $message = $model->joinSave();
        $Itemid = JRequest::getInt('Itemid');

        $this->set_sess($message[0], $message[1]);
        $this->setRedirect('index.php?option=com_joomsport&view=table&sid='.$model->s_id.'&Itemid='.$Itemid, $message[0]);
    }
    public function joinmePaypl()
    {
        $sid = JRequest::getVar('sid', 0, 'get', 'int');
        $payment_status = JRequest::getVar('payment_status', null, 'post', 'cmd');
        $usr_j = JRequest::getVar('usr_j', 0, 'get', 'int');
            //print_r($usr_j);echo "<hr>";

                $is_team = JRequest::getVar('is_team', 0, 'get', 'int');
                //$reg_team = JRequest::getVar('reg_team',0,'','int');99E77406FV516612D

            //print_r($reg_team);echo "--<hr>";

            $txn_id = JRequest::getVar('txn_id', null, 'post', 'varchar');
        $mc_gross = JRequest::getVar('mc_gross', null, 'post', 'cmd');
        $payment_date = JRequest::getVar('payment_date', null, 'post', 'string ');
        $order_date = date('Y-m-d H:i:s', strtotime($payment_date));
            /*
             * $_POST['txn_id'] //���������� ����� ����������
             * $_POST['mc_gross'] //������ �������
             * $_POST['payment_date'] //���� �������
             * $sid //����� ������
             * $usr_j // ���� ������������ ($reg_team)
             *
             *
             *
             * */
                $vName = 'join_season';
        $this->js_Model($vName);
        $classname = $vName.'JSModel';
        $model = new $classname();
        $message = $model->joinSavePaypl($sid, $is_team, $payment_status, $usr_j, $txn_id, $mc_gross, $order_date);
        $Itemid = JRequest::getInt('Itemid');

        $this->set_sess($message[0], $message[1]);
        $this->setRedirect('index.php?option=com_joomsport&view=table&sid='.$model->s_id.'&Itemid='.$Itemid, $message[0]);
    }
        //inviting confirm
    public function confirm_invitings()
    {
        $vName = 'inviting';
        $this->js_Model($vName);
        $classname = $vName.'JSModel';
        $model = new $classname();
        $messaga = $model->getData();
        $Itemid = JRequest::getInt('Itemid');
        $this->set_sess($messaga[0], $messaga[1]);
        $this->setRedirect('index.php?option=com_joomsport&task=regplayer&Itemid='.$Itemid, $messaga[0]);
    }
    public function reject_invitings()
    {
        $vName = 'inviting';
        $this->js_Model($vName);
        $classname = $vName.'JSModel';
        $model = new $classname();
        $messaga = $model->rejectInv();
        $Itemid = JRequest::getInt('Itemid');
        $this->set_sess($messaga[0], $messaga[1]);
        $this->setRedirect('index.php?option=com_joomsport&task=regplayer&Itemid='.$Itemid, $messaga[0]);
    }
    public function unreg_inviting()
    {
        $vName = 'inviting';
        $this->js_Model($vName);
        $classname = $vName.'JSModel';
        $model = new $classname();
        $messaga = $model->unregInvite();
        $Itemid = JRequest::getInt('Itemid');
        $this->set_sess($messaga[0], $messaga[1]);
        $this->setRedirect('index.php?option=com_joomsport&task=regplayer&Itemid='.$Itemid, $messaga[0]);
    }
    public function unreg_inviting_reject()
    {
        $vName = 'inviting';
        $this->js_Model($vName);
        $classname = $vName.'JSModel';
        $model = new $classname();
        $messaga = $model->rejectInv();
        $Itemid = JRequest::getInt('Itemid');
        $this->set_sess($messaga[0], $messaga[1]);
        $this->setRedirect('index.php?option=com_joomsport&task=regplayer&Itemid='.$Itemid, $messaga[0]);
    }

    public function match_inviting()
    {
        $vName = 'inviting';
        $this->js_Model($vName);
        $classname = $vName.'JSModel';
        $model = new $classname();
        $messaga = $model->matchInvite();
        $Itemid = JRequest::getInt('Itemid');
        $mid = JRequest::getVar('mid', 0, '', 'int');
        $this->set_sess($messaga[0], $messaga[1]);
        $this->setRedirect('index.php?option=com_joomsport&view=match&id='.$mid.'Itemid='.$Itemid, $messaga[0]);
    }

    public function jointeam()
    {
        $vName = 'inviting';
        $this->js_Model($vName);
        $classname = $vName.'JSModel';
        $model = new $classname();
        $messaga = $model->JoinTeam();
        $Itemid = JRequest::getInt('Itemid');
        $team_id = JRequest::getVar('tid', 0, '', 'int');
        $s_id = JRequest::getVar('sid', 0, '', 'int');
        $this->set_sess($messaga[0], $messaga[1]);
        $this->setRedirect('index.php?option=com_joomsport&task=team&tid='.$team_id.'&sid='.$s_id.'&Itemid='.$Itemid, $messaga[0]);
    }

    public function mobile()
    {
        $doc = JFactory::getDocument();
        $doc->addCustomTag('<link rel="stylesheet" type="text/css"  href="components/com_joomsport/sportleague/assets/css/btstrp.css" />');
        $doc->addCustomTag('<link rel="stylesheet" type="text/css"  href="components/com_joomsport/sportleague/assets/css/joomsport.css" />');
    }
}
?>