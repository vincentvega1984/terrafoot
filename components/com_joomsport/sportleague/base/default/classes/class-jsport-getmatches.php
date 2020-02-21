<?php

class classJsportgetmatches
{
    public static function getMatches($options, $single = 0)
    {
        $result_array = array();

        if ($options) {
            extract($options);
        }
        global $jsDatabase;
        if (!isset($ordering)) {
            $ordering = 'm.m_date, m.m_time, md.ordering';
        }

        $season_sql = '';
        $season_sql_where = '';
        if (isset($season_id) && $season_id == 0) {
            $season_sql = ', '.DB_TBL_SEASONS.' as s,'
                    .' '.DB_TBL_TOURNAMENT.' as tr ';
            $season_sql_where = " AND ((tr.id=s.t_id AND tr.t_single = '".$single."' AND md.s_id=s.s_id ) OR (md.s_id = -1 AND m.m_single='".$single."'))";
        }

        $query = 'SELECT m.*,md.m_name '
                .'FROM '.DB_TBL_MATCHDAY.' as md'
                .' JOIN '.DB_TBL_MATCH.' as m ON md.id = m.m_id'
                .$season_sql
                .' WHERE 1=1'
                .$season_sql_where
                .(isset($season_id) && $season_id ? ' AND md.s_id = '.$season_id : '')
                .(isset($matchday_id) ? ' AND md.id = '.$matchday_id : '')
                .(isset($team_id) ? (isset($place) ? ($place == 1 ? ' AND m.team1_id = '.$team_id : ' AND m.team2_id = '.$team_id) : ' AND (m.team1_id = '.$team_id.' OR m.team2_id = '.$team_id.')') : '')
                .(isset($played) ? " AND m.m_played = '".$played."'" : '')
                .(isset($date_from) ? " AND m.m_date >= '".$date_from."'" : '')
                .(isset($date_to) ? " AND m.m_date <= '".$date_to."'" : '')
                .' AND (m.team1_id != 0 OR m.team2_id != 0)'
                .' ORDER BY '.$ordering
                .(isset($limit) && $limit ? " LIMIT {$limit}" : '')
                .(isset($limit) && $limit && isset($offset) ? " OFFSET {$offset}" : '');
        $matches = $jsDatabase->select($query);

        $query = 'SELECT COUNT(m.id)'
                .'FROM '.DB_TBL_MATCHDAY.' as md'
                .' JOIN '.DB_TBL_MATCH.' as m ON md.id = m.m_id'
                .$season_sql
                .' WHERE 1=1'
                .$season_sql_where
                .(isset($season_id) && $season_id ? ' AND md.s_id = '.$season_id : '')
                .(isset($matchday_id) ? ' AND md.id = '.$matchday_id : '')
                .(isset($team_id) ? ' AND (m.team1_id = '.$team_id.' OR m.team2_id = '.$team_id.')' : '')
                .(isset($played) ? " AND m.m_played = '".$played."'" : '')
                .(isset($date_from) ? " AND m.m_date >= '".$date_from."'" : '')
                .(isset($date_to) ? " AND m.m_date <= '".$date_to."'" : '')
                .' AND (m.team1_id != 0 OR m.team2_id != 0)';
        $matches_count = $jsDatabase->selectValue($query);

        $result_array['list'] = $matches;
        $result_array['count'] = $matches_count;

        return $result_array;
    }
}
