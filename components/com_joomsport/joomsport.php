<?php/**  http://www.BearDev.com */// no direct accessdefined('_JEXEC') or die('Restricted access');

$controller = JRequest::getVar('controller', null, '', 'cmd');//JRequest::getWord('controller');//die();$task = JRequest::getVar('task', null, 'default', 'cmd');$view = JRequest::getVar('view', null, 'default', 'cmd');$user_ctr = array(                'regplayer',                'playerreg_save',                'regteam',                'teamreg_save',                'add_comment',                'del_comment',                'join_season',                'joinme',                'joinmePaypl',                'confirm_invitings',                'reject_invitings',                'unreg_inviting',                'unreg_inviting_reject',                'match_inviting',                'jointeam',                'moderedit_umatchday',                'moderedit_umatch');

if (in_array($task, $user_ctr) || in_array($view, $user_ctr)) {
    $controller = 'users';
}

if ($controller) {
    $path = JPATH_COMPONENT.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.$controller.'.php';

    if (file_exists($path) && $controller) {
        require_once $path;
    } else {
        $controller = '';
        require_once JPATH_COMPONENT.DIRECTORY_SEPARATOR.'controller.php';
    }
} else {
    $controller = '';
    require_once JPATH_COMPONENT.DIRECTORY_SEPARATOR.'controller.php';
}

// Create the controller$classname = 'JoomsportController'.ucfirst($controller);

$controller = new $classname();

// Perform the Request task$controller->execute(JRequest::getVar('task', null, 'default', 'cmd'));

// Redirect if set by the controller$controller->redirect();
