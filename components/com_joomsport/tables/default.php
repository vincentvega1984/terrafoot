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
class JTableMaps extends JTable
{
    public $id = null;
    public $m_name = null;
    public $map_descr = null;
    public function __construct(&$db)
    {
        parent::__construct('#__bl_maps', 'id', $db);
    }
}
class JTableTourn extends JTable
{
    public $id = null;
    public $name = null;
    public $descr = null;
    public $published = null;
    public $t_single = null;
    public $logo = null;
    public $tournament_type = null;
    public function __construct(&$db)
    {
        parent::__construct('#__bl_tournament', 'id', $db);
    }
}
class JTableSeason extends JTable
{
    public $s_id = null;
    public $s_name = null;
    public $s_descr = null;
    public $published = null;
    public $s_rounds = null;
    public $t_id = null;
    public $s_win_point = null;
    public $s_lost_point = null;
    public $s_enbl_extra = null;
    public $s_extra_win = null;
    public $s_extra_lost = null;
    public $s_draw_point = null;
    public $s_groups = null;
    public $s_win_away = null;
    public $s_draw_away = null;
    public $s_lost_away = null;
    public $s_participant = null;
    public $s_reg = null;
    public $reg_start = null;
    public $reg_end = null;
    public $s_rules = null;
    public $idtemplate = null;
    public $season_options = null;

    public function __construct(&$db)
    {
        parent::__construct('#__bl_seasons', 's_id', $db);
    }
}
class JTableClub extends JTable
{
    public $id = null;
    public $c_name = null;
    public $c_descr = null;
    public $def_img = null;
    public $c_emblem = null;
    public $c_city = null;

    public function __construct(&$db)
    {
        parent::__construct('#__bl_club', 'id', $db);
    }
}
class JTableTeams extends JTable
{
    public $id = null;
    public $t_name = null;
    public $t_descr = null;
    public $t_yteam = null;
    public $def_img = null;
    public $t_emblem = null;
    public $t_city = null;
    public $created_by = null;
    public $venue_id = null;
    public $club_id = null;
    public function __construct(&$db)
    {
        parent::__construct('#__bl_teams', 'id', $db);
    }
}
class JTablePos extends JTable
{
    public $p_id = null;
    public $p_name = null;
    public function __construct(&$db)
    {
        parent::__construct('#__bl_positions', 'p_id', $db);
    }
}
class JTablePlayer extends JTable
{
    public $id = null;
    public $first_name = null;
    public $last_name = null;
    public $nick = null;
    public $about = null;
    public $position_id = null;
    public $def_img = null;
    public $usr_id = null;
    public $country_id = null;
    public $registered = null;

    public function __construct(&$db)
    {
        parent::__construct('#__bl_players', 'id', $db);
    }
}
class JTablePhotos extends JTable
{
    public $id = null;
    public $ph_name = null;
    public $ph_filename = null;
    public $ph_descr = null;
    public function __construct(&$db)
    {
        parent::__construct('#__bl_photos', 'id', $db);
    }
}
class JTableMday extends JTable
{
    public $id = null;
    public $m_name = null;
    public $m_descr = null;
    public $s_id = null;
    public $is_playoff = null;
    public $k_format = null;
    public $t_type = null;
    public $start_date = null;
    public $end_date = null;

    public function __construct(&$db)
    {
        parent::__construct('#__bl_matchday', 'id', $db);
    }
}
class JTableMatch extends JTable
{
    public $id = null;
    public $m_id = null;
    public $team1_id = null;
    public $team2_id = null;
    public $score1 = null;
    public $score2 = null;
    public $match_descr = null;
    public $published = null;
    public $is_extra = null;
    public $m_played = null;
    public $m_date = null;
    public $m_time = null;
    public $m_location = null;
    public $k_ordering = null;
    public $k_title = null;
    public $k_stage = null;
    public $points1 = null;
    public $points2 = null;
    public $new_points = null;
    public $bonus1 = null;
    public $bonus2 = null;
    public $aet1 = null;
    public $aet2 = null;
    public $p_winner = null;
    public $venue_id = null;
    public $m_single = null;
    public $betavailable = null;
    public $betfinishdate = null;
    public $betfinishtime = null;
    public $k_type = null;
    public $options = null;

    public function __construct(&$db)
    {
        parent::__construct('#__bl_match', 'id', $db);
    }
}
class JTableEvents extends JTable
{
    public $id = null;
    public $e_name = null;
    public $e_img = null;
    public $e_descr = null;
    public $player_event = null;
    public $result_type = null;
    public $sumev1 = null;
    public $sumev2 = null;

    public function __construct(&$db)
    {
        parent::__construct('#__bl_events', 'id', $db);
    }
}
class JTableGroups extends JTable
{
    public $id = null;
    public $group_name = null;
    public $s_id = null;

    public function __construct(&$db)
    {
        parent::__construct('#__bl_groups', 'id', $db);
    }
}
class JTableFields extends JTable
{
    public $id = null;
    public $name = null;
    public $published = null;
    public $type = null;
    public $ordering = null;
    public $e_table_view = null;
    public $field_type = null;
    public $reg_exist = null;
    public $reg_require = null;
    public $fdisplay = null;
    public $faccess = null;
    public $person_category = 0;

    public function __construct(&$db)
    {
        parent::__construct('#__bl_extra_filds', 'id', $db);
    }
}
class JTableLang extends JTable
{
    public $id = null;
    public $lang_file = null;
    public $is_default = null;

    public function __construct(&$db)
    {
        parent::__construct('#__bl_languages', 'id', $db);
    }

    public function check()
    {
        $db = &JFactory::getDBO();
        $query = "SELECT lang_file FROM #__bl_languages WHERE id = '".$this->id."'";
        $db->SetQuery($query);
        $old_name = $db->LoadResult();
        if (isset($old_name) && $old_name == 'default') {
            $this->setError('Could not modify DEFAULT Language');

            return false;
        }
        $query = "SELECT count(*) FROM #__bl_languages WHERE id <> '".$this->id."' and lang_file = '".$this->lang_file."'";
        $db->SetQuery($query);
        $items_count = $db->LoadResult();
        if ($items_count > 0) {
            $this->setError('This name for Language is already exist');

            return false;
        }
        if ((trim($this->lang_file == '')) || (preg_match('/[0-9a-z]/', $this->lang_file) == false)) {
            $this->setError('Please enter valid Language name');

            return false;
        }

        return true;
    }
}

class JTableVenue extends JTable
{
    public $id = null;
    public $v_name = null;
    public $v_descr = null;
    public $v_defimg = null;
    public $v_address = null;
    public $v_coordx = null;
    public $v_coordy = null;
    public function __construct(&$db)
    {
        parent::__construct('#__bl_venue', 'id', $db);
    }
}

class JTableTempl extends JTable
{
    public $id = null;
    public $name = null;
    public $isdefault = null;
    public $variable1 = null;
    public $variable2 = null;
    public $variable3 = null;
    public $variable4 = null;
    public $variable5 = null;
    public $variable6 = null;
    public $variable7 = null;
    public function __construct(&$db)
    {
        parent::__construct('#__bl_templates', 'id', $db);
    }
}

class JTableTemplates extends JTable
{
    public $id = null;
    public $name = null;
    public $description = null;
    public $isdeleted = null;

    public function __construct(&$db)
    {
        parent::__construct('#__bl_betting_templates', 'id', $db);
    }
}

class JTableBettingEvents extends JTable
{
    public $id = null;
    public $name = null;
    public $type = null;
    public $difffrom = null;
    public $diffto = null;
    public $isdeleted = null;

    public function __construct(&$db)
    {
        parent::__construct('#__bl_betting_events', 'id', $db);
    }
}

class JTableBettingLogs extends JTable
{
    public $id = null;
    public $iduser = null;
    public $points = null;
    public $date = null;

    public function __construct(&$db)
    {
        parent::__construct('#__bl_betting_logs', 'id', $db);
    }

    public function addToLog($iduser, $points)
    {
        $this->iduser = $iduser;
        $this->points = $points;
        $this->date = date('Y-m-d H:i:s');
        $this->store();
    }
}

class JTableBettingUsers extends JTable
{
    public $id = null;
    public $iduser = null;
    public $points = null;

    public function __construct(&$db)
    {
        parent::__construct('#__bl_betting_users', 'id', $db);
    }

    public function changePoints($points)
    {
        $this->points += $points;
        $this->store();
    }
}

class JTableBettingCashRequests extends JTable
{
    public $id = null;
    public $iduser = null;
    public $points = null;
    public $status = null;

    public function __construct(&$db)
    {
        parent::__construct('#__bl_betting_requests_cash', 'id', $db);
    }

    public function getStatuses()
    {
        return array('pending', 'approved', 'rejected', 'postponed');
    }
}

class JTableBettingPointsRequests extends JTable
{
    public $id = null;
    public $iduser = null;
    public $points = null;
    public $status = null;

    public function __construct(&$db)
    {
        parent::__construct('#__bl_betting_requests_points', 'id', $db);
    }

    public function getStatuses()
    {
        return array('pending', 'approved', 'rejected', 'postponed');
    }
}

class JTableBettingCoeffs extends JTable
{
    public $id = null;
    public $idmatch = null;
    public $idevent = null;
    public $coeff1 = null;
    public $coeff2 = null;
    public $betfinishdate = null;
    public $betfinishtime = null;

    public function __construct(&$db)
    {
        parent::__construct('#__bl_betting_coeffs', 'id', $db);
    }
}

class JTableBettingUsersBets extends JTable
{
    public $id = null;
    public $iduser = null;
    public $won = null;

    public function __construct(&$db)
    {
        parent::__construct('#__bl_betting_users_bets', 'id', $db);
    }
}

class JTableBettingBets extends JTable
{
    public $id = null;
    public $idbet = null;
    public $idmatch = null;
    public $idevent = null;
    public $who = null;
    public $points = null;

    public function __construct(&$db)
    {
        parent::__construct('#__bl_betting_bets', 'id', $db);
    }
}

class JTableBoxFields extends JTable
{
    public $id = null;
    public $name = null;
    public $complex = null;
    public $published = null;
    public $parent_id = null;
    public $ftype = null;
    public $options = null;
    public $ordering = null;
    public $displayonfe = null;
    public function __construct(&$db)
    {
        parent::__construct('#__bl_box_fields', 'id', $db);
    }
}
class JTablePersonsCategory extends JTable
{
    public $id = null;
    public $name = null;

    public function __construct(&$db)
    {
        parent::__construct('#__bl_persons_category', 'id', $db);
    }
}
class JTablePersons extends JTable
{
    public $id = null;
    public $first_name = null;
    public $last_name = null;
    public $def_img = null;
    public $category_id = null;
    public $about = null;
    
    public function __construct(&$db)
    {
        parent::__construct('#__bl_persons', 'id', $db);
    }
}