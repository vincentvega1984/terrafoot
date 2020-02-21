<?php

class classExtrafieldRadio
{
    public static function getValue($ef)
    {
        $html = '';
        if ($ef->fvalue) {
            $html = $ef->fvalue ? classJsportLanguage::get('JYES') : classJsportLanguage::get('JNO');
        }

        return $html;
    }
}
