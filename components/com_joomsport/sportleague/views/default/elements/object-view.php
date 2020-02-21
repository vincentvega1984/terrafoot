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
<div class="row">    
    <div class="col-xs-12 rmpadd" style="padding-right:0px;">
        <div class="jsObjectPhoto rmpadd">
            <div class="photoPlayer">

                    <?php echo jsHelperImages::getEmblemBig($rows->getDefaultPhoto());?>

                        

            </div>   
            <?php
            
            $optionsPl = array("oview" => isset($viewname)?$viewname:'', "id" => $rows->object->id);

            classJsportPlugins::get('addInfoAfterPhoto', $optionsPl);
            ?>
            
        </div>
        <?php
        $class = '';
        $extra_fields = jsHelper::getADF($rows->lists['ef']);
        if ($extra_fields) {
            $class = 'well well-sm';
        } else {
            ?>
            <div class="rmpadd" style="padding-right:0px;padding-left:15px;">
                <?php echo $rows->getDescription();
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
    </div>
    <?php if ($extra_fields) {
    ?>
    <div class="col-xs-12 rmpadd" style="padding-right:0px;">
        <?php echo $rows->getDescription();
    ?>
    </div>
    <?php 
} ?>
</div>    