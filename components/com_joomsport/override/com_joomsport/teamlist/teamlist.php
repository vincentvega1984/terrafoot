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
    <?php
$html = '<div class="table-responsive">';
$html .= '<form role="form" method="post" lpformnum="1">';
        if (count($rows->lists['teamsObj'])) {
            $html .= '<div class="jstable">';

            for ($intA = 0; $intA < count($rows->lists['teamsObj']); ++$intA) {
                $team = $rows->lists['teamsObj'][$intA];

                $html .= '<div class="jstable-row">
                        <div class="jstable-cell">
                            <div class="jsDivLineEmbl">'
                                .($team->getEmblem(true, 0, ''))
                                .jsHelper::nameHTML($team->getName(true))

                            .'</div>
                            
                        </div>
                        
                    </div>';
            }

            $html .= '</div>';
        }
        //echo $html;

if (isset($lists['pagination']) && $lists['pagination']) {
    require_once JS_PATH_VIEWS.'elements'.DIRECTORY_SEPARATOR.'pagination.php';
    $html .= paginationView($lists['pagination']);
}
$html .= '</form>';
$html .= '</div>';
echo $html;
?>
</div>
