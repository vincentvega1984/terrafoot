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

require_once __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'base.php';

function date_bl($date, $time)
{
    return JSBaseView::formatDate($date.' '.$time);

    $format = 'd-m-Y H:i';
    if ($date == '' || $date == '0000-00-00') {
        return '';
    }

    // $format = getJS_Config('date_format');
// print_r($format);echo "<hr>";
    /*switch ($format){
        case "d-m-Y H:i": $format = "%d-%m-%Y %H:%M"; break;
        case "m-d-Y g:i A": $format = "%m-%d-%Y %I:%M %p"; break;
        case "j F, Y H:i": $format = "%m %B, %Y %H:%M"; break;
        case "j F, Y g:i A": $format = "%m %B, %Y %I:%H %p"; break;
        case "d-m-Y": $format = "%d-%m-%Y"; break;
        case "l d F, Y H:i": $format = "%A %d %B, %Y  %H:%M"; break;
    }*/

    if (!$time) {
        $time = '00:00';
    }
    $time_m = explode(':', $time);
    $date_m = explode('-', $date);

    if (function_exists('date_default_timezone_set')) {
        date_default_timezone_set('GMT');
    }
    $tm = @mktime($time_m[0], $time_m[1], '0', $date_m[1], $date_m[2], $date_m[0]);
    jimport('joomla.utilities.date');
    $dt = new JDate($tm, null);

    return $dt->format($format);
}

function getVer()
{
    $version = new JVersion();
    $joomla = $version->getShortVersion();

    return substr($joomla, 0, 3);
}
function getImgPop($img, $thumb_type = 0, $height = 0, $width = 0)
{
    $link = ($thumb_type != 6) ? ('media/bearleague/'.$img) : ('media/bearleague/events/'.$img);

    $fileDetails = ($thumb_type != 6) ? pathinfo(JURI::base().'/media/bearleague/'.$img) : (pathinfo(JURI::base().'/media/bearleague/events/'.$img));
    $img_types = array('png', 'gif', 'jpg', 'jpeg');
    $ext = strtolower($fileDetails['extension']);
    $adit_par = '';

    if (is_file(JPATH_ROOT.'/media/bearleague/'.$img) || is_file(JPATH_ROOT.'/media/bearleague/events/'.$img)) {
        $size = ($thumb_type != 6) ? getimagesize(JPATH_ROOT.'/media/bearleague/'.$img) : getimagesize(JPATH_ROOT.'/media/bearleague/events/'.$img);
        switch ($thumb_type) {
            case '1': //player list, team list
                        $max_height = 29;
                        $max_width = 29;

                    break;
            case '2': //photo gallery
                        $max_height = 100;
                        //hack width
                        $max_width = 1000;
                        $size[0] = 1;

                    break;
            case '3': //main photo team, player
                        //hack height
                        $max_height = 100;
                        $size[1] = 1;
                        $max_width = 198;
                    break;
            case '4': //tournament logo
                        //hack height
                        $max_height = 100;
                        $size[1] = 1;
                        $max_width = 100;
                    break;
            case '5': //player list, team list
                        $max_height = $height;
                        $max_width = $width;

                    break;
            case '6': //player list, team list
                        $max_height = 32;
                        $max_width = 32;

                    break;
            default:
                        $max_height = 500;
                        $max_width = 600;
                        if (in_array(strtolower($ext), $img_types)) {
                            if ($size[0] > $max_width && $size[0] >= $size[1]) {
                                //$link = JURI::base().'index.php?option=com_joomsport&task=imgres&src='.$link.'&w='.$max_width;
                            } elseif ($size[1] > $max_height && $size[1] >= $size[0]) {
                                //$link = JURI::base().'index.php?option=com_joomsport&task=imgres&src='.$link.'&h='.$max_height;
                            }
                        }

                        return $link;

        }
        if (in_array(strtolower($ext), $img_types)) {
            if ($size[0] > $max_width && $size[0] >= $size[1]) {
                $adit_par = ' width="'.$max_width.'" ';
                                //$link = JURI::base().'index.php?option=com_joomsport&task=imgres&src='.$link.'&w='.$max_width;
            } elseif ($size[1] > $max_height && $size[1] >= $size[0]) {
                //$link = JURI::base().'index.php?option=com_joomsport&task=imgres&src='.$link.'&h='.$max_height;
                            $adit_par = ' width="'.$max_width.'" ';
            }
        } else {
            if ($size[0] >= $size[1]) {
                $adit_par = ' width="'.$max_width.'" ';
            } else {
                $adit_par = ' height="'.$max_height.'" ';
            }
        }
    }
    $res = ' src="'.$link.'" '.$adit_par;

    return $res;
}

function getJS_Location($id)
{
    $db = JFactory::getDBO();
    $Itemid = JRequest::getInt('Itemid');
    $query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='unbl_venue'";
    $db->setQuery($query);
    $unbl_venue = $db->loadResult();

    $query = 'SELECT m_location FROM #__bl_match WHERE id='.$id;
    $db->setQuery($query);
    $loc = $db->loadResult();
    if ($unbl_venue) {
        $query = 'SELECT v.* FROM #__bl_match as m LEFT JOIN #__bl_venue as v ON m.venue_id=v.id WHERE m.id='.$id;
        $db->setQuery($query);
        $ven = $db->loadObject();
        if ($ven->v_name) {
            $link = JRoute::_('index.php?option=com_joomsport&task=venue&id='.$ven->id.'&Itemid='.$Itemid);
            $loc = '<a href="'.$link.'" title="'.$ven->v_name.'">'.$ven->v_name.'</a>';
        }
    }

    return $loc;
}

function getJS_Config($val)
{
    $db = JFactory::getDBO();
    $Itemid = JRequest::getInt('Itemid');
    $query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='".$val."'";
    $db->setQuery($query);

    return $db->loadResult();
}
function getBettingMenu($Itemid)
{
    $menu = '<div class="betmenu">
                <div>
                    <a href="'.JRoute::_('index.php?option=com_joomsport&view=bet_cash_request&Itemid='.$Itemid).'">'.
                        JText::_('BLFA_BET_REQUEST_CASH').'
                    </a>
                </div>
                <div>
                    <a href="'.JRoute::_('index.php?option=com_joomsport&view=bet_points_request&Itemid='.$Itemid).'">'.
                        JText::_('BLFA_BET_REQUEST_POINTS').'
                    </a>
                </div>
                <div>
                    <a href="'.JRoute::_('index.php?option=com_joomsport&view=currentbets&Itemid='.$Itemid).'">'.
                        JText::_('BLFA_BET_CURRENT_BETS').'
                    </a>
                </div>
                <div>
                    <a href="'.JRoute::_('index.php?option=com_joomsport&view=pastbets&Itemid='.$Itemid).'">'.
                        JText::_('BLFA_BET_PAST_BETS').'
                    </a>
                </div>
                <div>
                    <a href="'.JRoute::_('index.php?option=com_joomsport&view=bet_matches&Itemid='.$Itemid).'">'.
                        JText::_('BLFA_BET_MATCHES').'
                    </a>
                </div>
            </div>';

    return $menu;
}
function getUserInfo($model, $Itemid)
{
    $mainmodel = new JSPRO_Models();
    $data = $model->getData();
    $user = JFactory::getUser();
    if ($data) {
        $points = $data['points'];
        $currentBets = count($data['currentbets']);
        $pastBets = count($data['pastbets']);
        $wonBets = count($data['wonbets']);
    } else {
        $points = $mainmodel->getUserPoints($user->get('id'));
        $currentBets = count($model->getCurrentBets());
        $pastBets = count($model->getPastBets());
        $wonBets = count($model->getWonBets());
    }

    return '
        <span>'.$user->get('username').'</span><br/>
        <span style="margin-right:10px">'.JText::_('BLFA_BET_POINTS').'</span><span>'.$points.'</span><br/>
        <span style="margin-right:10px">'.JText::_('BLFA_BET_CURRENTBETS').'</span><span>'.$currentBets.'</span><br/>
        <span style="margin-right:10px">'.JText::_('BLFA_BET_WINBETS').'</span><span>'.$wonBets.'</span><br/>
        <span style="margin-right:10px">'.JText::_('BLFA_BET_PASTBETS').'</span><span>'.$pastBets.'</span><br/>
    ';
}
