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
require_once JS_PATH_OBJECTS.'class-jsport-player.php';

class modelJsportComments
{
    public $match_id = null;
    public $lists = null;
    public $row = null;
    public $season = null;

    public function __construct($match_id)
    {
        $this->match_id = $match_id;

        if (!$this->match_id) {
            die('ERROR! Match ID not DEFINED');
        }
    }
    public function getComments()
    {
        global $jsDatabase;
        $query = "SELECT DISTINCT(c.id),c.*,IF(pl.nick <> '',pl.nick,p.name) as nick, p.id as usrid,"
                ."pl.id as plid,pl.def_img,CONCAT(pl.first_name,' ',pl.last_name) as plname"
                .' FROM '.DB_TBL_COMMENTS.' as c, #__users as p LEFT JOIN '.DB_TBL_PLAYERS.' as pl ON p.id=pl.usr_id'
                .' WHERE c.match_id = '.$this->match_id.' AND c.user_id=p.id'
                .' ORDER BY c.date_time';
        $this->row = $jsDatabase->select($query);

        for ($intA = 0; $intA < count($this->row); ++$intA) {
            $player = $this->getCommentsAuthor($this->row[$intA]->usrid);
            $this->row[$intA]->name = $player['name'];
            $this->row[$intA]->photo = $player['avatar'];
            $this->row[$intA]->date_time = $this->getDate($this->row[$intA]->date_time);
            $this->row[$intA]->comment = str_replace("\n", '<br />', htmlspecialchars($this->row[$intA]->comment));
        }

        return $this->row;
    }

    public function canDelComment($season_id)
    {
        global $jsDatabase;
        $user = JFactory::getUser();
        $canDelete = false;

        $query = "SELECT IF(m.group_id <> 8,'','1') as gid"
                        .' FROM  #__users as p, #__user_usergroup_map as m'
                        .' WHERE m.user_id=p.id AND p.id='.intval($user->id);

        $canDelete = (boolean) $jsDatabase->selectValue($query);
        if (!$canDelete) {
            $query = 'SELECT COUNT(*) '
                    .' FROM #__users as u, #__bl_feadmins as f'
                    ." WHERE f.user_id = u.id AND f.season_id='".$season_id."' AND u.id = '".intval($user->id)."'";

            $canDelete = (boolean) $jsDatabase->selectValue($query);
        }

        return $canDelete;
    }

    private function getCommentsAuthor($user_id)
    {
        global $jsDatabase;
        $player = array();
        $query = 'SELECT pl.id FROM #__users as p'
                .' LEFT JOIN '.DB_TBL_PLAYERS.' as pl ON p.id=pl.usr_id'
                .' WHERE p.id='.intval($user_id);

        $player_id = $jsDatabase->selectValue($query);
        if (!$player_id) {
            $query = 'SELECT p.name FROM #__users as p WHERE p.id='.intval($user_id);
            $player['name'] = $jsDatabase->selectValue($query);
            $player['avatar'] = jsHelperImages::getEmblem('profile.png');
        } else {
            $obj = new classJsportPlayer($player_id, null, false);
            $player['name'] = $obj->getName(false);
            $def_img = $obj->getEmblem(false);
            $player['avatar'] = $def_img;
        }

        return $player;
    }

    private function getDate($date = null)
    {
        jimport('joomla.utilities.date');
        $tz = new DateTimeZone(JFactory::getApplication()->getCfg('offset'));
        if ($date) {
            $jdate = new JDate($date);
        } else {
            $jdate = new JDate(time());
        }

        $jdate->setTimezone($tz);

        return $jdate->format('Y-m-d H:i:s', true, false);
    }

    //only for joomla
    public function add_comment()
    {
        global $jsDatabase;
        $addcomm = classJsportRequest::get('addcomm');
        $addcomm = strip_tags($addcomm);
        $responce_array = array();
        $math_id = classJsportRequest::get('mid', 'request', 'int');
        $user = JFactory::getUser();

        if ($user->get('guest')) {
            $responce_array['error'] = classJsportLanguage::get('BLFA_NOTREGISTRED');

            return json_encode($responce_array);
                //return;
        }
        $query = 'INSERT INTO '.DB_TBL_COMMENTS.' ( `id` , `user_id` , `match_id` , `date_time` , `comment` )'
                .' VALUES(0,'.$user->id.','.$math_id.",'".gmdate('Y-m-d H:i:s')."','".addslashes($addcomm)."')";
        $jsDatabase->insert($query);
        $curid = $jsDatabase->insertedId();

        $player = $this->getCommentsAuthor($user->id);

        jimport('joomla.utilities.date');
        $tz = new DateTimeZone(JFactory::getApplication()->getCfg('offset'));
        $jdate = new JDate(time());

        $jdate->setTimezone($tz);

        $responce_array = array();
        $responce_array['id'] = $curid;
        $responce_array['photo'] = $player['avatar'];
        $responce_array['delimg'] = "<img alt='' src='".JS_LIVE_ASSETS."images/red_cross.png' border='0' class='jsCommentDelImg' onclick='javascript:delCom(".$curid.");'>";
        $responce_array['datetime'] = "<img alt='' src='".JS_LIVE_ASSETS."images/calend.png' />".$this->getDate();
        $responce_array['name'] = $player['name'];
        $responce_array['posted'] = str_replace("\n", '<br />', htmlspecialchars($addcomm));
        $responce_array['error'] = '';

        return json_encode($responce_array);
    }
    public function del_comment()
    {
        $c_id = JRequest::getVar('cid', 0, 'get', 'int');

        $user = &JFactory::getUser();
        $dend = false;
        $db = &JFactory::getDBO();
        if (getVer() >= '1.6') {
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
        } else {
            if ($user->gid == 25) {
                $dend = true;
            }
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
}
