<?php

class modelJsportPlayer
{
    public $season_id = null;
    public $player_id = null;
    public $lists = null;
    private $row = null;

    public function __construct($id, $season_id = 0)
    {
        $this->season_id = $season_id;
        $this->player_id = $id;

        if (!$this->player_id) {
            die('ERROR! Player ID not DEFINED');
        }
        $this->loadObject();
    }
    private function loadObject()
    {
        global $jsDatabase;
        $this->row = $jsDatabase->selectObject('SELECT * '
                .'FROM '.DB_TBL_PLAYERS.''
                .' WHERE id = '.$this->player_id);
    }
    public function getRow()
    {
        //$this->loadLists();
        return $this->row;
    }
    public function loadLists()
    {
        $this->lists['ef'] = classJsportExtrafields::getExtraFieldList($this->player_id, '0', $this->season_id);
        $this->getPhotos();
        $this->getDefaultImage();
        $this->getHeaderSelect();

        return $this->lists;
    }

    public function getDefaultImage()
    {
        global $jsDatabase;
        $this->lists['def_img'] = null;
        if ($this->row->def_img) {
            $query = 'SELECT ph_filename FROM  '.DB_TBL_PHOTOS.' as p WHERE p.id = '.$this->row->def_img;

            $this->lists['def_img'] = $jsDatabase->selectValue($query);
        } elseif (isset($this->lists['photos'][0])) {
            $this->lists['def_img'] = $this->lists['photos'][0]->filename;
        }
    }
    public function getPhotos()
    {
        global $jsDatabase;
        $query = 'SELECT p.ph_name as name,p.id as id,p.ph_filename as filename'
                .' FROM '.DB_TBL_ASSIGN_PHOTOS.' as ap, '.DB_TBL_PHOTOS.' as p'
                .' WHERE ap.photo_id = p.id AND cat_type = 1 AND cat_id = '.$this->player_id;

        $this->lists['photos'] = $jsDatabase->select($query);
    }

    public function getHeaderSelect()
    {
        global $jsDatabase;
        /*$query = "SELECT s.s_id as id,s.s_name as s_name, t.id as tourn_id, t.name"
                . " FROM ".DB_TBL_SEASONS." as s"
                . " JOIN ".DB_TBL_TOURNAMENT." as t ON t.id = s.t_id"
                . " JOIN ".DB_TBL_SEASON_TEAMS." as st ON s.s_id=st.season_id"
                . " WHERE s.published='1' AND t.published='1' AND st.team_id=" . $this->team_id . ""
                . " ORDER BY t.name, t.id, s.s_name";
        $this->lists["header"]["season"] = $jsDatabase->select($query);*/

        $query = 'SELECT * FROM '.DB_TBL_TOURNAMENT." WHERE published = '1' ORDER BY name";

        $tourn = $jsDatabase->select($query);

        $javascript = " onchange='fSubmitwTab(this);'";

        $jqre = '<select class="selectpicker" name="sid" id="sid"  size="1" '.$javascript.'>';
        $jqre .= '<option value="0">'.classJsportLanguage::get('BLFA_ALL').'</option>';

        $query = 'SELECT COUNT(*) FROM '.DB_TBL_MATCHDAY.' as md,'
                .' '.DB_TBL_MATCH.' as m WHERE m.m_id=md.id AND md.s_id= -1'
                ." AND m.m_single='1' "
                .' AND (m.team1_id='.$this->player_id.' OR m.team2_id='.$this->player_id.')';

        $frm = $jsDatabase->selectValue($query);

        $query = 'SELECT COUNT(*) FROM '.DB_TBL_MATCHDAY.' as md,'
                .' '.DB_TBL_MATCH.' as m,'
                .' '.DB_TBL_SQUARD.' as sc'
                .' WHERE sc.match_id=m.id AND sc.player_id='.$this->player_id.''
                .' AND m.m_id=md.id AND md.s_id= -1 ';
        $frm2 = $jsDatabase->selectValue($query);

        if ($frm && $frm2) {
            $jqre .= '<option value="-1" '.((-1 == $this->season_id) ? 'selected' : '').'>'.classJsportLanguage::get('BLFA_FRIENDLY_MATCHES').'</option>';
        }
        $this->_lists['tour_name_s'] = array();
        /////////!!!!!
        $ind_s = 0;
        for ($i = 0; $i < count($tourn); ++$i) {
            $is_tourn2 = array();
            $tsingl = $tourn[$i]->t_single;
            //print_r($tsingl);
            if ($tsingl) {
                $query = 'SELECT s.s_id as id,s.s_name as s_name'
                        .' FROM '.DB_TBL_SEASON_PLAYERS.' as sp,'
                        .' '.DB_TBL_SEASONS.' as s'
                        ." WHERE s.published = '1' AND s.t_id=".$tourn[$i]->id.''
                        .' AND s.s_id=sp.season_id AND sp.player_id='.$this->player_id;
            } else {
                $query = 'SELECT DISTINCT(s.s_id) as id,s.s_name as s_name'
                        .' FROM '.DB_TBL_SEASONS.' as s,'
                        .' '.DB_TBL_SEASON_TEAMS.' st,'
                        .' '.DB_TBL_PLAYERS_TEAM.' as pt'
                        ." WHERE pt.confirmed='0' AND s.published = '1'"
                        .' AND s.t_id='.$tourn[$i]->id.' AND st.season_id=s.s_id'
                        .' AND st.team_id=pt.team_id'
                        .' AND pt.season_id=s.s_id AND pt.player_id='.$this->player_id
                        .'  ORDER BY s.s_name';
            }

            $rows = $jsDatabase->select($query);

            if (count($rows)) {
                $ind_s = 1;
                $jqre .= '<optgroup label="'.htmlspecialchars($tourn[$i]->name).'">'; ///this
                array_push($this->_lists['tour_name_s'], $tourn[$i]->name);
                for ($g = 0; $g < count($rows); ++$g) {
                    $jqre .= '<option value="'.$rows[$g]->id.'" '.(($rows[$g]->id == $this->season_id) ? 'selected' : '').'>'.$rows[$g]->s_name.'</option>';
                    $seasplayed[] = $rows[$g]->id;
                }
                $jqre .= '</optgroup>';
            }
        }
        $jqre .= '</select>';

        $this->lists['tourn'] = $jqre;
    }
}
