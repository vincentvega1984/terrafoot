<div class="matches-scroller">

    
        <?php if($enbl_slider){?>
            <ul class="scores-items">
        <?php }else{?>
            <div class="jsmatchcont">
                <div class="match-wrapper">
        <?php } ?>
            
                <?php
                foreach ($list as $match) {
                    $partic_home = $match->getParticipantHome();
                    $partic_away = $match->getParticipantAway();
                    $match_date = classJsportDate::getDate($match->object->m_date, $match->object->m_time);
                   ?>
                    <?php if($enbl_slider){?>
                    <li class="item">
                        <div class="jsmatchdate">
                            <?php echo $match_date;?>
                            <?php if($match->object->venue_id && $params->get('venue_is')){
                                $venue_name = $match->getLocation(false);
                            ?>
                            <a href="<?php echo classJsportLink::venue($venue_name, $match->object->venue_id, true, $cItemId);?>" title="<?php echo $match->getLocation(false);?>"><img src="<?php echo JS_LIVE_ASSETS;?>/images/location.png" /></a>
                            <?php } ?>
                        </div>
                        <div class="item-wrapper">
                            <?php
                            if($params->get('seasonname_is')){
                                $seasObj =  new modelJsportSeason($match->season_id);
                                echo '<div class="jsmatchseason">'.$seasObj->getName().'</div>';
                            }
                            ?>
                                    <?php }else{?>

                                    <?php
                                    if($params->get('seasonname_is')){
                                        $seasObj =  new modelJsportSeason($match->season_id);
                                        echo '<div class="jsmatchseason">'.$seasObj->getName().'</div>';
                                    }
                                    ?>

                                    <?php }?>        
                                    <div class="match-wrap">
                                        <div class="home-team team">
                                            <span class="team-logo">
                                                <?php
                                                if($params->get('embl_is')){
                                                    echo $partic_home->getEmblem(true, 0, 'emblInline', 0, $cItemId);
                                                }
                                                ?>
                                            </span>
                                            <span class="team-name">
                                                <?php
                                                echo jsHelper::nameHTML($partic_home->getName(true, $cItemId));
                                                ?>
                                            </span>
                                        </div>

                                        <div class="match-result">
                                            <?php
                                            echo jsHelper::getScore($match,'','',$cItemId);
                                            ?>
                                        </div>


                                        <div class="away-team team">
                                            <span class="team-logo">
                                                <?php
                                                if($params->get('embl_is')){
                                                    echo $partic_away->getEmblem(true, 0, 'emblInline', 0, $cItemId);
                                                }
                                                ?>
                                            </span>
                                            <span class="team-name">
                                                <?php
                                                echo jsHelper::nameHTML($partic_away->getName(true, $cItemId));
                                                ?>
                                            </span>
                                        </div>
                                    </div>

                            
                    <?php if($enbl_slider){?>
                            </div>
                        </li>
                    <?php }?>    

                
                    <?php

                }
                ?>
                    <?php if($enbl_slider){?>
                        </ul>
                    <?php }else{ ?>
                        </div>
                    </div>
                    <?php } ?>
        </div>