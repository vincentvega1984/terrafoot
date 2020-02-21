<?php

foreach (glob(__DIR__.DIRECTORY_SEPARATOR.'extrafields/*.php') as $filename) {
    include $filename;
}
// type 0 - player, 1 - team, 2 - match, 3 - season, 4 - club
class classJsportEf
{
    public $type = null;

    public function __construct($type)
    {
        $this->type = $type;
    }
    public function getValue($id, $field_id, $season_id = 0)
    {
        global $jsDatabase;
        $value = null;
        $query = 'SELECT DISTINCT(ef.id),ef.*,'
                .'ev.fvalue as fvalue,ev.fvalue_text'
                .' FROM '.DB_TBL_EXTRA_FILDS.' as ef'
                .'  JOIN '.DB_TBL_EXTRA_VALUES.' as ev'
                .' ON ef.id=ev.f_id'
                .' AND ef.id = '.$field_id
                .' AND ev.uid='.($id ? intval($id) : -1).''
                .' AND ((ev.season_id='.($season_id > 0 ? $season_id : -100)." AND ef.season_related = '1') OR (ev.season_id=0 AND ef.season_related = '0'))"
                ." WHERE ef.published=1 AND ef.type='".$this->type."' ".(classJsportUser::getUserId() ? '' : " AND ef.faccess='0'").'';
        $efObj = $jsDatabase->selectObject($query);
        // field type 0-text,1-radio,2-editor,3-select,4-link
        if (!empty($efObj)) {
            switch ($efObj->field_type) {
                case 0:
                       $value = classExtrafieldText::getValue($efObj);

                    break;
                case 1:
                       $value = classExtrafieldRadio::getValue($efObj);

                    break;
                case 2:
                       $value = classExtrafieldEditor::getValue($efObj);

                    break;
                case 3:
                       $value = classExtrafieldSelect::getValue($efObj);

                    break;
                case 4:
                       $value = classExtrafieldLink::getValue($efObj);

                    break;
                default:
                    $value = null;
                    break;
            }
        }

        return $value;
    }
    public function getList($id, $season_id)
    {
        global $jsDatabase;
        $return = array();
        $query = 'SELECT DISTINCT(ef.id),ef.*,'
                .'ev.fvalue as fvalue,ev.fvalue_text'
                .' FROM '.DB_TBL_EXTRA_FILDS.' as ef'
                .' LEFT JOIN '.DB_TBL_EXTRA_VALUES.' as ev'
                .' ON ef.id=ev.f_id'
                .' AND ev.uid='.($id ? intval($id) : -1).''
                .' AND ((ev.season_id='.($season_id > 0 ? $season_id : -100)." AND ef.season_related = '1') OR (ev.season_id=0 AND ef.season_related = '0'))"
                ." WHERE ef.published=1 AND ef.type='".$this->type."' ".(classJsportUser::getUserId() ? '' : " AND ef.faccess='0'").''
                .' ORDER BY ef.ordering';

        $ef = $jsDatabase->select($query);

        for ($intA = 0; $intA < count($ef); ++$intA) {
            $return[$ef[$intA]->name] = self::getValue($id, $ef[$intA]->id, $season_id);
        }

        return $return;
    }

    public function getListTable()
    {
        global $jsDatabase;

        $query = 'SELECT ef.name, ef.id'
                .' FROM '.DB_TBL_EXTRA_FILDS.' as ef '
                ." WHERE ef.published=1 AND ef.type = '".$this->type."'"
                ." AND ef.e_table_view = '1' AND ef.fdisplay = '1'"
                .' ORDER BY ef.ordering';

        $ef = $jsDatabase->select($query);

        return $ef;
    }
}
