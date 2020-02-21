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

class jsHelperTabs
{
    /*
     * $tabs array
     * $tabs['id'] - string
     * $tabs['title'] - string
     * $tabs['body'] - text
     */
    public static function draw($tabs, $rows, $viewname = '')
    {
        if (count($tabs)) {
            $jscurtab = classJsportRequest::get('jscurtab');
            if ($jscurtab && substr($jscurtab, 0, 1) != '#') {
                $jscurtab = '#'.$jscurtab;
            }
            ?>
        <div class="tabs">    
            <?php
            if (count($tabs) > 1) {
                ?>
            
            <ul class="nav nav-tabs">
              <?php
              $is_isset_tab = false;
                for ($intA = 0; $intA < count($tabs); ++$intA) {
                    if ($jscurtab == '#'.$tabs[$intA]['id']) {
                        $is_isset_tab = true;
                    }
                }
                if (!$is_isset_tab) {
                    $jscurtab = '';
                }
                for ($intA = 0; $intA < count($tabs); ++$intA) {
                    $tab_ico = isset($tabs[$intA]['ico']) ? $tabs[$intA]['ico'] : tableS;
                    ?>
                <li <?php echo (($intA == 0 && !$jscurtab) || ($jscurtab == '#'.$tabs[$intA]['id'])) ? 'class="active"' : '';
                    ?>><a data-toggle="tab" href="#<?php echo $tabs[$intA]['id'];
                    ?>"><i class="hidden-xs <?php echo $tab_ico;
                    ?>"></i> <?php echo $tabs[$intA]['title'];
                    ?></a></li>
              <?php 
                }
                ?>
              
            </ul>
            <?php

            }
            ?>
            <div class="tab-content">
                <?php
                for ($intAi = 0; $intAi < count($tabs); ++$intAi) {
                    ?>
                    <div id="<?php echo $tabs[$intAi]['id'];
                    ?>" class="tab-pane fade<?php echo (($intAi == 0 && !$jscurtab) || ($jscurtab == '#'.$tabs[$intAi]['id'])) ? ' in active' : ' in';
                    ?>">
                        <?php if ($tabs[$intAi]['text']) {
    ?>
                            <p><?php echo $tabs[$intAi]['text'];
    ?></p>
                        <?php 
                        } else{
                            $app = JFactory::getApplication();
                                $templateName = $app->getTemplate();
                                $overtmpl =  JPATH_ROOT. DIRECTORY_SEPARATOR. 'templates'. DIRECTORY_SEPARATOR. $templateName .DIRECTORY_SEPARATOR . 'html' .DIRECTORY_SEPARATOR. 'com_joomsport' .DIRECTORY_SEPARATOR. $viewname . DIRECTORY_SEPARATOR . $tabs[$intAi]['body'];
                                if(is_file($overtmpl)){
                                    require_once $overtmpl;
                                }elseif (is_file(JS_PATH_VIEWS_ELEMENTS.$tabs[$intAi]['body'])) {
                            ?>
                                                    <?php require_once JS_PATH_VIEWS_ELEMENTS.$tabs[$intAi]['body'];
                            ?>
                                                <?php 
                                }
                        }
    
                    ?>
                    </div>
                <?php 
                }
            ?>
                
            </div>
        </div>
        <?php

        }
    }
}
