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

require_once JS_PATH_MODELS.'model-jsport-venue.php';

class classJsportVenue
{
    private $id = null;
    private $season_id = null;
    public $object = null;
    public $lists = null;

    const VIEW = 'common';

    public function __construct($id = 0, $season_id = null)
    {
        if (!$id) {
            $this->season_id = (int) classJsportRequest::get('sid');
            $this->id = (int) classJsportRequest::get('id');
        } else {
            $this->season_id = $season_id;
            $this->id = $id;
        }
        if (!$this->id) {
            die('ERROR! Venue ID not DEFINED');
        }

        $this->loadObject();
    }

    private function loadObject()
    {
        $obj = new modelJsportVenue($this->id, $this->season_id);
        $this->object = $obj->getRow();
        if ($this->object) {
            $this->lists = $obj->loadLists();
        }
    }

    public function getObject()
    {
        $this->setHeaderOptions();

        return $this->object;
    }

    public function getName($linkable = false)
    {
        $html = '';
        if (!$this->object) {
            return '';
        }
        if (!$linkable) {
            return $this->object->v_name;
        }
        if ($this->id > 0) {
            $html = classJsportLink::venue($this->object->v_name, $this->id, false, '');
        }

        return $html;
    }

    public function getDefaultPhoto()
    {
        if(!$this->lists['def_img']){
            return JSCONF_VENUE_DEFAULT_IMG;
        }
        return $this->lists['def_img'];
    }

    public function getRow()
    {
        return $this;
    }
    public function getDescription()
    {
        return classJsportText::getFormatedText($this->object->v_descr).$this->getVenueLocation();
    }
    public function getView()
    {
        return self::VIEW;
    }

    public function getTabs()
    {
        $tabs = array();
        $intA = 0;
        //main tab
        $tabs[$intA]['id'] = 'stab_main';
        $tabs[$intA]['title'] = classJsportLanguage::get('BLFA_VENUE');
        $tabs[$intA]['body'] = 'object-view.php';
        $tabs[$intA]['text'] = '';
        $tabs[$intA]['class'] = '';
        $tabs[$intA]['ico'] = 'flag';

        //photos
        if (count($this->lists['photos'])) {
            ++$intA;
            $tabs[$intA]['id'] = 'stab_photos';
            $tabs[$intA]['title'] = classJsportLanguage::get('BL_TAB_PHOTOS');
            $tabs[$intA]['body'] = 'gallery.php';
            $tabs[$intA]['text'] = '';
            $tabs[$intA]['class'] = '';
            $tabs[$intA]['ico'] = 'photos';
        }

        return $tabs;
    }

    public function getVenueLocation()
    {

        $html = '';
        if (isset($this->object->v_coordx) && isset($this->object->v_coordy)) {
            $html .= '<div class="map">';

            if ($this->object->v_coordx && $this->object->v_coordy) {
                $html .= '<div id="venue_gmap"></div>
                <script type="text/javascript">
                    function initialize() {
                        var myLatlng = new google.maps.LatLng('.$this->object->v_coordx.', '.$this->object->v_coordy.');

                        var myOptions = {
                            zoom: 12,
                            center: myLatlng,
                            mapTypeId: google.maps.MapTypeId.ROADMAP
                        }
                        var map = new google.maps.Map(document.getElementById("venue_gmap"), myOptions);
                        var marker = new google.maps.Marker({
                            position: myLatlng,
                            title:"'.htmlspecialchars($this->object->v_name).'"
                        });

                        // To add the marker to the map, call setMap();
                        marker.setMap(map);
                    }

                    function loadScriptik() {
                        var script = document.createElement("script");
                        script.type = "text/javascript";
                        script.src = "https://maps.google.com/maps/api/js?callback=initialize&key='.JSCONF_GMAP_API_KEY.'";
                        document.body.appendChild(script);
                    }

                    window.onload = loadScriptik;
                </script>';
            }

            $html .= '</div>';
        }

        return $html;
    }
    public function setHeaderOptions()
    {
        global $jsConfig;
        //social
        //social
        if ($jsConfig->get('jsbp_venue') == '1') {
            $this->lists['options']['social'] = true;
            classJsportAddtag::addCustom('og:title', $this->getName(false));
            $img = $this->getDefaultPhoto();
            if (is_file(JS_PATH_IMAGES.$img)) {
                classJsportAddtag::addCustom('og:image', JS_LIVE_URL_IMAGES.$img);
            }
            classJsportAddtag::addCustom('og:description', $this->getDescription());
        }
    }
}
