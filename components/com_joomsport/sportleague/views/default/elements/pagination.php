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
<?php
function paginationView($pagination)
{
    $html = '';
    if (isset($pagination) && $pagination->pages > 1) {
        $additvars = '';
        if (count($pagination->additvars)) {
            foreach ($pagination->additvars as $key => $value) {
                $additvars .= '&'.$key.'='.$value;
            }
        }

        $html .= '<div class="js-div-pagination">';
        $current = $pagination->getCurrentPage();
        $html .=  '<nav>';
        $html .=  '<ul class="pagination">
            <li '.($current < 1 ? 'class="disabled"' : '').'>
              <a href="'.$pagination->link.$additvars.'" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
              </a>
            </li>';
        $firstSymb = '?';

        if (strpos($pagination->link,'?')) {
            $firstSymb = '&';
        }
        $start = ($current > 5 && $pagination->pages > 10) ? $current - 5 : 0;
        $end = ($pagination->pages - $current > 5 && $pagination->pages > 10) ? ($current > 10 ? $current + 5 : $start + 10) : $pagination->pages;
        for ($intP = $start; $intP < ($end); ++$intP) {
            $class = '';
            if ($current == ($intP)) {
                $class = ' class="active"';
            }
            $html .=  '<li '.$class.'><a href="'.$pagination->link.$firstSymb.'page='.($intP + 1).$additvars.'">'.($intP + 1).'</a></li>';
        }

        $html .= '<li '.($current == ($pagination->pages - 1)  ? 'class="disabled"' : '').'>
              <a href="'.$pagination->link.$firstSymb.'page='.($pagination->pages).$additvars.'" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
              </a>
            </li>
          </ul>
        </nav>';

        $html .= '</div>';
        $html .= '<div class="jsClear jsOverflowHidden">'.$pagination->getLimitBox().'</div>';
    }

    return $html;
}
?>
