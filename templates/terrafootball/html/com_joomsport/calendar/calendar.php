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

?>
<div class="table-responsive calendar-table">
<form role="form" method="post" lpformnum="1">
    <div class="searchMatchesDiv">
        <div>

            <div class="searchBar col-xs-12 col-lg-12">
                <?php if (isset($lists['filters']) && $lists['enable_search'] == '1') {
    ?>
                    <div <?php echo ($lists['apply_filters'] == true) ? ' style="display:block;"' : 'style="display:none;"';
    ?> id="jsFilterMatches">


                          <div class="form-group srcTeam">
                            <label for="partic"><?php echo classJsportLanguage::get('BL_PARTIC');
    ?></label>
                            <select name="filtersvar[partic]" id="partic" >
                              <option value="0"><?php echo classJsportLanguage::get('BLFA_ALL');
    ?></option>
                              <?php
                              if (count($lists['filters']['partic_list'])) {
                                  foreach ($lists['filters']['partic_list'] as $key => $value) {
                                      echo '<option value="'.$key.'" '.((isset($lists['filtersvar']->partic) && $lists['filtersvar']->partic == $key) ? 'selected' : '').'>'.$value.'</option>';
                                  }
                              }
    ?>
                            </select>
                            <select name="filtersvar[place]" style="width:80px;" >
                              <option value="0"><?php echo classJsportLanguage::get('BLFA_ALL');
    ?></option>
                              <option value="1" <?php echo  (isset($lists['filtersvar']->place) && $lists['filtersvar']->place == 1) ? 'selected' : ''?>><?php echo classJsportLanguage::get('BLFA_HOME_SHTR');
    ?></option>
                              <option value="2" <?php echo  (isset($lists['filtersvar']->place) && $lists['filtersvar']->place == 2) ? 'selected' : ''?>><?php echo classJsportLanguage::get('BLFA_AWAY_SHTR');
    ?></option>
                            </select>
                          </div>
                          <div class="form-group srcDay">
                            <label for="matchDay"><?php echo classJsportLanguage::get('BLFA_MATCHDAY');
    ?></label>
                            <select name="filtersvar[mday]" id="matchDay">
                              <option value="0"><?php echo classJsportLanguage::get('BLFA_ALL');
    ?></option>
                              <?php
                              if (count($lists['filters']['mday_list'])) {
                                  foreach ($lists['filters']['mday_list'] as $mday) {
                                      echo '<option value="'.$mday->id.'" '.((isset($lists['filtersvar']->mday) && $lists['filtersvar']->mday == $mday->id) ? 'selected' : '').'>'.$mday->m_name.'</option>';
                                  }
                              }
    ?>
                            </select>
                          </div>
                          <div class="form-group srcDate">
                            <label for="date_from"><?php echo classJsportLanguage::get('BLFA_DATE');
    ?></label>

                                <input type="date" onkeydown="return false" name="filtersvar[date_from]" value="<?php echo  (isset($lists['filtersvar']->date_from) && $lists['filtersvar']->date_from) ? $lists['filtersvar']->date_from : ''?>" class="form-control " id="date_from" placeholder="">
                                <input type="date" onkeydown="return false" name="filtersvar[date_to]" value="<?php echo  (isset($lists['filtersvar']->date_to) && $lists['filtersvar']->date_to) ? $lists['filtersvar']->date_to : ''?>" class="form-control" id="date_to" placeholder="">

                          </div>
                          <div class="form-group">
                              <button type="button" class="btn btn-default pull-right" onclick="javascript:this.form.submit();"><i class="fa fa-search"></i><?php echo classJsportLanguage::get('BLFA_SEARCH');
    ?></button>
                          </div>
                    </div>
                <?php
} ?>
            </div>

        </div>
    </div>
    <div class="table-responsive">
        <?php
        $matches = $rows;
        $pagination = $lists['pagination'];
        $mdname = true;
        $html = '';
        global $jsConfig;
        if (count($matches)) {
            $html .= '<div class="table-responsive">';
            if (jsHelper::isMobile()) {
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

                if (jsHelper::isMobile()) {
                    $html .= '<div class="jsMobileMatchCont">';
                    if ($jsConfig->get('enbl_mdnameoncalendar') == '1' && $mdname) {
                        $html .= '<div class="jsDivMobileMdayName">';
                        $html .= $match->object->m_name.'</div>';
                    }
                    $html .= '<div class="date">'.$match_date;
                    if ($jsConfig->get('cal_venue')) {
                        $html .= '<div class="jsMatchDivVenue">'
                                    .$match->getLocation()
                                .'</div>';
                    }
                    $html .= '</div>';
                    //$html .= '<div class="jsDivCenter">'
                    //            .'<div class="jsDivLineEmbl">'
                    //            .jsHelper::nameHTML($partic_home->getName(true))
                    //        .'</div></div>';
                    $html .= '<div class="jsMatchDivScore">'.
                    
                                '<div class="home-team">'.
                                    ($partic_home->getEmblem()).
                                    '<div class="team-name">'
                                    .jsHelper::nameHTML($partic_home->getName(true)).
                                    '</div>'.
                                '</div>'.

                                    '<div class="jsScoreBonusB">'.jsHelper::getScore($match, '').'</div>'.

                                '<div class="away-team">'.
                                    ($partic_away->getEmblem()).
                                    '<div class="team-name">'
                                    .jsHelper::nameHTML($partic_away->getName(true)).
                                    '</div>'.
                                '</div>'.

                                '</div>';
                    //$html .= '<div  class="jsDivCenter">'
                    //            .'<div class="jsDivLineEmbl">'
                    //            .jsHelper::nameHTML($partic_away->getName(true))
                    //        .'</div></div>';

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
                            .'<div class="jstable-cell jsMatchDivHome">
                                <div class="jsDivLineEmbl">'

                                    .jsHelper::nameHTML($partic_home->getName(true))
                                .'</div>'
                            .'</div>'
                            .'<div class="jstable-cell jsMatchDivHomeEmbl">'
                                .'<div class="jsDivLineEmbl" style="float:right;">'
                                    .($partic_home->getEmblem())

                                .'</div>

                            </div>
                            <div class="jstable-cell jsMatchDivScore">
                                '.jsHelper::getScore($match, '', $tooltip).'
                            </div>
                            <div class="jstable-cell jsMatchDivAwayEmbl">
                                <div class="jsDivLineEmbl">'

                                        .($partic_away->getEmblem())
                                .'</div>'
                            .'</div>'
                            .'<div class="jstable-cell jsMatchDivAway">'
                                .'<div class="jsDivLineEmbl">'

                                        .jsHelper::nameHTML($partic_away->getName(true), 0).'

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

            echo $html;
        }
        ?>
    </div>
</form>

</div>
