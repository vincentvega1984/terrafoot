<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
/**
 * HTML View class for the Registration component.
 *
 * @since 1.0
 */
class joomsportViewadmin_team extends JViewLegacy
{
    public $_model = null;
    public function __construct(&$model)
    {
        $this->_model = $model;
    }
    public function display($tpl = null)
    {
        $this->_model->getData();
        $lists = $this->_model->_lists;

        $params = $this->_model->_params;
        $row = $this->_model->_data;
        $page = $this->_model->_pagination;
        $s_id = $this->_model->season_id;

        $this->assignRef('params',        $params);
        $this->assignRef('rows',        $row);
        $this->assignRef('page',        $page);
        $this->assignRef('s_id',        $s_id);
        $this->assignRef('lists', $lists);

        require_once dirname(__FILE__).'/tmpl/default'.$tpl.'.php';
    }
}
