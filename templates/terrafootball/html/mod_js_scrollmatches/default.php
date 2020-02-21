<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_banners
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
if(count($list)){
$document		= JFactory::getDocument();
$document->addScript(JURI::root() . 'modules/mod_js_scrollmatches/js/jquery.jcarousellite.min.js');
$baseurl = JUri::base();

$module_id = $module->id;
$enbl_slider = $params->get('enbl_slider');
$classname = $enbl_slider ? "jsSliderContainer":"jsDefaultContainer";
$match_count = count($list);
if($enbl_slider){
    $curpos = ModJSScrollMatchesHelper::getStartedIndex($list);
    $curpos = $curpos > 1 ? $curpos-1 : 0;
}
$cItemId = $params->get('customitemid');
$Itemid = JRequest::getInt('Itemid');
if(!$cItemId){
	$cItemId = $Itemid;
}
?>

<div class="<?php echo $classname;?>">
<div class="jsmainscroll jsrelatcont">
    <div>
        <?php
        require JModuleHelper::getLayoutPath('mod_js_scrollmatches', $params->get('chosenview', 'default_view1'));
        ?>
    </div>
    <?php if($enbl_slider){?>
  
    <?php
     }
    ?>
</div>
</div>    
<?php

}