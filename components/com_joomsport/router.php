<?php
/**
* @version		$Id: router.php 10711 2008-08-21 10:09:03Z eddieajau $
*
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
function joomsportBuildRoute(&$query)
{
    $segments = array();
    $db = JFactory::getDBO();
    $app = JFactory::getApplication();
    $menu = $app->getMenu();
    if (empty($query['Itemid'])) {
        $menuItem = $menu->getActive();
    } else {
        $menuItem = $menu->getItem($query['Itemid']);
    }
    $view_str = '';
    if (isset($query['view'])) {
        $segments[] = $view_str = $query['view'];
        unset($query['view']);
    } elseif (isset($query['task'])) {
        $segments[] = $view_str = $query['task'];
        unset($query['task']);
    }
    if (isset($query['sid'])) {
        $sid = $query['sid'];
        $query_string = "SELECT CONCAT(t.name,' ',s.s_name) as name FROM #__bl_tournament as t, #__bl_seasons as s WHERE s.s_id = ".intval($sid).' AND s.t_id = t.id ORDER BY t.name, s.s_name';
        $db->setQuery($query_string);
        $sname = $db->loadResult();
        if ($sname) {
            $segments[] = $query['sid'].'-'.urlencode(str_replace('/', '-', toAscii($sname)));
        } else {
            $segments[] = $query['sid'];
        }

        unset($query['sid']);
    };
    if (isset($query['tid'])) {
        $query_string = 'SELECT t_name FROM #__bl_teams WHERE id = '.intval($query['tid']);
        $db->setQuery($query_string);
        $tname = $db->loadResult();

        $segments[] = $query['tid'].'-'. urlencode(str_replace('/', '', toAscii($tname)));
        unset($query['tid']);
    };
    if (isset($query['id'])) {
        if ($view_str == 'player') {
            $query_string = "SELECT CONCAT(first_name, ' ', last_name) FROM #__bl_players WHERE id = ".intval(($query['id']));
            $db->setQuery($query_string);
            $pname = $db->loadResult();
            $segments[] = $query['id'].'-'.urlencode(str_replace('/', '', toAscii($pname)));
        }elseif ($view_str == 'person') {
            $query_string = "SELECT CONCAT(first_name, ' ', last_name) FROM #__bl_persons WHERE id = ".intval(($query['id']));
            $db->setQuery($query_string);
            $pname = $db->loadResult();
            $segments[] = $query['id'].'-'.urlencode(str_replace('/', '', toAscii($pname)));
        }elseif ($view_str == 'view_match') {
        
            $query_string = 'SELECT s_id FROM #__bl_matchday as md, #__bl_match as m  WHERE md.id=m.m_id AND m.id = '.intval($query['id']);
            $db->setQuery($query_string);
            $s_id = $db->loadResult();

            $query_string = 'SELECT t.t_single FROM #__bl_tournament as t, #__bl_seasons as s WHERE s.s_id = '.intval($s_id).' AND s.t_id = t.id ORDER BY t.name, s.s_name';
            $db->setQuery($query_string);
            $t_single = $db->loadResult();

            if ($t_single) {
                $query_string = "SELECT m.score1,m.score2,m.m_played, CONCAT(t1.first_name,' ',t1.last_name) as home, CONCAT(t2.first_name,' ',t2.last_name) as away"
                     .' FROM #__bl_matchday as md, #__bl_match as m LEFT JOIN #__bl_players as t1 ON m.team1_id = t1.id LEFT JOIN #__bl_players as t2 ON m.team2_id = t2.id'
                     .' WHERE m.m_id = md.id AND m.published = 1 AND m.id = '.intval($query['id']);
            } else {
                $query_string = 'SELECT m.score1,m.score2,m.m_played,t1.t_name as home, t2.t_name as away'
                                            .' FROM #__bl_matchday as md, #__bl_match as m LEFT JOIN #__bl_teams as t1 ON m.team1_id = t1.id LEFT JOIN #__bl_teams as t2 ON m.team2_id = t2.id'
                                            .' WHERE m.m_id = md.id AND m.published = 1  AND m.id = '.intval($query['id']);
            }

            $db->setQuery($query_string);
            $match = $db->loadObject();

            $match_str = '';
            if ($match) {
                $match_str .= $match->home;
                if ($match->m_played) {
                    $match_str .= ' '.$match->score1.':'.$match->score2.' ';
                } else {
                    $match_str .= ' vs ';
                }
                $match_str .= $match->away;
            }
            $segments[] = $query['id'].'-'.urlencode(str_replace('/', '', toAscii($match_str)));
        } else {
            $segments[] = $query['id'];
        }
        unset($query['id']);
    };

    if (isset($query['cid'][0])) {
        $segments[] = $query['cid'][0];
        unset($query['cid']);
    };
    if (isset($query['mid'])) {
        $segments[] = $query['mid'];
        unset($query['mid']);
    };
    if (isset($query['controller'])) {
        $segments[] = $query['controller'];
        unset($query['controller']);
    };

    return $segments;
}

function joomsportParseRoute($segments)
{
    $vars = array();
    //ob_start();
    switch ($segments[0]) {
        case 'team':
            $vars['view'] = 'team';
            $id = explode(':', $segments[1]);

            $vars['sid'] = (int) $id[0];
            $id = explode(':', $segments[2]);
            $vars['tid'] = (int) $id[0];
            if (!isset($segments[2])) {
                $vars['sid'] = 0;
                $id = explode(':', $segments[1]);

                $vars['tid'] = (int) $id[0];
            }

        break;
        case 'player':
            $vars['view'] = 'player';
            $id = explode(':', $segments[1]);
            $vars['sid'] = (int) $id[0];
            //$id = explode(':', $segments[2]);
            $id = isset($segments[2]) ? explode(':', $segments[2]) : array(0);
            $vars['id'] = (int) $id[0];
            if (!isset($segments[2])) {
                $vars['sid'] = 0;
                $id = explode(':', $segments[1]);

                $vars['id'] = (int) $id[0];
            }

        break;
        case 'person':
            $vars['view'] = 'person';
            $id = explode(':', $segments[1]);
            $vars['id'] = (int) $id[0];

        break;
        case 'calendar':
        $vars['view'] = 'calendar';

        $id = explode(':', $segments[1]);
        $vars['sid'] = (int) $id[0];
        break;
        case 'playerlist':
        $vars['view'] = 'playerlist';

        $id = explode(':', $segments[1]);
        $vars['sid'] = (int) $id[0];
        break;

        case 'teamlist':
        $vars['view'] = 'teamlist';

        $id = explode(':', $segments[1]);
        $vars['sid'] = (int) $id[0];
        break;

        case 'view_match':
        case 'match':
        $vars['view'] = 'match';
        $id = explode(':', $segments[1]);
        $vars['id'] = (int) $id[0];
        break;
        case 'matchday':
        $vars['view'] = 'matchday';
        $id = explode(':', $segments[1]);
        $vars['id'] = (int) $id[0];
        break;
        case 'admin_team':
            $vars['view'] = 'admin_team';
            $vars['controller'] = 'admin';
            $id = explode(':', $segments[1]);
            $vars['sid'] = (int) $id[0];
        break;

        case 'admin_matchday':
            $vars['view'] = 'admin_matchday';
            $vars['controller'] = 'admin';
            $id = explode(':', $segments[1]);
            $vars['sid'] = (int) $id[0];

        break;
        case 'edit_team':
            $vars['view'] = 'edit_team';

            if (isset($segments[2]) && $segments[2] == 'moder') {
                $id = explode(':', $segments[1]);
                $vars['tid'] = (int) $id[0];
                $vars['controller'] = 'moder';
            } else {
                $id = explode(':', $segments[1]);
                $vars['sid'] = (int) $id[0];
                $id = explode(':', $segments[2]);
                $vars['cid'][0] = (int) $id[0];
                $vars['controller'] = 'admin';
            }

        break;
        case 'player_edit':
            $vars['view'] = 'edit_player';
            $vars['controller'] = 'admin';
            $id = explode(':', $segments[1]);
            $vars['sid'] = (int) $id[0];
            $id = explode(':', $segments[2]);
            $vars['cid'][0] = (int) $id[0];

        break;
        case 'matchday_edit':
        case 'edit_matchday':
            $vars['view'] = 'edit_matchday';
            if (isset($segments[3]) && $segments[3] == 'admin') {
                $id = explode(':', $segments[1]);
                $vars['sid'] = (int) $id[0];
                $id = explode(':', $segments[2]);
                $vars['cid'][0] = (int) $id[0];
                $vars['controller'] = 'admin';
            } else {
                $vars['controller'] = 'moder';
                $id = explode(':', $segments[2]);
                if (intval($id[0])) {
                    $id = explode(':', $segments[3]);
                    $vars['mid'] = (int) $id[0];

                    $id = explode(':', $segments[1]);

                    $vars['sid'] = (int) $id[0];
                    $id = explode(':', $segments[2]);
                    $vars['tid'] = (int) $id[0];
                } else {
                    $id = explode(':', $segments[1]);
                    $vars['tid'] = (int) $id[0];
                }
            }
        break;
        case 'match_edit':
        case 'edit_match':
            $vars['view'] = 'edit_match';

            if ($segments[3] == 'admin') {
                $id = explode(':', $segments[1]);
                $vars['sid'] = (int) $id[0];
                $id = explode(':', $segments[2]);
                $vars['cid'][0] = (int) $id[0];
                $vars['controller'] = 'admin';
            } else {
                $vars['controller'] = 'moder';
                $id = explode(':', $segments[1]);
                $vars['sid'] = (int) $id[0];
                $id = explode(':', $segments[2]);
                $vars['tid'] = (int) $id[0];
                $id = explode(':', $segments[3]);
                $vars['cid'][0] = (int) $id[0];
            }

        break;

        case 'regplayer':
            $vars['view'] = 'regplayer';
        break;
        case 'regteam':
            $vars['view'] = 'regteam';
        break;
        case 'join_season':
            $vars['view'] = 'join_season';
            $id = explode(':', $segments[1]);
            $vars['sid'] = (int) $id[0];

        break;
        case 'moderedit_umatchday':
            $vars['view'] = 'edit_matchday';
            if (isset($segments[1])) {
                $id = explode(':', $segments[1]);
                $vars['sid'] = (int) $id[0];
            } else {
                $vars['sid'] = 0;
            }
            if (isset($segments[2])) {
                $id = explode(':', $segments[2]);
                $vars['mid'] = (int) $id[0];
            } else {
                $vars['mid'] = 0;
            }
        break;

        case 'moderedit_umatch':
            $vars['view'] = 'edit_match';
            $id = explode(':', $segments[1]);
            $vars['cid'][0] = (int) $id[0];

        break;

        case 'table':
            $vars['view'] = 'table';
                $id = explode(':', $segments[1]);
                $vars['sid'] = (int) $id[0];
            break;
        case 'seasonlist':
            $vars['view'] = 'seasonlist';
            break;
                case 'tournlist':
            $vars['view'] = 'tournlist';
                        $id = explode(':', $segments[1]);
                        $vars['id'] = (int) $id[0];
            break;
        case 'admin_player':
            $vars['view'] = 'admin_player';
            $id = explode(':', $segments[1]);
            if ($segments[2] == 'moder') {
                $vars['tid'] = (int) $id[0];
            } else {
                $vars['sid'] = (int) $id[0];
            }
            $vars['controller'] = $segments[2];

            break;

        case 'adplayer_edit':
            $vars['controller'] = $segments[3];
            if ($vars['controller'] == 'admin') {
                $id = explode(':', $segments[1]);
                $vars['sid'] = (int) $id[0];
                $id = explode(':', $segments[2]);
                $vars['cid'][0] = (int) $id[0];
            } else {
                $id = explode(':', $segments[1]);
                $vars['tid'] = (int) $id[0];
                $id = explode(':', $segments[2]);
                $vars['cid'][0] = (int) $id[0];
            }
            $vars['view'] = 'adplayer_edit';

            break;
        case 'venue':
                $vars['view'] = 'venue';
                $id = explode(':', $segments[1]);
                $vars['id'] = (int) $id[0];
                break;
        case 'club':
                $vars['view'] = 'club';
                $id = explode(':', $segments[1]);
                $vars['id'] = (int) $id[0];
                break;
        case 'jointeam':
                $vars['task'] = 'jointeam';
                $id = explode(':', $segments[1]);
                $vars['sid'] = (int) $id[0];
                $id = explode(':', $segments[2]);
                $vars['tid'] = (int) $id[0];
                break;
        case 'matrix':
            $vars['view'] = 'matrix';

            $id = explode(':', $segments[1]);
            $vars['sid'] = (int) $id[0];
            break;    
        ///betting
        case 'currentbets':
            $vars['view'] = 'currentbets';
            $vars['task'] = 'currentbets';
            break;
        case 'pastbets':
            $vars['view'] = 'pastbets';
            $vars['task'] = 'pastbets';
            break;
        case 'bet_points_request':
            $vars['view'] = 'bet_points_request';
            $vars['task'] = 'bet_points_request';
            break;
        case 'bet_cash_request':
            $vars['view'] = 'bet_cash_request';
            $vars['task'] = 'bet_cash_request';
            break;
        case 'bet_matches':
            $vars['view'] = 'bet_matches';
            $vars['task'] = 'bet_matches';
            break;
        
        default:

                $vars['view'] = 'table';
                $id = explode(':', $segments[1]);
                $vars['sid'] = (int) $id[0];

    }
    //ob_end_clean();
    return $vars;
}
setlocale(LC_ALL, 'en_US.UTF8');
function toAscii($str, $replace=array(), $delimiter='-') {
	if( !empty($replace) ) {
		$str = str_replace((array)$replace, ' ', $str);
	}

	$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
	$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
	$clean = strtolower(trim($clean, '-'));
	$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

	return $clean;
}
