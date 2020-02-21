<div class="jscaruselcont jsview1" id="jsScrollMatches<?php echo $module_id;?>">

            <ul style="margin: 0px; padding: 0px; position: relative; list-style: none; z-index: 1;">
                <?php
                foreach ($list as $match) {
                    $partic_home = $match->getParticipantHome();
                    $partic_away = $match->getParticipantAway();
                    $match_date = classJsportDate::getDate($match->object->m_date, $match->object->m_time);
                   ?>
                <li>
                    <div class="jsmatchcont">
                        <?php
                        if($params->get('seasonname_is')){
                            $seasObj =  new modelJsportSeason($match->season_id);
                            echo '<div class="jsmatchseason">'.$seasObj->getName().'</div>';
                        }
                        ?>
                        <div class="jsmatchdate">



                            <?php echo $match_date;?>
                            <?php if($match->object->venue_id && $params->get('venue_is')){
                                $venue_name = $match->getLocation(false);
                                ?>
                                <a href="<?php echo classJsportLink::venue($venue_name, $match->object->venue_id, true, $cItemId);?>" title="<?php echo $match->getLocation(false);?>"><img src="<?php echo JS_LIVE_ASSETS;?>/images/location.png" /></a>
                            <?php } ?>
                        </div>
                        <table>
                            <tr>
                                <td class="tdminembl">
                                    <?php
                                    if($params->get('embl_is')){
                                        echo $partic_home->getEmblem(true, 0, 'emblInline', 0, $cItemId);
                                    }
                                    ?>
                                </td>
                                <td class="tdminwdt">
                                    <?php
                                    echo jsHelper::nameHTML($partic_home->getName(true, $cItemId));
                                    ?>
                                </td>
                                <?php
                                if($match->object->m_played > 1 || $match->object->m_played == 0){
                                ?>
                                <td width="30" rowspan="2" class="jsVerticlLn">

                                    <?php
                                    echo jsHelper::getScore($match,'','',$cItemId);
                                    
                                    ?>

                                </td>
                                <?php
                                }else{
                                    ?>
                                <td width="30">
                                    <?php
                                    if($match->object->m_played){    
                                        echo '<div class="scoreScrMod">'.classJsportLink::match($match->object->score1, $match->object->id,false,'',$cItemId).'</div>';
                                    }
                                    ?>
                                </td>
                                    <?php
                                }
                                ?>
                            </tr>
                            <tr>
                                <td>
                                    <?php
                                    if($params->get('embl_is')){
                                        echo $partic_away->getEmblem(true, 0, 'emblInline', 0,  $cItemId);
                                    }
                                    ?>
                                </td>
                                <td class="tdminwdt">
                                    <?php
                                    echo jsHelper::nameHTML($partic_away->getName(true, $cItemId));
                                    ?>
                                </td>
                                <?php
                                if($match->object->m_played > 1  || $match->object->m_played == 0){
                                    
                                }else{
                                ?>
                                <td>
                                    <?php
                                    if($match->object->m_played > 1){
                                        echo jsHelper::getScore($match,'','',$cItemId);
                                    }else if($match->object->m_played){
                                        echo '<div class="scoreScrMod">'.classJsportLink::match($match->object->score2, $match->object->id,false,'',$cItemId).'</div>';
                                    }
                                    ?>
                                </td>
                                <?php
                                }
                                ?>
                            </tr>
                        </table>

                    </div>

                </li>
                    <?php

                }
                ?>
            </ul>
        </div>