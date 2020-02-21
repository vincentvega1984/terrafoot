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

class pluginJoomsportStandings
{
    public static function generateTableStanding($args)
    {
        $season_id = (isset($args['season_id']) && $args['season_id']) ? $args['season_id'] : 0;
        if (!$season_id) {
            return;
        }
        new calcTable($season_id);
    }
}

class calcTable
{
    public $lists = null;
    public $id = null;
    public $object = null;
    public function __construct($season_id)
    {
        require_once JS_PATH_ENV_CLASSES.'class-jsport-participant.php';
        require_once JS_PATH_OBJECTS.'class-jsport-group.php';
        require_once JS_PATH_OBJECTS.'class-jsport-season.php';
        global $jsDatabase;
        $query = 'SELECT COUNT(md.id)'
                .' FROM '.DB_TBL_MATCHDAY.' as md '
                .' JOIN '.DB_TBL_SEASONS.' as s ON s.s_id = md.s_id'
                .' JOIN '.DB_TBL_TOURNAMENT.' as t ON t.id = s.t_id'
                .' WHERE md.s_id = '.$season_id.' AND md.t_type = 0 AND is_playoff = 0';
        $this->id = $season_id;

        $obj = new classJsportSeason($season_id);
        $this->object = $obj->getObject();

        $mdays_count = $jsDatabase->selectValue($query);
        //if ($mdays_count) {
            //get groups
            $groupsObj = new classJsportGroup($season_id);
            $groups = $groupsObj->getGroups();
            $this->lists['columns'] = $this->getTournColumns();
            $this->lists['groups'] = $groups;
            $columnsCell = array();
            //get participants
            if (count($groups)) {
                foreach ($groups as $group) {
                    $columnsCell[$group->group_name] = $this->getTable($group->id);
                }
            } else {
                $columnsCell[] = $this->getTable(0);
            }
            $this->lists['columnsCell'] = $columnsCell;
        //}
    }

    public function getTournColumns()
    {
        global $jsDatabase;
        $this->lists['available_options'] = array(
            'played_chk' => classJsportLanguage::get('BL_TBL_PLAYED'),
            'emblem_chk' => '',
            'win_chk' => classJsportLanguage::get('BL_TBL_WINS'),
            'lost_chk' => classJsportLanguage::get('BL_TBL_LOST'),
            'draw_chk' => classJsportLanguage::get('BL_TBL_DRAW'),
            'otwin_chk' => classJsportLanguage::get('BL_TBL_EXTRAWIN'),
            'otlost_chk' => classJsportLanguage::get('BL_TBL_EXTRALOST'),
            'diff_chk' => classJsportLanguage::get('BL_TBL_DIFF'),
            'gd_chk' => classJsportLanguage::get('BL_TBL_GD'),
            'point_chk' => classJsportLanguage::get('BL_TBL_POINTS'),
            'percent_chk' => classJsportLanguage::get('BL_TBL_WINPERCENT'),
            'goalscore_chk' => classJsportLanguage::get('BL_TBL_TTGSC'),
            'goalconc_chk' => classJsportLanguage::get('BL_TBL_TTGCC'),
            'winhome_chk' => classJsportLanguage::get('BL_TBL_TTWHC'),
            'winaway_chk' => classJsportLanguage::get('BL_TBL_TTWAC'),
            'drawhome_chk' => classJsportLanguage::get('BL_TBL_TTDHC'),
            'drawaway_chk' => classJsportLanguage::get('BL_TBL_TTDAC'),
            'losthome_chk' => classJsportLanguage::get('BL_TBL_TTLHC'),
            'lostaway_chk' => classJsportLanguage::get('BL_TBL_TTLAC'),
            'pointshome_chk' => classJsportLanguage::get('BL_TBL_TTPHC'),
            'pointsaway_chk' => classJsportLanguage::get('BL_TBL_TTPAC'),
            'grwin_chk' => classJsportLanguage::get('BL_WINGROUP'),
            'grlost_chk' => classJsportLanguage::get('BL_LOSTGROUP'),
            'grwinpr_chk' => classJsportLanguage::get('BL_PRCGROUP'),
            'curform_chk' => classJsportLanguage::get('BLFA_CURFORM'),
            );

        $lists = array();

        $query = 'SELECT *'
                .' FROM '.DB_TBL_SEASON_OPTION
                ." WHERE opt_value='1' AND s_id = ".$this->id." AND opt_name != 'equalpts_chk'"
                .' ORDER BY ordering';
        $listsss = $jsDatabase->select($query);
        for ($i = 0;$i < count($listsss);++$i) {
            $vars = get_object_vars($listsss[$i]);
            $lists[$vars['opt_name']] = $vars['opt_value'];
        }

        return $lists;
    }
    public function getTable($group_id)
    {
        $table = $this->getTournColumnsVar($group_id);
    }
    public function getTournColumnsVar($group_id)
    {
        global $jsDatabase;
        $obj = new classJsportParticipant($this->id);
        $participants = $obj->getParticipants($group_id);
        
        $query = 'SELECT opt_value FROM '.DB_TBL_SEASON_OPTION.''
                .' WHERE s_id = '.$this->id." AND opt_name='equalpts_chk'";
                $equalpts_chk = $jsDatabase->selectValue($query);
        $array = array();
        $intA = 0;
        if (count($participants)) {
            foreach ($participants as $participant) {
                $query = 'SELECT COUNT(m.id) as played_chk,'
                        ." SUM(if(m.score1 > m.score2 AND m.is_extra = 0 AND m.team1_id = {$participant->id},1,0)) as winhome_chk,"
                        ." SUM(if(m.score2 > m.score1 AND m.is_extra = 0 AND m.team2_id = {$participant->id},1,0)) as winaway_chk,"
                        ." SUM(if(m.score2 = m.score1 AND m.team1_id = {$participant->id},1,0)) as drawhome_chk,"
                        ." SUM(if(m.score2 = m.score1 AND m.team2_id = {$participant->id},1,0)) as drawaway_chk,"
                        ." SUM(if(m.score1 < m.score2 AND m.is_extra = 0 AND m.team1_id = {$participant->id},1,0)) as losthome_chk,"
                        ." SUM(if(m.score2 < m.score1 AND m.is_extra = 0 AND m.team2_id = {$participant->id},1,0)) as lostaway_chk,"
                        ." SUM(if(m.score1 > m.score2 AND m.is_extra = 0 AND m.team1_id = {$participant->id} AND m.new_points = '0',1,0)) as home_win_pts,"
                        ." SUM(if(m.score2 > m.score1 AND m.is_extra = 0 AND m.team2_id = {$participant->id} AND m.new_points = '0',1,0)) as away_win_pts,"
                        ." SUM(if(m.score2 = m.score1 AND m.team1_id = {$participant->id} AND m.new_points = '0',1,0)) as home_draw_pts,"
                        ." SUM(if(m.score2 = m.score1 AND m.team2_id = {$participant->id} AND m.new_points = '0',1,0)) as away_draw_pts,"
                        ." SUM(if(m.score1 < m.score2 AND m.is_extra = 0 AND m.team1_id = {$participant->id} AND m.new_points = '0',1,0)) as home_loose_pts,"
                        ." SUM(if(m.score2 < m.score1 AND m.is_extra = 0 AND m.team2_id = {$participant->id} AND m.new_points = '0',1,0)) as away_loose_pts,"
                        ." SUM(if(m.team1_id = {$participant->id},m.bonus1,0)) as home_bonus,"
                        ." SUM(if(m.team2_id = {$participant->id},m.bonus2,0)) as away_bonus,"
                        ." SUM(if(m.team1_id = {$participant->id},m.score1,m.score2)) as goalscore_chk,"
                        ." SUM(if(m.team1_id = {$participant->id},m.score2,m.score1)) as goalconc_chk,"
                        ." SUM(if(m.team1_id = {$participant->id} AND m.new_points = '1',m.points1,0)) as home_points,"
                        ." SUM(if(m.team2_id = {$participant->id} AND m.new_points = '1',m.points2,0)) as away_points"
                        .' FROM '.DB_TBL_MATCH.' as m'
                        .' JOIN '.DB_TBL_MATCHDAY.' as md ON m.m_id = md.id'
                        .' WHERE md.s_id = '.$this->id
                        .' AND m.published = 1'
                        ." AND (m.team1_id = {$participant->id} OR m.team2_id = {$participant->id} )"
                        .' AND md.is_playoff = 0'
                        .' AND m.m_played = 1'
                        .' AND md.t_type = 0';

                $array[$intA] = $jsDatabase->selectArray($query);
                $array[$intA]['id'] = $participant->id;
                $partObj = $obj->getParticipiantObj($participant->id);
                $array[$intA]['name'] = $partObj->getName();
                $array[$intA]['sortname'] = $partObj->getName(false);
                $wins_ext = 0;
                $loose_ext = 0;
                if ($this->object->s_enbl_extra) {
                    $query = 'SELECT COUNT(*)'
                                        .' FROM '.DB_TBL_MATCH.' as m'
                                        .' JOIN '.DB_TBL_MATCHDAY.' as md ON m.m_id = md.id'
                                        .' WHERE md.s_id = '.$this->id.' AND m.published = 1'
                                        .' AND ((m.team2_id = '.$participant->id.' AND m.score2 > m.score1)'
                                        .' OR ('.$participant->id.' = m.team1_id AND m.score1 > m.score2))'
                                        .' AND m.is_extra = 1 AND md.is_playoff = 0'
                                        .' AND m.m_played = 1 AND md.t_type = 0';

                    $wins_ext = $jsDatabase->selectValue($query);

                    $query = 'SELECT COUNT(*)'
                                        .' FROM '.DB_TBL_MATCH.' as m'
                                        .' JOIN '.DB_TBL_MATCHDAY.' as md ON m.m_id = md.id'
                                        .' WHERE md.s_id = '.$this->id.' AND m.published = 1'
                                        .' AND ((m.team2_id = '.$participant->id.' AND m.score2 < m.score1)'
                                        .' OR ('.$participant->id.' = m.team1_id AND m.score1 < m.score2))'
                                        .' AND m.is_extra = 1 AND md.is_playoff = 0'
                                        .' AND m.m_played = 1 AND md.t_type = 0';

                    $loose_ext = $jsDatabase->selectValue($query);
                    
                    $query = 'SELECT COUNT(*)'
                                        .' FROM '.DB_TBL_MATCH.' as m'
                                        .' JOIN '.DB_TBL_MATCHDAY.' as md ON m.m_id = md.id'
                                        .' WHERE md.s_id = '.$this->id.' AND m.published = 1'
                                        .' AND ((m.team2_id = '.$participant->id.' AND m.score2 > m.score1)'
                                        .' OR ('.$participant->id.' = m.team1_id AND m.score1 > m.score2))'
                                        .' AND m.is_extra = 1 AND md.is_playoff = 0'
                                        .' AND m.m_played = 1 AND md.t_type = 0 AND m.new_points = 0';

                    $wins_ext_pts = $jsDatabase->selectValue($query);

                    $query = 'SELECT COUNT(*)'
                                        .' FROM '.DB_TBL_MATCH.' as m'
                                        .' JOIN '.DB_TBL_MATCHDAY.' as md ON m.m_id = md.id'
                                        .' WHERE md.s_id = '.$this->id.' AND m.published = 1'
                                        .' AND ((m.team2_id = '.$participant->id.' AND m.score2 < m.score1)'
                                        .' OR ('.$participant->id.' = m.team1_id AND m.score1 < m.score2))'
                                        .' AND m.is_extra = 1 AND md.is_playoff = 0'
                                        .' AND m.m_played = 1 AND md.t_type = 0 AND m.new_points = 0';

                    $loose_ext_pts = $jsDatabase->selectValue($query);
                }

                        //calculate columns vars
                        $array[$intA]['win_chk'] = $array[$intA]['winhome_chk'] + $array[$intA]['winaway_chk'];
                $array[$intA]['draw_chk'] = $array[$intA]['drawhome_chk'] + $array[$intA]['drawaway_chk'];
                $array[$intA]['lost_chk'] = $array[$intA]['losthome_chk'] + $array[$intA]['lostaway_chk'];
                $array[$intA]['diff_chk'] = $array[$intA]['goalscore_chk'].' - '.$array[$intA]['goalconc_chk'];
                $array[$intA]['gd_chk'] = $array[$intA]['goalscore_chk'] - $array[$intA]['goalconc_chk'];
                $array[$intA]['point_chk'] =
                                ($array[$intA]['home_win_pts'] * $this->object->s_win_point + $array[$intA]['away_win_pts'] * $this->object->s_win_away)
                                + ($array[$intA]['home_draw_pts'] * $this->object->s_draw_point + $array[$intA]['away_draw_pts'] * $this->object->s_draw_away)
                                + ($array[$intA]['home_loose_pts'] * $this->object->s_lost_point + $array[$intA]['away_loose_pts'] * $this->object->s_lost_away)
                                + $array[$intA]['home_points'] + $array[$intA]['away_points']
                                + $array[$intA]['home_bonus'] + $array[$intA]['away_bonus']
                                + $participant->bonus_point
                +(($this->object->s_enbl_extra) ? ($wins_ext_pts * $this->object->s_extra_win + $loose_ext_pts * $this->object->s_extra_lost) : 0);

                if ($array[$intA]['played_chk']) {
                    $array[$intA]['percent_chk'] = sprintf("%0.3f",($array[$intA]['win_chk'] + ($array[$intA]['draw_chk'] / 2)) / ($array[$intA]['played_chk']));
                } else {
                    $array[$intA]['percent_chk'] = 0;
                }

                        //pointshome_chk
                        $array[$intA]['pointshome_chk'] =
                                ($array[$intA]['home_win_pts'] * $this->object->s_win_point)
                                + ($array[$intA]['home_draw_pts'] * $this->object->s_draw_point)
                                + ($array[$intA]['home_loose_pts'] * $this->object->s_lost_point)
                                + $array[$intA]['home_points']
                                + $array[$intA]['home_bonus'];
                        //pointsaway_chk
                        $array[$intA]['pointsaway_chk'] =
                                ($array[$intA]['away_win_pts'] * $this->object->s_win_point)
                                + ($array[$intA]['away_draw_pts'] * $this->object->s_draw_point)
                                + ($array[$intA]['away_loose_pts'] * $this->object->s_lost_point)
                                + $array[$intA]['away_points']
                                + $array[$intA]['away_bonus'];

                        //otwin_chk
                        $array[$intA]['otwin_chk'] = $wins_ext;
                $array[$intA]['otlost_chk'] = $loose_ext;
                if ($group_id) {
                    $this->inGroupsVar($array[$intA], $group_id);
                }
                
                

                if ($equalpts_chk) {
                    $array[$intA]['avulka_v'] = '';
                    $array[$intA]['avulka_cf'] = '';
                    $array[$intA]['avulka_cs'] = '';
                    $array[$intA]['avulka_qc'] = '';
                }
                
                ++$intA;
            }
            $this->sortTable($array);
            $this->saveToDB($array, $group_id);
            //$array = $this->getTable($group_id);
        }else{
            $query = 'DELETE FROM '.DB_TBL_SEASON_TABLE.' '
                .' WHERE season_id = '.$this->id
                .' AND group_id = '.$group_id;
                $jsDatabase->delete($query);
        }
        //return $array;
    }

    public function sortTable(&$table_view)
    {
        global $jsDatabase;
        $query = 'SELECT opt_value FROM '.DB_TBL_SEASON_OPTION.''
                .' WHERE s_id = '.$this->id." AND opt_name='equalpts_chk'";
        $equalpts_chk = $jsDatabase->selectValue($query);

        if ($equalpts_chk) {


            $pts_arr = array();
            $pts_equal = array();
            foreach ($table_view as $tv) {
                if (!in_array($tv['point_chk'], $pts_arr)) {
                    $pts_arr[] = $tv['point_chk'];
                } else {
                    if (!in_array($tv['point_chk'], $pts_equal)) {
                        $pts_equal[] = $tv['point_chk'];
                    }
                }
            }
            $k = 0;
            $team_arr = array();
            foreach ($pts_equal as $pts) {
                foreach ($table_view as $tv) {
                    if ($tv['point_chk'] == $pts) {
                        $team_arr[$k][] = $tv['id'];
                    }
                }
                ++$k;
            }

            foreach ($team_arr as $tm) {

                foreach ($tm as $tm_one) {
                    $query = 'SELECT COUNT(*)'
                            .' FROM '.DB_TBL_MATCHDAY.' as md, '.DB_TBL_MATCH.' as m'
                            //. " #__bl_teams as t1, #__bl_teams as t2 "
                            .' WHERE m.m_id = md.id AND m.published = 1'
                            .' AND md.s_id='.$this->id.''
                            //. "  AND m.team1_id = t1.id"
                            //. " AND m.team2_id = t2.id"
                            .' AND m.m_played=1 '
                            .' AND md.t_type = 0 '
                            .' AND ((m.team1_id = '.$tm_one.' AND m.score1>m.score2 AND m.team2_id IN ('.implode(',', $tm).')) OR (m.team2_id='.$tm_one.' AND m.score1<m.score2 AND m.team1_id IN ('.implode(',', $tm).')))';
                            //. " AND ((t1.id = ".$tm_one." AND m.score1>m.score2 AND t2.id IN (".implode(',',$tm).")) OR (t2.id=".$tm_one." AND m.score1<m.score2 AND t1.id IN (".implode(',',$tm).")))";

                    $matchs_avulsa_win = $jsDatabase->selectValue($query);
                    $query = 'SELECT COUNT(*)'
                            .' FROM '.DB_TBL_MATCHDAY.' as md, '.DB_TBL_MATCH.' as m'
                            //. " #__bl_teams as t1, #__bl_teams as t2"
                            .' WHERE m.m_id = md.id AND m.published = 1'
                            .' AND md.s_id='.$this->id.''
                            //. " AND m.team1_id = t1.id AND m.team2_id = t2.id"
                            .' AND m.m_played=1 AND md.t_type = 0'
                            .' AND ((m.team1_id = '.$tm_one.' AND m.score1=m.score2 AND m.team2_id IN ('.implode(',', $tm).')) OR (m.team2_id='.$tm_one.' AND m.score1=m.score2 AND m.team1_id IN ('.implode(',', $tm).')))';
                            //. " AND ((t1.id = ".$tm_one." AND m.score1=m.score2 AND t2.id IN (".implode(',',$tm).")) OR (t2.id=".$tm_one." AND m.score1=m.score2 AND t1.id IN (".implode(',',$tm).")))";

                                        $matchs_avulsa_win_c = 3 * $matchs_avulsa_win + $jsDatabase->selectValue($query);
                    $tm_equal_win = array();

                    foreach ($tm as $tm_other) {
                        $query = 'SELECT COUNT(*)'
                            .' FROM '.DB_TBL_MATCHDAY.' as md, '.DB_TBL_MATCH.' as m'
                            //. " #__bl_teams as t1, #__bl_teams as t2"
                            .' WHERE m.m_id = md.id AND m.published = 1'
                            .' AND md.s_id='.$this->id.' '
                            //. " AND m.team1_id = t1.id AND m.team2_id = t2.id"
                            .' AND m.m_played=1 AND md.t_type = 0'
                            .' AND ((m.team1_id = '.$tm_other.' AND m.score1>m.score2 AND m.team2_id IN ('.implode(',', $tm).')) OR (m.team2_id='.$tm_other.' AND m.score1<m.score2 AND m.team1_id IN ('.implode(',', $tm).')))';

                            //. " AND ((t1.id = ".$tm_other." AND m.score1>m.score2 AND t2.id IN (".implode(',',$tm).")) OR (t2.id=".$tm_other." AND m.score1<m.score2 AND t1.id IN (".implode(',',$tm).")))";

                        $matchs_avulsa_win_other = $jsDatabase->selectValue($query);

                        if ($matchs_avulsa_win_other == $matchs_avulsa_win) {
                            $tm_equal_win[] = $tm_other;
                        }
                    }

                    $query = 'SELECT SUM(score1) as sh,'
                            .' SUM(score2) as sw'
                            .' FROM '.DB_TBL_MATCHDAY.' as md, '.DB_TBL_MATCH.' as m'
                            //. " #__bl_teams as t1, #__bl_teams as t2"
                            .' WHERE m.m_id = md.id AND m.published = 1'
                            .' AND m.m_played=1 AND md.s_id='.$this->id.' '
                            //. " AND m.team1_id = t1.id AND m.team2_id = t2.id"
                            .' AND md.t_type = 0'
                            .' AND ((m.team1_id = '.$tm_one.' AND m.team2_id IN ('.implode(',', $tm_equal_win).')))';

                            //. " AND ((t1.id = ".$tm_one." AND t2.id IN (".implode(',',$tm_equal_win).")))";

                    $matchs_avulsa_score = $jsDatabase->selectObject($query);

                    $query = 'SELECT SUM(score2) as sh,'
                            .' SUM(score1) as sw'
                            .' FROM '.DB_TBL_MATCHDAY.' as md, '.DB_TBL_MATCH.' as m'
                            //. " #__bl_teams as t1, #__bl_teams as t2"
                            .' WHERE m.m_id = md.id AND m.published = 1'
                            .' AND m.m_played=1 AND md.s_id='.$this->id.' '
                            //. " AND m.team1_id = t1.id AND m.team2_id = t2.id"
                            .' AND md.t_type = 0'
                            .' AND ((m.team2_id='.$tm_one.' AND m.team1_id IN ('.implode(',', $tm_equal_win).')))';

                    $matchs_avulsa_rec = $jsDatabase->selectObject($query);

                    $matchs_avulsa_res = intval($matchs_avulsa_score->sh) + intval($matchs_avulsa_rec->sh);
                    $matchs_avulsa_res2 = intval($matchs_avulsa_score->sw) + intval($matchs_avulsa_rec->sw);

                    for ($b = 0;$b < count($table_view);++$b) {
                        if ($table_view[$b]['id'] == $tm_one) {
                            $table_view[$b]['avulka_v'] = $matchs_avulsa_win_c;
                            $table_view[$b]['avulka_cf'] = $matchs_avulsa_res;
                            $table_view[$b]['avulka_cs'] = $matchs_avulsa_res2;
                            $table_view[$b]['avulka_qc'] = $matchs_avulsa_res - $matchs_avulsa_res2;
                        }
                    }
                }
            }
        }
        //--/playeachother---///

        $sort_arr = array();
        foreach ($table_view as $uniqid => $row) {
            foreach ($row as $key => $value) {
                $sort_arr[$key][$uniqid] = $value;
            }
        }

        if (count($sort_arr)) {
            // sort fields 1-points, 2-wins percent, /*3-if equal between teams*/, 4-goal difference, 5-goal score
            $query = 'SELECT *'
                                .' FROM '.DB_TBL_RANKSORT.''
                                .' WHERE seasonid='.$this->id.''
                                .' ORDER BY ordering';

            $savedsort = $jsDatabase->select($query);
            $argsort = array();
            $argsort_way = array();
            if (count($savedsort)) {
                foreach ($savedsort as $sortop) {
                    switch ($sortop->sort_field) {
                        case '1': $argsort[][0] = $sort_arr['point_chk'];        break;
                        case '2': $argsort[][0] = $sort_arr['percent_chk'];        break;
                        case '3': $argsort[][0] = $sort_arr['point_chk'];        break; /* not used */
                        case '4': $argsort[][0] = $sort_arr['gd_chk'];            break;
                        case '5': $argsort[][0] = $sort_arr['goalscore_chk'];    break;
                        case '6': $argsort[][0] = $sort_arr['played_chk'];        break;
                        case '7': $argsort[][0] = $sort_arr['win_chk'];        break;
                    }

                    $argsort_way[] = $sortop->sort_way;
                }
            }

            if ($equalpts_chk) {
                array_multisort($sort_arr['point_chk'], SORT_DESC, $sort_arr['avulka_v'], SORT_DESC, $sort_arr['avulka_qc'], SORT_DESC, $sort_arr['avulka_cf'], SORT_DESC, $sort_arr['gd_chk'], SORT_DESC, $sort_arr['goalscore_chk'], SORT_DESC,$sort_arr['sortname'],SORT_ASC, $table_view);
            } else {
                
                array_multisort((isset($argsort[0][0]) ? $argsort[0][0] : $sort_arr['point_chk']), (isset($argsort_way[0]) ? ($argsort_way[0] ? SORT_ASC : SORT_DESC) : SORT_DESC), (isset($argsort[1][0]) ? $argsort[1][0] : $sort_arr['gd_chk']), (isset($argsort_way[1]) ? ($argsort_way[1] ? SORT_ASC : SORT_DESC) : SORT_DESC), (isset($argsort[2][0]) ? $argsort[2][0] : $sort_arr['goalscore_chk']), (isset($argsort_way[2]) ? ($argsort_way[2] ? SORT_ASC : SORT_DESC) : SORT_DESC), (isset($argsort[3][0]) ? $argsort[3][0] : $sort_arr['win_chk']), (isset($argsort_way[3]) ? ($argsort_way[3] ? SORT_ASC : SORT_DESC) : SORT_DESC), $sort_arr['sortname'],SORT_ASC, $table_view);

            }
        }
    }

    public function inGroupsVar(&$array, $group_id)
    {
        global $jsDatabase;
        // in groups

        $query = 'SELECT t_id'
                .' FROM '.DB_TBL_GRTEAMS.''
                .' WHERE t_id != '.$array['id']
                .' AND g_id = '.$group_id;

        $gtid = $jsDatabase->selectColumn($query);

        //if($season_par->s_groups == 1){	//updt
                if (count($gtid)) {
                    $query = 'SELECT '
                                ." SUM(if(m.score1 > m.score2 AND m.team1_id = {$array['id']},1,0)) as win_home,"
                                ." SUM(if(m.score1 < m.score2 AND m.team2_id = {$array['id']},1,0)) as win_away,"
                                ." SUM(if(m.score1 = m.score2 AND m.team1_id = {$array['id']},1,0)) as draw_home,"
                                ." SUM(if(m.score1 = m.score2 AND m.team2_id = {$array['id']},1,0)) as draw_away,"
                                ." SUM(if(m.score1 < m.score2 AND m.team1_id = {$array['id']},1,0)) as loose_home,"
                                ." SUM(if(m.score1 > m.score2 AND m.team2_id = {$array['id']},1,0)) as loose_away"
                                .' FROM '.DB_TBL_MATCH.' as m'
                                .' JOIN '.DB_TBL_MATCHDAY.' as md ON m.m_id = md.id'
                                .' WHERE m.m_id = md.id AND md.s_id = '.$this->id.''
                                .' AND m.published = 1'
                                .($this->object->s_enbl_extra ? '' : ' AND m.is_extra = 0')
                                .' AND ('
                                .'('.$array['id'].' = m.team1_id AND  m.team2_id IN ('.implode(',', $gtid).'))'
                                .' OR '
                                .'('.$array['id'].' = m.team2_id AND m.team1_id IN ('.implode(',', $gtid).')) )'
                                .' AND m.m_played = 1 AND md.is_playoff = 0 AND md.t_type = 0';

                    $gr_array = $jsDatabase->selectArray($query);

                    $wins_gr = $array['grwin_chk'] = $gr_array['win_home'] + $gr_array['win_away'];
                    $loose_gr = $array['grlost_chk'] = $gr_array['loose_home'] + $gr_array['loose_away'];
                    $draw_gr = $gr_array['draw_home'] + $gr_array['draw_away'];

                    if (($wins_gr + $loose_gr + $draw_gr) > 0) {
                        $array['grwinpr_chk'] = sprintf("%0.3f",($wins_gr + $draw_gr / 2) / ($wins_gr + $loose_gr + $draw_gr));
                    } else {
                        $array['grwinpr_chk'] = 0;
                    }
                } else {
                    $array['grwin_chk'] = 0;
                    $array['grlost_chk'] = 0;
                }
        //}
    }

    public function saveToDB($array, $group_id)
    {
        global $jsDatabase;
        $query = 'DELETE FROM '.DB_TBL_SEASON_TABLE.' '
                .' WHERE season_id = '.$this->id
                .' AND group_id = '.$group_id;
        $jsDatabase->delete($query);
        $intA = 1;
        foreach ($array as $tbl) {
            unset($tbl['name']);
            unset($tbl['sortname']);
            $options = json_encode($tbl);
            $query = 'INSERT INTO '.DB_TBL_SEASON_TABLE.' (season_id,group_id,participant_id,options,ordering) '
                    ." VALUES({$this->id},{$group_id},{$tbl['id']},'".$options."',{$intA})";
            $jsDatabase->insert($query);
            ++$intA;
        }
    }
}
