<div class="joomsport-matchday-view">

            <ul class="joomsport-matchday-view__list">
                <?php
                foreach ($list as $match) {
                    $partic_home = $match->getParticipantHome();
                    $partic_away = $match->getParticipantAway();
                    $match_date = classJsportDate::getDate($match->object->m_date, $match->object->m_time);
                   ?>
                <li class="joomsport-matchday-view__item">
                    <div class="joomsport-matchday-view__match"
                    <?php if($match->object->m_played > 1 || $match->object->m_played == 0){ ?>
                        <?php echo 'attr-played="await"' ?>
                    <?php }else{ ?>
                        <?php echo 'attr-played="finished"' ?>
                    <?php } ?>
                    >
                        <?php
                        if($params->get('seasonname_is')){
                            $seasObj =  new modelJsportSeason($match->season_id);
                            echo '<div class="joomsport-matchday-view__match-season">'.$seasObj->getName().'</div>';
                        }
                        ?>
                        <div class="joomsport-matchday-view__match-date">
                            <?php echo $match_date;?>
                            <?php if($match->object->venue_id && $params->get('venue_is')){
                                $venue_name = $match->getLocation(false);
                                ?>
                                <a class="joomsport-matchday-view__match-venue" href="<?php echo classJsportLink::venue($venue_name, $match->object->venue_id, true, $cItemId);?>" title="<?php echo $match->getLocation(false);?>"><img src="<?php echo JS_LIVE_ASSETS;?>/images/location.png" /></a>
                            <?php } ?>
                        </div>
                        <div class="joomsport-matchday-view__match-teams">
                            <div class="joomsport-matchday-view__team-home">
                                <div class="joomsport-matchday-view__team-logo">
                                    <?php
                                    if($params->get('embl_is')){
                                        echo $partic_home->getEmblem(true, 0, 'emblInline', 0, $cItemId);
                                    }
                                    ?>
                                </div>
                                <div class="joomsport-matchday-view__team-name">
                                    <?php
                                    echo jsHelper::nameHTML($partic_home->getName(true, $cItemId));
                                    ?>
                                </div>
                                <?php
                                if($match->object->m_played > 1 || $match->object->m_played == 0){
                                ?>
                                <div class="joomsport-matchday-view__team-scores">
                                    <?php echo jsHelper::getScore($match,'','',$cItemId); ?>
                                </div>
                                <?php
                                }else{
                                    ?>
                                <div class="joomsport-matchday-view__team-scores">
                                    <?php
                                    if($match->object->m_played){    
                                        echo '<div class="scoreScrMod">'.classJsportLink::match($match->object->score1, $match->object->id,false,'',$cItemId).'</div>';
                                    }
                                    ?>
                                </div>
                                    <?php
                                }
                                ?>
                            </div>
                            <div class="joomsport-matchday-view__team-away">
                                <div class="joomsport-matchday-view__team-logo">
                                    <?php
                                    if($params->get('embl_is')){
                                        echo $partic_away->getEmblem(true, 0, 'emblInline', 0,  $cItemId);
                                    }
                                    ?>
                                </div>
                                <div class="joomsport-matchday-view__team-name">
                                    <?php echo jsHelper::nameHTML($partic_away->getName(true, $cItemId)); ?>
                                </div>
                                <?php
                                    if($match->object->m_played > 1  || $match->object->m_played == 0){
                                        
                                    }else{
                                ?>
                                <div class="joomsport-matchday-view__team-scores">
                                    <?php
                                    if($match->object->m_played > 1){
                                        echo jsHelper::getScore($match,'','',$cItemId);
                                    }else if($match->object->m_played){
                                        echo '<div class="scoreScrMod">'.classJsportLink::match($match->object->score2, $match->object->id,false,'',$cItemId).'</div>';
                                    }
                                    ?>
                                </div>
                                <?php
                                }
                                ?>
                            </div>
                        </div>

                    </div>

                </li>
                    <?php

                }
                ?>
            </ul>
        </div>