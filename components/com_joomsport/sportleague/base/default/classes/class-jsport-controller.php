<?php

class classJsportController
{
    private $task = null;
    private $model = null;
    public function __construct()
    {
        $this->task = classJsportRequest::get('task');
    }

    private function getModel()
    {
        if (!$this->task) {
            $this->task = 'seasonlist';
        } else {
        }
        require_once JS_PATH_OBJECTS.'class-jsport-'.$this->task.'.php';
        $class = 'classJsport'.ucwords($this->task);
        $this->model = new $class();
    }

    public function execute()
    {
        $this->getModel();
        $rows = $this->model->getRow();
        $lists = $this->model->lists;
        $view = $this->task;

        if (method_exists($this->model, 'getView')) {
            $view = $this->model->getView();
        }
        $options = isset($lists['options']) ? $lists['options'] : null;
        echo '<div id="joomsport-container">
                <div class="page-content">';
        echo jsHelper::JsHeader($options);
            //echo '<div class="under-module-header">';
            require_once JS_PATH_VIEWS.$view.'.php';
            //echo '</div>';
            echo '</div>';
        echo '</div>';
    }
}
