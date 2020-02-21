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
<div id="jsMatchViewID">
    <div class="heading col-xs-12 col-lg-12">
        <div class="col-xs-5 col-lg-5">
            <div class="matchdtime">
                <?php
                if ($rows->object->m_date && $rows->object->m_date != '0000-00-00') {
                    echo '<img src="'.JS_LIVE_ASSETS.'images/calendar-date.png" alt="" />';
                    if (preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $rows->object->m_date)) {
                        echo classJsportDate::getDate($rows->object->m_date, $rows->object->m_time);
                    } else {
                        echo $rows->object->m_date;
                    }
                }
                ?>
            </div>
        </div>
        <div class="col-xs-2 col-lg-2 jsTextAlignCenter">
            <?php if ($rows->object->is_extra) {
    ?>
            <img src="<?php echo JS_LIVE_ASSETS?>images/extra-t.png" alt="<?php echo classJsportLanguage::get('BLFA_TEAM_WON_ET');?>" title="<?php echo classJsportLanguage::get('BLFA_TEAM_WON_ET');?>" />
            <?php 
} ?>
        </div>
        <div class="col-xs-5 col-lg-5">
            
            <div class="matchvenue">
            <?php
            if ($rows->object->m_location || $rows->object->venue_id) {
                echo $rows->getLocation();
                echo '<img src="'.JS_LIVE_ASSETS.'images/location.png" />';
            }
            ?>
            </div>
        </div>
        
        
    </div>
    <div class="jsClear"></div>
    <div class="jsmatchHeader table-responsive">
        <div class="topMHead"></div>
        <?php 
            global $jsConfig;
            $width = $jsConfig->get('set_emblemhgonmatch');
            $match = $rows;
            $partic_home = $match->getParticipantHome();
            $partic_away = $match->getParticipantAway();

            ?>
        <?php
        if (jsHelper::isMobile()) {
            ?>
            <div class="jsMatchDivMain">
                <div>
                <div class="jsDivLineEmbl">

                    <?php echo jsHelper::nameHTML($partic_home->getName(true))?>
                </div>
                </div>    
                <div class="jsMatchDivScore">
                    <?php echo($partic_home->getEmblem()).
                        '<div class="jsScoreBonusB">'.jsHelper::getScore($match, '').'</div>'
                    .($partic_away->getEmblem())?>
                </div>
                <div>
                    <div class="jsDivLineEmbl">

                    <?php echo jsHelper::nameHTML($partic_away->getName(true))?>
                    </div>
                </div>
            </div>    
            <?php 
        } else {
            ?>
        <div class="jstable">
            
            <div class="jstable-row">
                <div class="jstable-cell jsMatchEmbl">
                    <?php echo $partic_home ? ($partic_home->getEmblem(true, 0, 'emblInline', $width)) : '';
            ?>
                </div>
                <div class="jstable-cell jsMatchPartName">

                    <?php

                        echo ($partic_home) ? ($partic_home->getName(true)) : '';
            ?>
                </div>
                <div class="jstable-cell  mainScoreDiv">
                    <?php echo jsHelper::getScoreBigM($match);
            ?>
                </div>
                <div class="jstable-cell jsMatchPartName" style="text-align: right;">
                    <?php

                        echo ($partic_away) ? ($partic_away->getName(true)) : '';
            ?>
                </div>
                <div class="jstable-cell jsMatchEmbl">
                    <?php echo $partic_away ? ($partic_away->getEmblem(true, 0, 'emblInline', $width)) : '';
            ?>
                </div>
            </div>
            <?php

            ?>
        </div>
        <?php

        }
        ?>
        <!-- MAPS -->
        <?php

        if (count($rows->lists['maps'])) {
            echo jsHelper::getMap($rows->lists['maps']);
        }
        ?>
        <div class="botMHead"></div>
    </div>
    <div class="jsClear">
        <?php
            $tabs = $rows->getTabs();
            jsHelperTabs::draw($tabs, $rows, 'match');
        ?>
    </div>
    <?php if (isset($lists['enbl_comments']) && $lists['enbl_comments']) {
    ?>
    <div>
        <div class="dv_comments center-block jscenter"><h3><?php echo classJsportLanguage::get('BLFA_COMMENTS');
    ?></h3></div>

        <ul class="comments-box" id="all_comments">
        <?php
            jsHelper::JsCommentBox($lists['usr_comments'], $lists['canDeleteComments']);
    $link = JUri::base().'index.php?option=com_joomsport&controller=users&task=add_comment&format=row&tmpl=component';
    ?>
        </ul>     

        <form action="<?php echo $link;
    ?>" method="POST" id="comForm" name="comForm">
        <!-- <Post comment> -->
            <div class="post-comment">
                <textarea class="form-control" rows="3" name="addcomm" id="addcomm"></textarea>
                <button type="submit" class="btn btn-default" id="submcom"><span><b><?php echo classJsportLanguage::get('BLFA_POSTCOMMENT');
    ?></b></span></button>
                <input type="hidden" name="mid" value="<?php echo $rows->object->id;
    ?>" />
            </div>
        </form>
    </div>    
    <?php 
} ?>
    
</div>
