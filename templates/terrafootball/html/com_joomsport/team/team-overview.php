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
<?php global $jsConfig;?>
<div class="jsOverflowHidden">
    <div class="table-responsive">
        <?php
        if ($jsConfig->get('tlb_position') && $rows->lists['curposition']) {
            $aoptions = array(
                'played_chk' => classJsportLanguage::get('BL_TBL_PLAYED'),
                'win_chk' => classJsportLanguage::get('BL_TBL_WINS'),
                'lost_chk' => classJsportLanguage::get('BL_TBL_LOST'),
                'draw_chk' => classJsportLanguage::get('BL_TBL_DRAW'),
                'gd_chk' => classJsportLanguage::get('BL_TBL_GD'),
                'point_chk' => classJsportLanguage::get('BL_TBL_POINTS'),

            );
            $json = json_decode($rows->lists['curposition']->options, true);
            ?>
            <div class="overviewBlocks">
                <div class="center-block jscenter">
                    <h3><?php echo classJsportLanguage::get('BLFA_LEAGUE_LOCATION');
            ?></h3>
                </div>
                <table class="tblPosition">
                    <thead>
                        <tr>
                            <th><?php echo classJsportLanguage::get('BL_TBL_RANK');
            ?></th>
                            <?php
                                foreach ($aoptions as $key => $value) {
                                    ?>
                                    <th><?php echo $value;
                                    ?></th>
                                    <?php

                                }
            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo $rows->lists['curposition']->ordering;
            ?></td>
                            <?php
                                foreach ($aoptions as $key => $value) {
                                    ?>
                                    <td><?php echo $json[$key];
                                    ?></td>
                                    <?php

                                }
            ?>
                        </tr>
                    </tbody>
                </table>
            </div>    
            <?php

        }

        ?>
        
        <?php
        if ($jsConfig->get('tlb_form') && count($rows->lists['matches_latest'])) {
            ?>
            <div class="overviewBlocks">
                <div class="center-block jscenter">
                    <h3><?php echo classJsportLanguage::get('BLFA_CURRENT_FORM');
            ?></h3>
                </div>
                <table class="tblPosition">
                    <thead>
                        <tr>
                            <?php 
                            for ($intA = 0; $intA < 5; ++$intA) {
                                if (isset($rows->lists['matches_latest'][$intA]->object)) {
                                    if ($rows->lists['matches_latest'][$intA]->object->team1_id == $rows->object->id) {
                                        echo '<th>'.classJsportLanguage::get('BLFA_HOME_SHTR').'</th>';
                                    } else {
                                        echo '<th>'.classJsportLanguage::get('BLFA_AWAY_SHTR').'</th>';
                                    }
                                } else {
                                    echo '<th></th>';
                                }
                            }
            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php 
                            for ($intA = 0; $intA < 5; ++$intA) {
                                echo '<td>';
                                echo jsHelper::JsFormViewElement(isset($rows->lists['matches_latest'][$intA]) ? ($rows->lists['matches_latest'][$intA]) : null, $rows->object->id);
                                echo '</td>';
                            }
            ?>
                            
                        </tr>
                    </tbody>
                </table>
            </div>    
        <?php 
        }

        ?>
        
        <?php
        if ($jsConfig->get('tlb_latest') && count($rows->lists['matches_latest'])) {
            ?>
            <div class="overviewBlocks">
                <div class="center-block jscenter">
                    <h3><?php echo classJsportLanguage::get('BLFA_LATEST_RESULT');
            ?></h3>
                </div>
                <table class="tblPosition">
                    <thead>
                        <tr>
                            <th width="25%">
                                <?php echo classJsportLanguage::get('BLFA_DATE');
            ?>
                            </th>
                            <th class="jsTextAlignLeft">
                                <?php echo classJsportLanguage::get('BLFA_TEAM');
            ?>
                            </th>
                            <th width="15%">
                                <?php echo classJsportLanguage::get('BLFA_LOCATION');
            ?>
                            </th>
                            <th width="20%">
                                <?php echo classJsportLanguage::get('BLFA_RESULTS');
            ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php 
                        for ($intA = 0; $intA < count($rows->lists['matches_latest']); ++$intA) {
                            $match = $rows->lists['matches_latest'][$intA];
                            $match_date = classJsportDate::getDate($match->object->m_date, $match->object->m_time);
                            if ($rows->object->id == $match->object->team1_id) {
                                $field = classJsportLanguage::get('BLFA_HOME_SHTR');
                                $opponent = $match->getParticipantAway();
                            } else {
                                $field = classJsportLanguage::get('BLFA_AWAY_SHTR');
                                $opponent = $match->getParticipantHome();
                            }
                            echo '<tr>';
                            echo '<td>'.$match_date.'</td>';
                            echo '<td class="jsTextAlignLeft">'.$opponent->getEmblem().' '.$opponent->getName(true).'</td>';
                            echo '<td>'.$field.'</td>';
                            echo '<td>'.jsHelper::getScore($match).'</td>';
                            echo '</tr>';
                        }
            ?>
                            

                    </tbody>
                </table>
            </div>    
        <?php 
        }

        ?>
        
        <?php
        if ($jsConfig->get('tlb_next') && count($rows->lists['matches_next'])) {
            ?>
            <div class="overviewBlocks">
                <div class="center-block jscenter"><h3><?php echo classJsportLanguage::get('BLFA_NEXT_FIXTURES');
            ?></h3></div>
                <table class="tblPosition">
                    <thead>
                        <tr>
                            <th width="25%">
                                <?php echo classJsportLanguage::get('BLFA_DATE');
            ?>
                            </th>
                            <th class="jsTextAlignLeft">
                                <?php echo classJsportLanguage::get('BLFA_TEAM');
            ?>
                            </th>
                            <th width="15%">
                                <?php echo classJsportLanguage::get('BLFA_LOCATION');
            ?>
                            </th>
                            <th width="20%">
                                <?php echo classJsportLanguage::get('BLFA_RESULTS');
            ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php 
                        for ($intA = 0; $intA < count($rows->lists['matches_next']); ++$intA) {
                            $match = $rows->lists['matches_next'][$intA];
                            $match_date = classJsportDate::getDate($match->object->m_date, $match->object->m_time);
                            if ($rows->object->id == $match->object->team1_id) {
                                $field = classJsportLanguage::get('BLFA_HOME_SHTR');
                                $opponent = $match->getParticipantAway();
                            } else {
                                $field = classJsportLanguage::get('BLFA_AWAY_SHTR');
                                $opponent = $match->getParticipantHome();
                            }
                            echo '<tr>';
                            echo '<td>'.$match_date.'</td>';
                            echo '<td class="jsTextAlignLeft">'.$opponent->getEmblem().' '.$opponent->getName(true).'</td>';
                            echo '<td>'.$field.'</td>';
                            echo '<td>'.jsHelper::getScore($match).'</td>';
                            echo '</tr>';
                        }
            ?>
                            

                    </tbody>
                </table>
            </div>    
        <?php 
        }

        ?>
    </div>
</div>
    