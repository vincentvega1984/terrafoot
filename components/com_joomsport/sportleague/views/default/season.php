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
<div class="seasonTable">
    <div class="jsOverflowHidden" style="padding:0px 15px;">

        <?php
        if ($rows->object->tourn_logo) {
            ?>

                <div class="jsObjectPhoto rmpadd">

                        <?php echo jsHelperImages::getEmblemBig($rows->object->tourn_logo, 1, 'emblInline', '150', false);
            ?>



                </div>    

            <?php

        }
        ?>

            <?php
            $class = '';
            $extra_fields = jsHelper::getADF($rows->lists['ef']);
            if ($extra_fields) {
                $class = 'well well-sm';
            } else {
                ?>
                <div class="rmpadd" style="padding-right:0px;padding-left:15px;">
                    <?php echo $rows->object->tourn_descr;
                ?>
                </div>
                <?php

            }
            ?>
            <div class="<?php echo $class;?> pt10 extrafldcn">
                <?php

                    echo $extra_fields;
                ?>
            </div>
            <?php

            if ($rows->object->tourn_descr && $extra_fields) {
                echo '<div class="col-xs-12 rmpadd" style="padding-right:0px;">';
                echo $rows->object->tourn_descr;
                echo '</div>';
            }

            ?>


</div>
    <div>
        <?php
        //require_once JS_PATH_VIEWS_ELEMENTS . 'table-group.php';

        $tabs = $rows->getTabs();
        jsHelperTabs::draw($tabs, $rows);

        ?>
    </div>
    <div>
        <div>
            <?php
            if (isset($rows->season->lists['playoffs'])) {
                echo jsHelper::getMatches($rows->season->lists['playoffs']);
            }
            ?>
        </div>
    </div>
    <div class="jsClear"></div>
    <?php
    global $jsConfig;
    ?>
    <?php if ($jsConfig->get('jsbrand_on') == 1):?>
    <br />
    <div id="copy" class="copyright"><a href="http://joomsport.com">JoomSport - sport Joomla league</a></div> 
    <?php endif;?>
     <div class="jsClear"></div>
</div>
