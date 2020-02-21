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

class jsHelper
{
    public static function getADF($ef, $suff = '')
    {
        $return = '';
        if (count($ef)) {
            foreach ($ef as $key => $value) {
                if ($value != null) {
                    $return .=  '<div class="jstable-row">';
                    $return .=  '<div class="jstable-cell"><strong>'.$key.':</strong></div>';
                    $return .=  '<div class="jstable-cell">'.$value.'</div>';
                    $return .=  '</div>';
                }
            }
        }
        if ($return) {
            $return = '<div class="jstable">'.$return.'</div>';
        }
        //$return .= '</div>';
        return $return;
    }

    public static function getMatches($matches, $pagination = null, $mdname = true)
    {
        $html = '';
        global $jsConfig;
        if (count($matches)) {
            $html .= '<div class="table-responsive">';
            if (self::isMobile()) {
                $html .= '<div class="jstable jsMatchDivMainMobile">';
            } else {
                $html .= '<div class="jstable jsMatchDivMain">';
            }

            $md_id = 0;
            for ($intA = 0; $intA < count($matches); ++$intA) {
                $match = $matches[$intA];

                if (JSCONF_ENBL_MATCH_TOOLTIP && isset($match->lists['m_events_home']) && (count($match->lists['m_events_home']) || count($match->lists['m_events_away']))) {
                    $tooltip = '<div style="overflow:hidden;" class="tooltipInnerHtml"><div class="jstable jsInline" '.(count($match->lists['m_events_home']) >= count($match->lists['m_events_away']) ? 'style="border-right:1px solid #ccc;"' : '').'>';

                    for ($intP = 0; $intP < count($match->lists['m_events_home']); ++$intP) {
                        $tooltip .= '<div class="jstable-row">
                                <div class="jstable-cell">
                                    <div style="min-height:35px;vertical-align:middle;margin-top:12px;min-width:30px;">'.$match->lists['m_events_home'][$intP]->objEvent->getEmblem().'</div>
                                </div>
                                <div class="jstable-cell">
                                    '.$match->lists['m_events_home'][$intP]->obj->getName().'
                                </div>
                                <div class="jstable-cell">
                                    '.$match->lists['m_events_home'][$intP]->ecount.'
                                </div>
                                <div class="jstable-cell">
                                    '.($match->lists['m_events_home'][$intP]->minutes ? $match->lists['m_events_home'][$intP]->minutes."'" : '').'
                                </div>
                            </div>';
                    }
                    if (!count($match->lists['m_events_home'])) {
                        $tooltip .= '&nbsp';
                    }

                    $tooltip .= '</div>';
                    $tooltip .= '<div class="jstable jsInline" '.(count($match->lists['m_events_home']) < count($match->lists['m_events_away']) ? 'style="border-right:1px solid #ccc;"' : '').'>';

                    for ($intP = 0; $intP < count($match->lists['m_events_away']); ++$intP) {
                        $tooltip .= '<div class="jstable-row">
                                <div class="jstable-cell">
                                    <div style="min-height:35px;vertical-align:middle;margin-top:12px;min-width:30px;">'.$match->lists['m_events_away'][$intP]->objEvent->getEmblem().'</div>
                                </div>
                                <div class="jstable-cell">
                                    '.$match->lists['m_events_away'][$intP]->obj->getName().'
                                </div>
                                <div class="jstable-cell">
                                    '.$match->lists['m_events_away'][$intP]->ecount.'
                                </div>
                                <div class="jstable-cell">
                                    '.($match->lists['m_events_away'][$intP]->minutes ? $match->lists['m_events_away'][$intP]->minutes."'" : '').'
                                </div>
                            </div>';
                    }

                    $tooltip .= '</div>';
                } else {
                    $tooltip = '';
                }
                $partic_home = $match->getParticipantHome();
                $partic_away = $match->getParticipantAway();
                $match_date = classJsportDate::getDate($match->object->m_date, $match->object->m_time);

                $home_class = 'jscal_notplayed';
                $away_class = 'jscal_notplayed';
                if ($match->object->m_played == '1') {
                    if($match->object->score1 > $match->object->score2){
                        $home_class = 'jscal_winner';
                        $away_class = 'jscal_looser';
                    }elseif($match->object->score1 < $match->object->score2){
                        $away_class = 'jscal_winner';
                        $home_class = 'jscal_looser';
                    }else{
                        $away_class = 'jscal_draw';
                        $home_class = 'jscal_draw';
                    }
                }
                
                if (self::isMobile()) {
                    $html .= '<div class="jsMobileMatchCont">';
                    if ($jsConfig->get('enbl_mdnameoncalendar') == '1' && $mdname) {
                        $html .= '<div class="jsDivMobileMdayName">';
                        $html .= $match->object->m_name.'</div>';
                    }
                    $html .= '<div class="matchDate">'.$match_date;
                    if ($jsConfig->get('cal_venue')) {
                        $html .= '<div class="jsMatchDivVenue">'
                                    .$match->getLocation()
                                .'</div>';
                    }
                    $html .= '</div>';
                    $html .= '<div class="jsDivCenter">'
                                .'<div class="jsDivLineEmbl">'

                                .self::nameHTML($partic_home->getName(true))
                            .'</div></div>';
                    $html .= '<div class="jsMatchDivScore">'
                                .($partic_home->getEmblem()).
                                    '<div class="jsScoreBonusB">'.self::getScore($match, '').'</div>'
                                .($partic_away->getEmblem()).
                                '</div>';
                    $html .= '<div  class="jsDivCenter">'
                                .'<div class="jsDivLineEmbl">'

                                .self::nameHTML($partic_away->getName(true))
                            .'</div></div>';

                    $html .= '</div>';
                } else {
                    if ($md_id != $match->object->m_id && $jsConfig->get('enbl_mdnameoncalendar') == '1' && $mdname) {
                        $html .= '<div class="jstable-row js-mdname"><div class="jsrow-matchday-name">'.$match->object->m_name.'</div></div>';
                        $md_id = $match->object->m_id;
                    }
                    $html .= '<div class="jstable-row">
                            <div class="jstable-cell jsMatchDivTime">
                                <div class="jsDivLineEmbl">'

                                    .$match_date
                                .'</div>'
                            .'</div>'
                            .'<div class="jstable-cell jsMatchDivHome '.$home_class.'">
                                <div class="jsDivLineEmbl">'

                                    .self::nameHTML($partic_home->getName(true))
                                .'</div>'
                            .'</div>'
                            .'<div class="jstable-cell jsMatchDivHomeEmbl '.$home_class.'">'
                                .'<div class="jsDivLineEmbl" style="float:right;">'
                                    .($partic_home->getEmblem())

                                .'</div>

                            </div>
                            <div class="jstable-cell jsMatchDivScore">
                                '.self::getScore($match, '', $tooltip).'
                            </div>
                            <div class="jstable-cell jsMatchDivAwayEmbl '.$away_class.'">
                                <div class="jsDivLineEmbl">'

                                        .($partic_away->getEmblem())
                                .'</div>'
                            .'</div>'
                            .'<div class="jstable-cell jsMatchDivAway '.$away_class.'">'
                                .'<div class="jsDivLineEmbl">'

                                        .self::nameHTML($partic_away->getName(true), 0).'

                                </div>    
                            </div>';
                    if ($jsConfig->get('cal_venue')) {
                        $html .= '<div class="jstable-cell jsMatchDivVenue">'
                                        .$match->getLocation()
                                    .'</div>';
                    }
                    $html .= '</div>';
                }
            }

            $tooltip .= '</div></div>';
            $html .= '</div></div>';
            if ($pagination) {
                require_once JS_PATH_VIEWS.'elements'.DIRECTORY_SEPARATOR.'pagination.php';
                $html .= paginationView($pagination);
            }

            return $html;
        }
    }
    public static function getScore($match, $class = '', $tooltip = '', $itemid = 0)
    {
        $html = '';

        if ($match->object->m_played == '1') {
            $text = $match->object->score1.JSCONF_SCORE_SEPARATOR.$match->object->score2;
            $html .= classJsportLink::match($text, $match->object->id, false, '', $itemid);
        } elseif ($match->object->m_played == '0') {
            $html .= classJsportLink::match(JSCONF_SCORE_SEPARATOR_VS, $match->object->id, false, '', $itemid);
        } else {
            if ($match->lists['mStatuses'] && isset($match->lists['mStatuses']->id)) {
                $tooltip = $match->lists['mStatuses']->stName;
                $html .= classJsportLink::match($match->lists['mStatuses']->stShort, $match->object->id, false, '', $itemid);
            } else {
                $html .= JSCONF_SCORE_SEPARATOR_VS;
            }
        }
        //$tooltip = '<table><tr><td style="width:200px;background-color:blue; vertical-align:top;"><div>Player 1 goal 55min</div><div>Player 1 goal 55min</div><div>Player 1 goal 55min</div></td><td style="background-color:red;vertical-align:top; width:50%;"><div>Player 1 goal 55min</div></td></tr></table>';
        return '<div data-html="true" class="jsScoreDiv '.$class.'" data-toggle2="tooltipJSF" data-placement="bottom" title="" data-original-title="'.htmlspecialchars(($tooltip)).'">'.$html.$match->getETLabel().'</div>'.$match->getBonusLabel();
    }
    public static function getScoreBigM($match)
    {
        $html = '';

        if ($match->object->m_played == '1') {
            $bonus1 = '';
            $bonus2 = '';
            $sep = JSCONF_SCORE_SEPARATOR;
            if ($match->object->bonus1 != '0.00' || $match->object->bonus2 != '0.00') {
                $bonus1 = '<div class="jsHmBonus">'.floatval($match->object->bonus1).'</div>';
                $bonus2 = '<div class="jsAwBonus">'.floatval($match->object->bonus2).'</div>';
            }
            $html .= "<div class='BigMScore1'>".$match->object->score1.$bonus1.'</div>';
            $html .= "<div class='BigMScore2'>".$match->object->score2.$bonus2.'</div>';
        } elseif ($match->object->m_played == '0') {
            $sep = JSCONF_SCORE_SEPARATOR_VS;
        } else {
            if ($match->lists['mStatuses'] && isset($match->lists['mStatuses']->id)) {
                $tooltip = $match->lists['mStatuses']->stName;
                $sep = $match->lists['mStatuses']->stShort;
                $html .= "<div class='BigMScoreCS'>".$tooltip.'</div>';
            } else {
                $sep = JSCONF_SCORE_SEPARATOR_VS;
            }
        }

        //$html .= '<div class="matchSeparator">'.$sep.'</div>';

        //$tooltip = '<table><tr><td style="width:200px;background-color:blue; vertical-align:top;"><div>Player 1 goal 55min</div><div>Player 1 goal 55min</div><div>Player 1 goal 55min</div></td><td style="background-color:red;vertical-align:top; width:50%;"><div>Player 1 goal 55min</div></td></tr></table>';
        return '<div class="jsScoreDivM">'.$html.$match->getETLabel(false).'</div>';
    }
    public static function getMap($match, $class = '')
    {
        $html = '<div class="jsDivCenter">';
        for ($i = 0;$i < count($match);++$i) {
            if (isset($match[$i]->m_score1) && isset($match[$i]->m_score2)) {
                $html .= '<div class="jsScoreDivMap '.$class.'">'.$match[$i]->m_score1.JSCONF_SCORE_SEPARATOR.$match[$i]->m_score2.'</div>';
            }
        }

        $html .= '</div>';

        return $html;
    }
    public static function nameHTML($name, $home = 1, $class = '')
    {
        return '<div class="js_div_particName">'.$name.'</div>';
    }

    public static function JsHeader($options)
    {
        global $jsConfig;
        $kl = '';
        if (classJsportRequest::get('tmpl') != 'component') {
            $kl .= '<div class="">';
            $kl .= '<nav class="navbar navbar-default navbar-static-top" role="navigation">';
            $kl .= '<div class="navbar-header navHeadFull">';

            $img = $jsConfig->get('jsbrand_epanel_image');
            $brand = $jsConfig->get('jsbrand_on') ? 'JoomSport' : '';

            if ($img && is_file(JS_PATH_JOOMLA.$img)) {
                $kl .= '<a class="module-logo" href="'.classJsportLink::seasonlist().'" title="'.$brand.'"><img src="'.JS_LIVE_URL.$img.'" style="height:38px;" alt="'.$brand.'"></a>';
            }

            $kl .= '<ul class="nav navbar-nav pull-right navSingle">';
                //calendar
            if (isset($options['calendar']) && $options['calendar']) {
                $link = classJsportLink::calendar('', $options['calendar'], true);
                $kl .= '<a class="btn btn-default" href="'.$link.'" title=""><i class="date pull-left"></i>'.classJsportLanguage::get('BLFA_CALENDAR').'</a>';
            }
                //table
            if (isset($options['standings']) && $options['standings']) {
                $link = classJsportLink::season('', $options['standings'], true);
                $kl .= '<a class="btn btn-default" href="'.$link.'" title=""><i class="tableS pull-left"></i>'.classJsportLanguage::get('BL_TAB_TBL').'</a>';
            }
                //join season
            if (isset($options['joinseason']) && $options['joinseason']) {
                $link = classJsportLink::joinseason($options['joinseason']);
                $kl .= '<a class="btn btn-default" href="'.$link.'" title="">'.classJsportLanguage::get('BLFA_REGGG').'<i class="fa fa-hand-o-right"></i></a>';
            }
                //join team
            if (isset($options['jointeam']) && $options['jointeam']) {
                $link = classJsportLink::jointeam($options['jointeam']['seasonid'], $options['jointeam']['teamid']);
                $kl .= '<a class="btn btn-default" href="'.$link.'" title="">'.classJsportLanguage::get('BLFA_PLJOINTEAM').'<i class="fa fa-sign-in"></i></a>';
            }

            if (isset($options['playerlist']) && $options['playerlist']) {
                $link = classJsportLink::playerlist($options['playerlist']);
                $kl .= '<a class="btn btn-default" href="'.$link.'" title=""><i class="fa fa-user"></i>'.classJsportLanguage::get('BLFA_PLAYERSLIST').'</a>';
            }
            $kl .= classJsportPlugins::get('addHeaderButton', $options);
            $kl .= '</ul></div></nav></div>';
        }
        $kl .= self::JsHistoryBox($options);
        $kl .= self::JsTitleBox($options);
        $kl .= "<div class='jsClear'></div>";

        return $kl;
    }

    public static function JsTitleBox($options)
    {
        $kl = '';
        $kl .= '<div class="heading col-xs-12 col-lg-12">
                    <div class="heading col-xs-6 col-lg-6">
                        <h2>
                            <span itemprop="name">'.$options['title'].'</span>
                        </h2>
                    </div>
                    <div class="selection col-xs-6 col-lg-6 pull-right">
                    <span class="tournament-selection">Выберите турнир</span>
                        <form method="post">
                            <div class="data">
                                '.(isset($options['tourn']) ? $options['tourn'] : '').'
                                <input type="hidden" name="jscurtab" value="" />    
                            </div>
                        </form>
                    </div>
                </div>';

        return $kl;
    }

    public static function JsHistoryBox($options)
    {
        $kl = '<div class="history col-xs-12 col-lg-12">
          <!--ol class="breadcrumb">
            <li><a href="javascript:void(0);" onclick="history.back(-1);" title="[Back]">
                <i class="fa fa-long-arrow-left"></i>[Back]
            </a></li>
          </ol-->
          <div class="div_for_socbut">'.self::addSocial($options).(isset($options['print']) ? '<div class="jsd_buttons">'.$options['print'].'</div>' : '').'<div class="jsClear"></div></div>
        </div>';

        return $kl;
    }

    public static function JsFormViewElement($match, $partic_id)
    {
        $from_str = '';
        if (isset($match) && $match) {
            if (isset($match->object)) {
                $match_object = $match;
                $match = $match->object;
            }
            if ($match->score1 == $match->score2) {
                $class = 'match_draw';
                $alpha = classJsportLanguage::get('BLFA_D');
            } else {
                if (($match->score1 > $match->score2 && $match->team1_id == $partic_id)
                     ||
                   ($match->score1 < $match->score2 && $match->team2_id == $partic_id)
                        ) {
                    $class = 'match_win';
                    $alpha = classJsportLanguage::get('BLFA_W');
                } else {
                    $class = 'match_loose';
                    $alpha = classJsportLanguage::get('BLFA_L');
                }
            }

            if (!isset($match->home)) {
                $partic_home = $match_object->getParticipantHome();
                $partic_away = $match_object->getParticipantAway();
                $home = $partic_home->getName(false);
                $away = $partic_away->getName(false);
            } else {
                $home = $match->home;
                $away = $match->away;
            }

            $title = $match->score1.':'.$match->score2.' ('.$home.' - '.$away.')'.'<br />'.classJsportDate::getDate($match->m_date, $match->m_time);
            $link = classJsportLink::match('', $match->id, true);
            $from_str .= '<a href="'.$link.'" title="'.$title.'" class="jstooltipJSF"><span class="jsform_none '.$class.'">'.$alpha.'</span></a>';
        } else {
            $from_str = '<span class="jsform_none match_quest">?</span>'.$from_str;
        }

        return $from_str;
    }
    public static function JsCommentBox($comments, $canDelete)
    {
        $kl = '';
        for ($intA = 0; $intA < count($comments); ++$intA) {
            $comment = $comments[$intA];
            ?>
            <li id="divcomb_<?php echo $comment->id;
            ?>">
                <div class="comments-box-inner">
                    <div class="jsOverflowHidden" style="position:relative;">
                        <?php echo $comment->photo;
            ?>
                        <div class="date">
                            
                            <img src="<?php echo JS_LIVE_ASSETS;
            ?>images/calend.png" />
                            <?php echo $comment->date_time;
            ?>
                            <?php
                            if ($canDelete) {
                                ?>
                            <img src="<?php echo JS_LIVE_ASSETS;
                                ?>images/red_cross.png" border="0" class="jsCommentDelImg" onclick="javascript:delCom(<?php echo $comment->id;
                                ?>);">
                            <?php

                            }
            ?>
                        </div>
                        <h4 class="nickname">
                            <?php echo $comment->name;
            ?>
                        </h4>
                    </div>    
                    <div class="jsCommentBox"><?php echo $comment->comment;
            ?></div>
                </div>
            </li>
            <?php

        }

        return $kl;
    }

    public static function addSocial($options)
    {
        global $jsConfig;
        $kl = '';
        if (classJsportRequest::get('tmpl') != 'component') {
            if (isset($options['social']) && $options['social']) {
                ob_start();
                ?>
                <?php 
                if ($jsConfig->get('jsb_fbshare') == '1' || $jsConfig->get('jsb_fblike') == '1') {
                    ?>
                <div id="fb-root"></div>
                    <script>(function(d, s, id) {
                      var js, fjs = d.getElementsByTagName(s)[0];
                      if (d.getElementById(id)) return;
                      js = d.createElement(s); js.id = id;
                      js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
                      fjs.parentNode.insertBefore(js, fjs);
                    }(document, 'script', 'facebook-jssdk'));</script>
                <?php

                }
                if ($jsConfig->get('jsb_twitter') == '1') {
                    ?>
                <div class="jsd_buttons">

                    <a href="https://twitter.com/share" class="twitter-share-button" data-lang="en" target="_blank">Tweet</a>
                    <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>

                </div>
                <?php

                }
                ?>
                <?php 
                if ($jsConfig->get('jsb_fblike') == '1') {
                    ?>
                <div class="jsd_buttons"> 
                    <div class="fb-like" 
                         data-href="<?php echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
                    ?>" 
                            data-layout="button_count" 
                            data-action="like" 
                            data-show-faces="true">
                    </div>
                </div>
                <?php 
                }
                if ($jsConfig->get('jsb_fbshare') == '1') {
                    ?>
                <div class="jsd_buttons">
                    <!-- Your share button code -->
                    <div class="fb-share-button" 
                            data-href="<?php echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
                    ?>" 
                            data-layout="button_count">
                    </div>
                </div>
                <?php 
                }

                if ($jsConfig->get('jsb_gplus') == '1') {
                    ?>

                <div class="jsd_buttons">
                    <script src="https://apis.google.com/js/platform.js" async defer></script>
                    <g:plusone size="medium"></g:plusone>
                </div>

                <?php

                }

                $kl = ob_get_contents();
                ob_end_clean();
            }

            return $kl;
        }
    }
    public static function getBoxValue($box_id, $row){
        global $jsDatabase;
        $boxfield = 'boxfield_'.$box_id;
        
        $cBox = $jsDatabase->selectObject('SELECT * FROM #__bl_box_fields WHERE id='.$box_id) ;
        $options = json_decode($cBox->options, true);

        if($cBox->ftype == '1' && isset($options['calc'])){
            $boxfield1 = 'boxfield_'.$options['depend1'];
            $boxfield2 = 'boxfield_'.$options['depend2'];
            if(isset($row->{$boxfield1}) && $row->{$boxfield1} != NULL && isset($row->{$boxfield2}) && $row->{$boxfield2} != NULL){

                switch ($options['calc']) {
                    
                    case '0':
                        $res = NULL;
                        if($row->{$boxfield2})
                        $res =  $row->{$boxfield1} / $row->{$boxfield2};
                        return ($res !== NULL?round($res,2):'');
                        break;
                    case '1':
                        $res =  $row->{$boxfield1} * $row->{$boxfield2};
                        return ($res !== NULL?round($res,2):'');
                        break;
                    case '2':
                        $res =  $row->{$boxfield1} + $row->{$boxfield2};
                        return ($res !== NULL?round($res,2):'');

                        break;
                    case '3':
                        $res =  $row->{$boxfield1} - $row->{$boxfield2};
                        return ($res !== NULL?round($res,2):'');

                        break;
                    case '4':
                        return $row->{$boxfield1}.'/'.$row->{$boxfield2};

                        break;
                    default:
                        break;
                }
                
                
            }
            
        }
        
        $res = isset($row->{$boxfield})?$row->{$boxfield}:NULL;
        
        return ($res !== NULL?round($res,2):'');
    }
    public static function isMobile()
    {
        return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER['HTTP_USER_AGENT']);
    }
}
