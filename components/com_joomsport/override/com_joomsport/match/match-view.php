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
<div class="table-responsive">
    <?php
            global $jsConfig;
            $width = $jsConfig->get('teamlogo_height');
            $match = $rows;
            $partic_home = $match->getParticipantHome();
            $partic_away = $match->getParticipantAway();
            ?>
    <?php
    if (count($rows->lists['m_events_home']) || count($rows->lists['m_events_away'])) {
        ?>
    <div class="center-block jscenter jsMarginBottom30">
        <h3 class="jsInlineBlock"><?php echo classJsportLanguage::get('BL_PBL_STAT');
        ?></h3>
    </div>
    <div class="jsPaddingBottom30">
        <div class="jsOverflowHidden">
            
            <div class="jsInline">
                <div>
                    
                    <div class="jstable-cell ">
                    <?php echo $partic_home ? ($partic_home->getEmblem(true, 0, 'emblInline', $width)) : '';
        ?>
                    </div>
                    <div class="jstable-cell ">

                        <?php

                            echo ($partic_home) ? ($partic_home->getName(true)) : '';
        ?>
                    </div>
                </div>
                <table class="jsTblMatchTab firstTeam">
                    <thead>
                        <tr>
                            <th></th>
                            <th><?php echo classJsportLanguage::get('BLFA_QTY');
        ?></th>
                            <th><?php echo classJsportLanguage::get('BLFA_EVENT');
        ?></th>
                            <th><?php echo classJsportLanguage::get('BLFA_TIME');
        ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    for ($intP = 0; $intP < count($rows->lists['m_events_home']); ++$intP) {
                        ?>
                        <tr>
                            <td class="evPlayerName">
                                <?php echo $rows->lists['m_events_home'][$intP]->obj->getName(true);
                        ?>
                            </td>
                            <td>
                                <?php echo $rows->lists['m_events_home'][$intP]->ecount;
                        ?>
                            </td>
                            <td>
                                <?php echo $rows->lists['m_events_home'][$intP]->objEvent->getEmblem(false);
                        ?>
                            </td>
                            
                            
                            <td>
                                <?php echo $rows->lists['m_events_home'][$intP]->minutes ? $rows->lists['m_events_home'][$intP]->minutes."'" : '';
                        ?>
                            </td>
                        </tr>    
                        <?php

                    }
        if (!count($rows->lists['m_events_home'])) {
            //echo "&nbsp";
        }
        ?>
                    </tbody>
                </table>
            </div>
            <div  class="jsInline">
                <div style="text-align: right;">
                    
                    
                    <div class="jstable-cell" style="display:inline-block;">

                        <?php

                            echo ($partic_away) ? ($partic_away->getName(true)) : '';
        ?>
                    </div>
                    <div class="jstable-cell" style="display:inline-block;">
                    <?php echo $partic_away ? ($partic_away->getEmblem(true, 0, 'emblInline', $width)) : '';
        ?>
                    </div>
                </div>
                <table class="jsTblMatchTab">
                    <thead>
                        <tr>
                            <th><?php echo classJsportLanguage::get('BLFA_TIME');
        ?></th>
                            <th><?php echo classJsportLanguage::get('BLFA_EVENT');
        ?></th>
                            <th><?php echo classJsportLanguage::get('BLFA_QTY');
        ?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    for ($intP = 0; $intP < count($rows->lists['m_events_away']); ++$intP) {
                        ?>
                        <tr>
                            <td>
                                <?php echo $rows->lists['m_events_away'][$intP]->minutes ? $rows->lists['m_events_away'][$intP]->minutes."'" : '';
                        ?>
                            </td>
                            <td>
                                <?php echo $rows->lists['m_events_away'][$intP]->objEvent->getEmblem(false);
                        ?>
                            </td>
                            <td>
                                <?php echo $rows->lists['m_events_away'][$intP]->ecount;
                        ?>
                            </td>
                            <td class="evPlayerName">
                                <?php echo $rows->lists['m_events_away'][$intP]->obj->getName(true);
                        ?>
                            </td>
                            
                        </tr>    
                        <?php

                    }
        if (!count($rows->lists['m_events_away'])) {
            //echo "&nbsp";
        }
        ?>
                    </tbody>
                </table>
                
            </div>
        </div>
    </div>
    <?php

    }
    ?>
    <?php
    if (count($rows->lists['team_events'])) {
        ?>
    <div class="center-block jscenter jsMarginBottom30">
        <h3 class="jsInlineBlock"><?php echo classJsportLanguage::get('BLFA_MATCHSTATS');
        ?></h3>
    </div>
    <div class="jsPaddingBottom30 jsTeamStat">
        <div class="jsOverflowHidden">
            <div class="jsInline">
                <div>
                    
                    <div class="jstable-cell ">
                    <?php echo $partic_home ? ($partic_home->getEmblem(true, 0, 'emblInline', $width)) : '';
        ?>
                    </div>
                    <div class="jstable-cell ">

                        <?php

                            echo ($partic_home) ? ($partic_home->getName(true)) : '';
        ?>
                    </div>
                </div>
            </div> 
            <div class="jsInline">
                <div style="text-align: right;">
                    
                    
                    <div class="jstable-cell" style="display:inline-block;">

                        <?php

                            echo ($partic_away) ? ($partic_away->getName(true)) : '';
        ?>
                    </div>
                    <div class="jstable-cell" style="display:inline-block;">
                    <?php echo $partic_away ? ($partic_away->getEmblem(true, 0, 'emblInline', $width)) : '';
        ?>
                    </div>
                </div>
            </div>
            <div class="jstable">
                <?php
                for ($intP = 0; $intP < count($rows->lists['team_events']); ++$intP) {
                    $graph_sum = $rows->lists['team_events'][$intP]->home_value + $rows->lists['team_events'][$intP]->away_value;
                    $graph_home_class = ' jsGray';
                    $graph_away_class = ' jsRed';
                    if ($graph_sum) {
                        $graph_home = round(100 * $rows->lists['team_events'][$intP]->home_value / $graph_sum);
                        $graph_away = round(100 * $rows->lists['team_events'][$intP]->away_value / $graph_sum);
                        if ($graph_home > $graph_away) {
                            //$graph_home_class = ' jsRed';
                        } else {
                            //$graph_away_class = ' jsRed';
                        }
                    }
                    if (!$graph_home) {
                        $graph_home_class = '';
                    }
                    if (!$graph_away) {
                        $graph_away_class = '';
                    }
                    ?>
                    <div class="jstable-row jsColTeamEvents">
                        
                        <div class="jstable-cell jsCol5">
                            <div class="teamEventGraph">
                                <div class="teamEventGraphHome<?php echo $graph_home_class?>" style="width:<?php echo $graph_home?>%"><?php echo $rows->lists['team_events'][$intP]->home_value;
                    ?></div>
                            </div>
                            
                        </div>
                        <div class="jstable-cell jsCol6">

                            <div>
                                <?php 
                                echo $rows->lists['team_events'][$intP]->objEvent->getEmblem();
                    echo $rows->lists['team_events'][$intP]->objEvent->getEventName();
                    ?>
                            </div>
 
                        </div>
                        <div class="jstable-cell jsCol5">
                            <div class="teamEventGraph">
                                <div class="teamEventGraphAway<?php echo $graph_away_class?>" style="width:<?php echo $graph_away?>%"><?php echo $rows->lists['team_events'][$intP]->away_value;
                    ?></div>
                            </div>
                            
                        </div>
                        

                    </div>    
                    <?php

                }
        ?>
            </div>
            
        </div>
    </div>
    <?php

    }
    ?>
    <?php

    if (jsHelper::getADF($rows->lists['ef'])) {
        ?>
        <div class="center-block jscenter">
            <h3><?php echo classJsportLanguage::get('BL_EBL_VAL');
        ?></h3>
        </div>
        <div class="matchExtraFields jsPaddingBottom30">
            <?php
            $ef = $rows->lists['ef'];
        if (count($ef)) {
            foreach ($ef as $key => $value) {
                if ($value != null) {
                    echo '<div class="JSplaceM">';
                    echo  '<div class="labelEFM">'.$key.'</div>';
                    echo  '<div class="valueEFM">'.$value.'</div>';
                    echo  '</div>';
                }
            }
        }
        ?>
        </div>
    <?php

    }
    ?>
</div>