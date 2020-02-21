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
<div class="player-main-info">    
    <div class="player-main-info-wrapper">
        <div class="photoPlayer">
            <?php echo jsHelperImages::getEmblemBig($rows->getDefaultPhoto());?>
            <a href="" class="player-team"><img src="" alt=""></a>
        </div> 
    <?php
        $class = '';
        $extra_fields = jsHelper::getADF($rows->lists['ef']);
        if ($extra_fields) {
            $class = 'well well-sm';
        } else { ?>
            <div class="rmpadd"> 
                <?php echo $rows->getDescription(); ?> 
            </div>

            <?php } ?>

<?php if ($extra_fields) {
    ?>
    <?php 
    } ?>
        
        <div class="<?php echo $class;?> pt10 extrafldcn">
            <?php echo $extra_fields; ?>
        </div>
    </div>
    <div class="descriptionPlayer">
            <?php echo $rows->getDescription();?>
        </div>
</div>    