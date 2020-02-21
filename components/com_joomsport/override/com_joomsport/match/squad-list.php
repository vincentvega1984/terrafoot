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
<div>
    <div class="center-block jscenter">
        <h3 class="solid"><?php echo classJsportLanguage::get('BLFA_LINEUP');?></h3>
    </div>
    <div class="jsOverflowHidden">
        <div class="jstable jsInline">
            <?php
            for ($intP = 0; $intP < count($rows->lists['squard1']); ++$intP) {
                ?>
                <div class="jstable-row">
                    <div class="jstable-cell width5prc" >
                        <?php echo jsHelperImages::getEmblem($rows->lists['squard1'][$intP]->obj->getDefaultPhoto(), 0, '');
                ?>
                    </div>
                    <div class="jstable-cell jsTextAlignLeft">
                        <?php echo $rows->lists['squard1'][$intP]->obj->getName(true);
                ?>
                    </div>
                    <div class="jstable-cell">
                        <?php

                        if ($rows->lists['squard1'][$intP]->player_out) {
                            echo '<img src="'.JS_LIVE_URL.'components/com_joomsport/img/ico/out-new.png" class="sub-player-ico" title="" alt="" />';
                            if ($rows->lists['squard1'][$intP]->minutes) {
                                echo '&nbsp;'.$rows->lists['squard1'][$intP]->minutes."'";
                            }
                        }
                ?>
                    </div>
                </div>    
                <?php

            }
            if (!count($rows->lists['squard1'])) {
                echo '&nbsp;';
            }
            ?>
        </div>
        <div  class="jstable jsInline">
            <?php
            for ($intP = 0; $intP < count($rows->lists['squard2']); ++$intP) {
                ?>
                <div class="jstable-row">
                    <div class="jstable-cell width5prc">
                        <?php echo jsHelperImages::getEmblem($rows->lists['squard2'][$intP]->obj->getDefaultPhoto(), 0, '');
                ?>
                    </div>
                    <div class="jstable-cell jsTextAlignLeft">
                        <?php echo $rows->lists['squard2'][$intP]->obj->getName(true);
                ?>
                    </div> 
                    <div class="jstable-cell">
                        <?php

                        if ($rows->lists['squard2'][$intP]->player_out) {
                            echo '<img src="'.JS_LIVE_URL.'components/com_joomsport/img/ico/out-new.png" class="sub-player-ico" title="" alt="" />';
                            if ($rows->lists['squard2'][$intP]->minutes) {
                                echo '&nbsp;'.$rows->lists['squard2'][$intP]->minutes."'";
                            }
                        }
                ?>
                    </div>
                </div>    
                <?php

            }
            ?>
        </div>
    </div>
</div>
<?php if (count($rows->lists['squard1_res']) || count($rows->lists['squard2_res'])) {
    ?>
<div>
    <div class="center-block jscenter">
        <h3 class="solid"><?php echo classJsportLanguage::get('BLFA_SUBSTITUTES');
    ?></h3>
    </div>    
    <div class="jsOverflowHidden">
        <div class="jstable jsInline">
            <?php
            for ($intP = 0; $intP < count($rows->lists['squard1_res']); ++$intP) {
                ?>
                <div class="jstable-row">
                    <div class="jstable-cell width5prc">
                        <?php echo jsHelperImages::getEmblem($rows->lists['squard1_res'][$intP]->obj->getDefaultPhoto(), 0, '');
                ?>
                    </div>
                    <div class="jstable-cell jsTextAlignLeft">
                        <?php echo $rows->lists['squard1_res'][$intP]->obj->getName(true);
                ?>
                    </div>
                    <div class="jstable-cell">
                        <?php

                        if ($rows->lists['squard1_res'][$intP]->player_in) {
                            echo '<img src="'.JS_LIVE_URL.'components/com_joomsport/img/ico/in-new.png" class="sub-player-ico" title="" alt="" />';
                            if ($rows->lists['squard1_res'][$intP]->minutes) {
                                echo '&nbsp;'.$rows->lists['squard1_res'][$intP]->minutes."'";
                            }
                        }
                ?>
                    </div>
                </div>    
                <?php

            }
    if (!count($rows->lists['squard1_res'])) {
        echo '&nbsp;';
    }
    ?>
        </div>
        <div  class="jstable jsInline">
            <?php
            for ($intP = 0; $intP < count($rows->lists['squard2_res']); ++$intP) {
                ?>
                <div class="jstable-row">
                    <div class="jstable-cell width5prc">
                        <?php echo jsHelperImages::getEmblem($rows->lists['squard2_res'][$intP]->obj->getDefaultPhoto(), 0, '');
                ?>
                    </div>
                    <div class="jstable-cell jsTextAlignLeft">
                        <?php echo $rows->lists['squard2_res'][$intP]->obj->getName(true);
                ?>
                    </div> 
                    <div class="jstable-cell">
                        <?php

                        if ($rows->lists['squard2_res'][$intP]->player_in) {
                            echo '<img src="'.JS_LIVE_URL.'components/com_joomsport/img/ico/in-new.png" class="sub-player-ico" title="" alt="" />';
                            if ($rows->lists['squard2_res'][$intP]->minutes) {
                                echo '&nbsp;'.$rows->lists['squard2_res'][$intP]->minutes."'";
                            }
                        }
                ?>
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