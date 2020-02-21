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
class classJsportController
{
    private $task = null;
    private $model = null;
    public function __construct()
    {
        $this->task = classJsportRequest::get('task');
        if (!$this->task) {
            $this->task = classJsportRequest::get('view');
        }
    }

    private function getModel()
    {
        if (!$this->task) {
            $this->task = 'seasonlist';
        } else {
            if ($this->task == 'table') {
                $this->task = 'season';
            }
            if ($this->task == 'tournlist') {
                $this->task = 'tournament';
            }
        }
        require_once JS_PATH_OBJECTS.'class-jsport-'.$this->task.'.php';
        $class = 'classJsport'.ucwords($this->task);
        $this->model = new $class();
    }

    public function execute()
    {
        $params = JFactory::getApplication('site')->getParams();
        $jsformat = classJsportRequest::get('jsformat','');
        $this->getModel();
        $rows = $this->model->getRow();

        $lists = $this->model->lists;
        $view = $this->task;
        
        if($jsformat == 'xml'){
            if (method_exists($this->model, 'getXML')) {
                $this->model->getXML();
            }
            exit();
        }elseif($jsformat == 'json'){
            if (method_exists($this->model, 'getJSON')) {
                $this->model->getJSON();
            }
            exit();
        }
        
        if (method_exists($this->model, 'getView')) {
            $view = $this->model->getView();
        }
        $options = isset($lists['options']) ? $lists['options'] : null;
        $this->getSLHeader();
        echo '<div id="joomsport-container" class="jsIclass{jswhoareyou} '.htmlspecialchars($params->get('pageclass_sfx')).'">
                <div class="page-content jmobile{yuserid}">';
        echo jsHelper::JsHeader($options);
        $app = JFactory::getApplication();
        $templateName = $app->getTemplate();
        $overtmpl =  JPATH_ROOT. DIRECTORY_SEPARATOR. 'templates'. DIRECTORY_SEPARATOR. $templateName .DIRECTORY_SEPARATOR . 'html' .DIRECTORY_SEPARATOR. 'com_joomsport' .DIRECTORY_SEPARATOR. $view . DIRECTORY_SEPARATOR . $view.'.php';
            //echo '<div class="under-module-header">';
        
            if (is_file($overtmpl)) {
                require_once $overtmpl;
            }else
            if (is_file(JS_PATH_VIEWS.$view.'.php')) {
                require_once JS_PATH_VIEWS.$view.'.php';
            }
            //echo '</div>';
            echo '</div>';
        echo '</div>';
        $this->getSLFooter();
    }

    public function getSLHeader()
    {
        require_once JS_PATH_VIEWS.'elements'.DIRECTORY_SEPARATOR.'header.php';
    }
    public function getSLFooter()
    {
        require_once JS_PATH_VIEWS.'elements'.DIRECTORY_SEPARATOR.'footer.php';
    }
}
