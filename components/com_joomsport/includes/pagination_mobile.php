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
defined('_JEXEC') or die;

class JS_Pagination
{
    public $pcount = 0;
    public $limit = 0;
    public $pages = 1;
    public $page = 0;
    public $show_links = 10;
    public $limit_array = array(5, 10, 20, 50, 100, 0);

    public function __construct($count, $page = 1, $limit = 20)
    {
        $this->pcount = $count;
        $this->limit = $limit;
        $this->page = $page;
        if ($this->pcount > $this->limit && $this->limit) {
            $this->pages = ceil($this->pcount / $this->limit);
        }
    }

    public function getPageLinks($link, $val = '')
    {
        $kl = '';
        if ($this->pages > 1) {
            $kl .= '<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 text-center"><nav>';
            $kl .= '<ul class="pagination">';

            if ($this->page == 1) {
                $kl .= '<li class="disabled"><a>«</a></li>';
            } else {
                $link_li = $link.'&page'.$val.'='.($this->page - 1);
                $kl .= "<li><a href='".JRoute::_($link_li)."'>«</a></li>";
            }
            $start = ($this->page < ceil($this->show_links / 2)) ? 1 : ($this->page - ceil($this->show_links / 2));
            $start = $start ? $start : 1;
            $end = $start + ($this->show_links);

            for ($i = $start; $i < $end; ++$i) {
                if ($this->pages >= $i) {
                    $link_li = $link.'&page'.$val.'='.$i;
                    if ($this->page == $i) {
                        $kl .= "<li  class='active'><a href='".JRoute::_($link_li)."'>".$i.'<span class="sr-only">(current)</span></a></li>';
                    } else {
                        $kl .= "<li><a href='".JRoute::_($link_li)."'>".$i.'</a></li>';
                    }
                }
            }

            if ($this->page == $this->pages) {
                $kl .= '<li class="disabled"><a>»</a></li>';
            } else {
                $link_li = $link.'&page'.$val.'='.($this->page + 1);
                $kl .= "<li><a href='".JRoute::_($link_li)."'>»</a></li>";
            }
            $kl .= '</ul>';
            $kl .= '</nav></div>';
        }

        return $kl;
    }

    public function getLimitBox($val = '')
    {
        $kl = '<div class="display col-xs-12 col-sm-12 col-md-2 col-lg-2 text-right" style="min-width: 170px; float: right;"><label>'.JText::_('BL_TAB_DISPLAY').'</label>';
        $jas = 'onchange = "document.adminForm.submit();"';
        foreach ($this->limit_array as $lim) {
            $limbox[] = JHTML::_('select.option', $lim, $lim ? $lim : JText::_('BLFA_ALL'), 'id', 'name');
        }
        $kl .= JHTML::_('select.genericlist', $limbox, 'jslimit'.$val, 'class="form-control pull-right" size="1" '.$jas, 'id', 'name', $this->limit);
        $kl .= '</div>';

        return $kl;
    }

    public function getLimitPage()
    {
        $kl = '<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">'.JText::_('BL_TAB_PAGE').' '.$this->page.' '.JText::_('BL_TAB_OF').' '.$this->pages.'</div>';

        return $kl;
    }
}
