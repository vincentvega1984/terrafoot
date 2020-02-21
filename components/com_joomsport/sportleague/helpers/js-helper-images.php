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

require_once JS_PATH_HELPERS.'easyphpthumbnail.class.php';

class jsHelperImages
{
    public static function getEmblem($img, $type = 0, $class = '', $width = 0)
    {
        global $jsConfig;

        if ($width === 0) {
            $width = $jsConfig->get('teamlogo_height');
        }

        $defimg = $type ? JSCONF_TEAM_DEFAULT_IMG : JSCONF_PLAYER_DEFAULT_IMG;

        $html = '';
        $resize_to = 150;

        if ($width && $width < 40) {
            $class .= ' emblpadd3';
        }

        if (!is_file(JS_PATH_IMAGES_THUMB.$img) && is_file(JS_PATH_IMAGES.$img)) {
            $thumb = new easyphpthumbnail();
            $thumb->Thumblocation = JS_PATH_IMAGES_THUMB;
            //$thumb -> Thumbprefix = $resize_to . '_';
            $thumb->Thumbwidth = $resize_to;
            $thumb->Quality = 100;
            $thumb->Square = true;
            $thumb->Createthumb(JS_PATH_IMAGES.$img, 'file');
        }
        if (is_file(JS_PATH_IMAGES_THUMB.$img)) {
            $html = '<img alt="" class="img-thumbnail img-responsive '.$class.'" src="'.JS_LIVE_URL_IMAGES_THUMB.$img.'" '.($width?'width="'.$width.'"':"").' />';
        } elseif (is_file(JS_PATH_IMAGES.$img)) {
            $html = '<img alt="" class="img-thumbnail img-responsive '.$class.'" src="'.JS_LIVE_URL_IMAGES.$img.'" '.($width?'width="'.$width.'"':"").' />';
        } else {
            $html = '<img alt="" class="img-thumbnail img-responsive '.$class.'" src="'.JS_LIVE_URL_IMAGES.$defimg.'" width="'.$width.'" />';
        }

        return $html;
    }
    public static function getEmblemBig($img, $type = 1, $class = 'emblInline', $width = '0', $light = true)
    {
        global $jsConfig;
        $add_styles = '';
        if (!$width) {
            $width = $jsConfig->get('set_defimgwidth');
            $add_styles = 'style="width:'.$width.'px;max-width:'.$width.'px;"';
        }
        $html = '';
        $resize_to = 300;
        if (!is_file(JS_PATH_IMAGES_THUMB.$img) && is_file(JS_PATH_IMAGES.$img)) {
            $thumb = new easyphpthumbnail();
            $thumb->Thumblocation = JS_PATH_IMAGES_THUMB;
            //$thumb -> Thumbprefix = $resize_to . '_';
            $thumb->Thumbwidth = $resize_to;
            $thumb->Quality = 100;
            $thumb->Createthumb(JS_PATH_IMAGES.$img, 'file');
        }
        if (is_file(JS_PATH_IMAGES_THUMB.$img)) {
            if ($light) {
                $html = '<a class="jsLightLink" href="'.JS_LIVE_URL_IMAGES.$img.'" data-lightbox="jsteam'.$type.'">';
            }
            $html .= '<img alt="" class="img-thumbnail img-responsive"  src="'.JS_LIVE_URL_IMAGES.$img.'" width="'.$width.'" '.$add_styles.' />';
            if ($light) {
                $html .= '</a>';
            }
        } elseif (is_file(JS_PATH_IMAGES.$img)) {
            if ($light) {
                $html = '<a class="jsLightLink" href="'.JS_LIVE_URL_IMAGES.$img.'" data-lightbox="jsteam'.$type.'">';
            }
            $html .= '<img alt="" class="img-thumbnail img-responsive" src="'.JS_LIVE_URL_IMAGES.$img.'" width="'.$width.'"  '.$add_styles.' />';
            if ($light) {
                $html .= '</a>';
            }
        } else {
            if($img == JSCONF_VENUE_DEFAULT_IMG){
                $html = '<img alt="" class="img-thumbnail img-responsive" src="'.JS_LIVE_URL_IMAGES.JSCONF_VENUE_DEFAULT_IMG.'" width="'.$width.'"  '.$add_styles.' />';
            }else{
                $html = '<img alt="" class="img-thumbnail img-responsive" src="'.JS_LIVE_URL_IMAGES.JSCONF_PLAYER_DEFAULT_IMG.'" width="'.$width.'"  '.$add_styles.' />';
            }
            
        }

        return $html;
    }
    public static function getEmblemEvents($img, $type = 0, $class = '', $width = 24, $title='')
    {
        $html = '';
        $resize_to = 40;
        if ($width < 40) {
            $class .= ' emblpadd3';
        }
        if (!is_file(JS_PATH_IMAGES_THUMB.$img) && is_file(JS_PATH_IMAGES_EVENTS.$img)) {
            $thumb = new easyphpthumbnail();
            $thumb->Thumblocation = JS_PATH_IMAGES_THUMB;
            //$thumb -> Thumbprefix = $resize_to . '_';
            $thumb->Thumbwidth = $resize_to;
            $thumb->Quality = 100;
            $thumb->Createthumb(JS_PATH_IMAGES_EVENTS.$img, 'file');
        }
        if (is_file(JS_PATH_IMAGES_THUMB.$img)) {
            //$html = '<a href="'.JS_LIVE_URL_IMAGES_EVENTS . $img.'" data-lightbox="jsteam'.$type.'">';
            $html .= '<img alt="" class="img-responsive '.$class.'"  src="'.JS_LIVE_URL_IMAGES_THUMB.$img.'" width="'.$width.'" title="'.$title.'" alt="'.$title.'" />';
            //$html .= '</a>';
        } elseif (is_file(JS_PATH_IMAGES.$img)) {
            //$html = '<a href="'.JS_LIVE_URL_IMAGES_EVENTS . $img.'" data-lightbox="jsteam'.$type.'">';
            $html .= '<img alt="" class="img-responsive '.$class.'" src="'.JS_LIVE_URL_IMAGES_EVENTS.$img.'" width="'.$width.'" title="'.$title.'" alt="'.$title.'" />';
            //$html .= '</a>';
        }

        return $html;
    }
}
