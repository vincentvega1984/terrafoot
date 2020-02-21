<?php

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

class JoomsportController extends JControllerLegacy
{
    protected $js_prefix = '';
    protected $mainframe = null;
    protected $option = 'com_joomsport';

    public function __construct()
    {
        parent::__construct();
    }

    public function display($cachable = false, $urlparams = false)
    {
        $db = JFactory::getDBO();
        //load languages for addons
        $query = "SELECT options FROM #__bl_addons WHERE published='1' AND options != ''";
        $db->setQuery($query);
        $addons = $db->loadColumn();
        $html = '';
        for($intA=0;$intA<count($addons);$intA++){
            $options = json_decode($addons[$intA], true);
            if(isset($options['langugesFE'])){
                $lang = JFactory::getLanguage();
                $extension = $options['langugesFE'];

                $reload = true;
                $lang->load($extension);
            }
        }   
        //end lang
        
        $vName = $this->input->getCmd('view', '');
        if (!$vName) {
            $vName = $this->input->getCmd('task', 'seasonlist');
        }
        $this->input->set('view', $vName);
        parent::display($cachable);

        return $this;
    }
        /*public function plugins(){
            require_once 'components/com_joomsport/sportleague/sportleague.php';
            require_once JS_PATH_CLASSES . 'class-jsport-plugins.php';
            $arguments = $_GET;
            $plugfunc = (isset($_GET["plugfunc"]) && $_GET["plugfunc"]) ? $_GET["plugfunc"] : "";
            
            if($plugfunc){
                classJsportPlugins::get($plugfunc, $arguments);
            }
        }*/
}
