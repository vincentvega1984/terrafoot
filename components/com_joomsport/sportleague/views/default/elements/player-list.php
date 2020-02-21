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
    if (count($rows->lists['players'])) {
        ?>
    <form role="form" method="post" lpformnum="1">
    <table class="table table-striped cansorttbl" id="jstable_plz">
        <thead>
            <tr>
                
                    <?php
                    $dest = (classJsportRequest::get('sortf') == 'first_name') ? (classJsportRequest::get('sortd') == 'DESC' ? 'ASC' : 'DESC') : 'DESC';
        $class = '';
        if (classJsportRequest::get('sortf') == 'first_name' || classJsportRequest::get('sortf') == '') {
            $class = (classJsportRequest::get('sortd') == 'DESC') ? 'headerSortDown' : 'headerSortUp';
        }
        ?>
                <th class="<?php echo $class?>">
                <?php 
                    if (isset($lists['pagination']) && $lists['pagination']) {
                        ?>
                    <a href="<?php echo classJsportLink::playerlist($rows->season_id, '&sortf=first_name&sortd='.$dest.'&playerevents='.$rows->playerevents.($rows->team_id?'&team_id='.$rows->team_id:''))?>"><span><?php echo classJsportLanguage::get('BLFA_NAME');
                        ?></span><i class="fa"></i></a>

                    <?php

                    } else {
                        ?>
                    <a href="javascript:void(0);">
                    <span><?php echo classJsportLanguage::get('BLFA_NAME');
                        ?></span><i class="fa"></i>
                    </a>
                    <?php

                    }
        ?>
                </th>

                <?php
                if (isset($rows->lists['played_matches_col']) && $rows->lists['played_matches_col']) {
                    $dest = (classJsportRequest::get('sortf') == 'played') ? (classJsportRequest::get('sortd') == 'DESC' ? 'ASC' : 'DESC') : 'DESC';
                    $class = '';
                    if (classJsportRequest::get('sortf') == 'played') {
                        $class = (classJsportRequest::get('sortd') == 'DESC') ? 'headerSortDown' : 'headerSortUp';
                    }
                    ?>
                    <th class="jsTextAlignCenter <?php echo $class?>">
                        <?php
                        if (isset($lists['pagination']) && $lists['pagination']) {
                            ?>
                        <a href="<?php echo classJsportLink::playerlist($rows->season_id, '&sortf=played&sortd='.$dest.'&playerevents='.$rows->playerevents.($rows->team_id?'&team_id='.$rows->team_id:''))?>"><span><?php echo $rows->lists['played_matches_col'];
                            ?></span><i class="fa"></i></a>

                        <?php

                        } else {
                            ?>
                        <a href="javascript:void(0);">
                                    
                            <span><?php echo $rows->lists['played_matches_col'];
                            ?></span><i class="fa"></i>
                        </a>

                        <?php

                        }
                    ?>
                        
                    </th>

                    <?php

                }

        if (count($rows->lists['events_col'])) {
            foreach ($rows->lists['events_col'] as $key => $value) {
                $dest = (classJsportRequest::get('sortf') == $key) ? (classJsportRequest::get('sortd') == 'DESC' ? 'ASC' : 'DESC') : 'DESC';
                $class = '';
                if (classJsportRequest::get('sortf') == $key) {
                    $class = (classJsportRequest::get('sortd') == 'DESC') ? 'headerSortDown' : 'headerSortUp';
                }
                ?>
                        <th class="jsTextAlignCenter <?php echo $class?>">
                            <?php
                            if (isset($lists['pagination']) && $lists['pagination']) {
                                ?>
                            <a href="<?php echo classJsportLink::playerlist($rows->season_id, '&sortf='.$key.'&sortd='.$dest.'&playerevents='.$rows->playerevents.($rows->team_id?'&team_id='.$rows->team_id:''))?>">
                                <span>
                                    <?php echo $value->getEmblem();
                                ?>
                                    <?php echo $value->getEventName();
                                ?>
                                </span>
                                <i class="fa"></i>
                            </a>
                            <?php

                            } else {
                                ?>
                            <a href="javascript:void(0);">
                                <span>
                                    <?php echo $value->getEmblem();
                                ?>
                                    <?php echo $value->getEventName();
                                ?>
                                </span>
                                <i class="fa"></i>
                            </a>    
                            <?php

                            }
                ?>
                        </th>
                        <?php

            }
        }
        if (count($rows->lists['ef_table'])) {
            foreach ($rows->lists['ef_table'] as $ef) {
                $key = 'efields_'.$ef->id;
                $value = $ef->name;
                $dest = (classJsportRequest::get('sortf') == $key) ? (classJsportRequest::get('sortd') == 'DESC' ? 'ASC' : 'DESC') : 'DESC';
                $class = '';
                if (classJsportRequest::get('sortf') == $key) {
                    $class = (classJsportRequest::get('sortd') == 'DESC') ? 'headerSortDown' : 'headerSortUp';
                }
                ?>
                        <th class="jsTextAlignCenter <?php echo $class?>">
                        <?php
                        if (isset($lists['pagination']) && $lists['pagination']) {
                        ?>
                    
                        <a href="<?php echo classJsportLink::playerlist($rows->season_id, '&sortf='.$key.'&sortd='.$dest.'&playerevents='.$rows->playerevents.($rows->team_id?'&team_id='.$rows->team_id:''))?>">
                            
                        
                            <span><?php echo $value;
                ?></span><i class="fa"></i>
                            </a>  

                    <?php

                    } else {
                        ?>
                    <a href="javascript:void(0);">
                    <span><?php echo $value;
                ?></span><i class="fa"></i>
                    </a>
                    <?php

                    }
                        ?>
                               
                        </th>
                    <?php

            }
        }
        ?>
            </tr>
        </thead>
        <tbody>
        <?php

        for ($intA = 0; $intA < count($rows->lists['players']); ++$intA) {
            $player = $rows->lists['players'][$intA];
            $playerevents = $player->lists['tblevents'];
            ?>

            <tr>
                <td>
                    <div class="jsDivLineEmbl">
                        <?php echo $player->getEmblem(true, 0, '');
            ?>
                        <?php echo jsHelper::nameHTML($player->getName(true));
            ?>


                    </div>

                </td>
                <?php
                if (isset($rows->lists['played_matches_col']) && $rows->lists['played_matches_col']) {
                    ?>
                    <td class="jsTextAlignCenter">
                        <?php
                        echo $playerevents->played;
                    ?>
                    </td>
                    <?php

                }
            ?>
                <?php
                
                if (count($rows->lists['events_col'])) {
                    foreach ($rows->lists['events_col'] as $key => $value) {
                        ?>
                        <td class="jsTextAlignCenter">
                            <?php
                            if (isset($playerevents->{$key})) {
                                
                                if (is_float(floatval($playerevents->{$key}))) {
                                    echo round($playerevents->{$key}, 3);
                                } else {
                                    echo floatval($playerevents->{$key});
                                }
                            }
                        ?>
                            
                        </td>
                        <?php

                    }
                }
            ?>
                <?php
                if (count($rows->lists['ef_table'])) {
                    foreach ($rows->lists['ef_table'] as $ef) {
                        $key = 'ef_'.$ef->id;
                        $value = $ef->name;
                        ?>
                        <td class="jsTextAlignCenter">
                            <?php
                            if (isset($player->{$key})) {
                                echo $player->{$key};
                            }
                        ?>
                            
                        </td>
                        <?php

                    }
                }
            ?>
            </tr>
            <?php

        }
        ?>
        </tbody>
    </table>  
        
    
<?php
if (isset($lists['pagination']) && $lists['pagination']) {
    require_once JS_PATH_VIEWS.'elements'.DIRECTORY_SEPARATOR.'pagination.php';
    echo paginationView($lists['pagination']);
} else {
    ?>
<script>
    jQuery(document).ready(function() {
        jQuery('#jstable_plz').tablesorter();
    } );
</script> 
<?php 
}
        ?>
</form>
    <?php

    }
    ?>
</div>
