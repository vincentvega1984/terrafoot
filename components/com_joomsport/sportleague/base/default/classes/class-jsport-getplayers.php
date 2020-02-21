<?php

require_once JS_PATH_MODELS.'model-jsport-season.php';
class classJsportgetplayers
{
    public static function getPlayersFromTeam($options, $player_id = null)
    {
        global $jsDatabase;

        $result_array = array();
        if ($options) {
            extract($options);
        }

        if (!isset($ordering) || !$ordering || $ordering == ' ') {
            $ordering = 'p.first_name, p.last_name';
        }

        $query = "SELECT COLUMN_NAME
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE table_name = '".DB_TBL_PLAYER_LIST."'
            AND column_name LIKE 'eventid%'";
        $cols = $jsDatabase->select($query);

        $sql_select = '';
        for ($intQ = 0; $intQ < count($cols); ++$intQ) {
            $sql_select .= ',COALESCE(SUM('.$cols[$intQ]->COLUMN_NAME.'),0) as '.$cols[$intQ]->COLUMN_NAME;
        }
        if ((isset($season_id) && $season_id)) {
            $season = new modelJsportSeason($season_id);
            $single = $season->getSingle();
            if ($single == 1) {
                $query = 'SELECT DISTINCT(p.id),p.*,p.id as id'
                    .$sql_select
                    .' FROM '.DB_TBL_PLAYERS.' as p'

                    .' JOIN '.DB_TBL_SEASON_PLAYERS.' as st ON st.player_id = p.id'
                    .' LEFT JOIN '.DB_TBL_PLAYER_LIST.' as pl ON p.id = pl.player_id  AND st.season_id = pl.season_id'
                    .(isset($season_id) && $season_id ? " AND pl.season_id = {$season_id}" : '')
                    .' WHERE 1 = 1'
                    .(isset($player_id) ? ' AND p.id = '.$player_id : '')
                    .(isset($season_id) && $season_id ? ' AND st.season_id = '.$season_id.' '.($season_id ? ' AND pl.season_id='.$season_id : '') : '')

                    .' GROUP BY p.id'
                    .' ORDER BY '.$ordering
                    .(isset($limit) && $limit ? " LIMIT {$limit}" : '')
                    .(isset($limit) && $limit && isset($offset) ? " OFFSET {$offset}" : '');

                $query_count = 'SELECT COUNT((p.id))'
                    //.$sql_select
.' FROM '.DB_TBL_PLAYERS.' as p'
                    .' JOIN '.DB_TBL_SEASON_PLAYERS.' as st ON st.player_id = p.id'
                    .' LEFT JOIN '.DB_TBL_PLAYER_LIST.' as pl ON p.id = pl.player_id  AND st.season_id = pl.season_id'
                    .(isset($season_id) && $season_id ? " AND st.season_id = {$season_id}" : '')
                    .' WHERE 1 = 1'
                    .(isset($player_id) ? ' AND p.id = '.$player_id : '')
                    .(isset($season_id) && $season_id ? ' AND st.season_id = '.$season_id : '');
            } else {
                $query = 'SELECT DISTINCT(p.id),p.*,p.id as id'
                    .$sql_select
                    .' FROM '.DB_TBL_PLAYERS.' as p'
                    .' JOIN '.DB_TBL_PLAYERS_TEAM.' as pt ON pt.player_id = p.id'
                    .' JOIN '.DB_TBL_SEASON_TEAMS.' as st ON pt.team_id = st.team_id'
                    .' LEFT JOIN '.DB_TBL_PLAYER_LIST.' as pl ON p.id = pl.player_id AND pl.team_id = pt.team_id AND st.season_id = pl.season_id'
                    .(isset($season_id) && $season_id ? " AND pl.season_id = {$season_id}" : '')
                    .' WHERE 1 = 1'
                    .(isset($player_id) ? ' AND p.id = '.$player_id : '')
                    .(isset($season_id) && $season_id ? ' AND st.season_id = '.$season_id.' '.($season_id ? ' AND pt.season_id='.$season_id : '') : '')
                    .(isset($team_id) ? ' AND pt.team_id = '.$team_id : '')
                    .' GROUP BY p.id'
                    .' ORDER BY '.$ordering
                    .(isset($limit) && $limit ? " LIMIT {$limit}" : '')
                    .(isset($limit) && $limit && isset($offset) ? " OFFSET {$offset}" : '');

                $query_count = 'SELECT COUNT((p.id))'
                    //.$sql_select
.' FROM '.DB_TBL_PLAYERS.' as p'
                    .' JOIN '.DB_TBL_PLAYERS_TEAM.' as pt ON pt.player_id = p.id'
                    .' JOIN '.DB_TBL_SEASON_TEAMS.' as st ON pt.team_id = st.team_id'
                    .' LEFT JOIN '.DB_TBL_PLAYER_LIST.' as pl ON p.id = pl.player_id AND pl.team_id = pt.team_id AND st.season_id = pl.season_id'
                    .(isset($season_id) && $season_id ? " AND pl.season_id = {$season_id}" : '')
                    .' WHERE 1 = 1'
                    .(isset($player_id) ? ' AND p.id = '.$player_id : '')
                    .(isset($season_id) && $season_id ? ' AND st.season_id = '.$season_id.' '.($season_id ? ' AND pt.season_id='.$season_id : '') : '')
                    .(isset($team_id) ? ' AND pt.team_id = '.$team_id : '');
            }
        } else {
            $query = 'SELECT DISTINCT(p.id),p.*,p.id as id'
                    .$sql_select
                    .' FROM '.DB_TBL_PLAYERS.' as p'

                    .' LEFT JOIN '.DB_TBL_PLAYER_LIST.' as pl ON p.id = pl.player_id'
                    .(isset($season_id) && $season_id ? " AND pl.season_id = {$season_id}" : '')
                    .' WHERE 1 = 1'
                    .(isset($player_id) ? ' AND p.id = '.$player_id : '')
                    .(isset($team_id) ? ' AND pl.team_id = '.$team_id : '')
                    .' GROUP BY p.id'
                    .' ORDER BY '.$ordering
                    .(isset($limit) && $limit ? " LIMIT {$limit}" : '')
                    .(isset($limit) && $limit && isset($offset) ? " OFFSET {$offset}" : '');

            $query_count = 'SELECT COUNT(DISTINCT(p.id))'
                    //.$sql_select
.' FROM '.DB_TBL_PLAYERS.' as p'

                    .' LEFT JOIN '.DB_TBL_PLAYER_LIST.' as pl ON p.id = pl.player_id'
                    .(isset($season_id) && $season_id ? " AND pl.season_id = {$season_id}" : '')
                    .' WHERE 1 = 1'
                    .(isset($player_id) ? ' AND p.id = '.$player_id : '')
                    .(isset($team_id) ? ' AND pl.team_id = '.$team_id : '');
        }

        $players = $jsDatabase->select($query);

        $players_count = $jsDatabase->selectValue($query_count);

        $result_array['list'] = $players;
        $result_array['count'] = $players_count;

        return $result_array;
    }

    public static function getPlayersEvents($season_id = 0)
    {
        global $jsDatabase;
        $query = 'SELECT DISTINCT(ev.id),ev.*'
                .' FROM '.DB_TBL_EVENTS.' as ev,'
                .' '.DB_TBL_MATCH_EVENTS.' as me,'
                .' '.DB_TBL_MATCH.' as m,'
                .' '.DB_TBL_MATCHDAY.' as md'
                .' WHERE (ev.id = me.e_id OR (ev.sumev1 = me.e_id OR ev.sumev2 = me.e_id))'
                .' AND me.match_id = m.id AND m.m_id=md.id '
                .($season_id ? 'AND md.s_id='.$season_id : '')
                .' AND (ev.player_event = 1 OR ev.player_event = 2)'
                .' ORDER BY ev.ordering';
        $events = $jsDatabase->select($query);

        $events_array = array();
        for ($intA = 0; $intA < count($events); ++$intA) {
            $events_array['eventid_'.$events[$intA]->id] = $events[$intA]->e_name;
        }

        return $events_array;
    }
}
