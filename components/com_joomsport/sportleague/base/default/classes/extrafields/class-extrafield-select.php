<?php

class classExtrafieldSelect
{
    public static function getValue($ef)
    {
        global $jsDatabase;
        $query = 'SELECT sel_value FROM '.DB_TBL_EXTRA_SELECT." WHERE id='".(int) $ef->fvalue."'";

        return $jsDatabase->selectValue($query);
    }
}
