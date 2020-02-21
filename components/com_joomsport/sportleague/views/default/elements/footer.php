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
<script src="<?php echo JS_LIVE_ASSETS;?>js/lightbox.js"></script>
<script type="text/javascript">
jQuery(document).ready(function() {

  
    jQuery("body").tooltip(
            { 
                selector: '[data-toggle2=tooltipJSF]',
                html:true
            });
            
});
/*jQuery(function() {
    jQuery( 'div[data-toggle2=tooltipJSF]' ).tooltip({
        html:true
    });    
});*/


jQuery(function() {
    jQuery( '.jstooltipJSF' ).tooltip({
        html:true,
      position: {
        my: "center bottom-20",
        at: "center top",
        using: function( position, feedback ) {
          jQuery( this ).css( position );
          jQuery( "<div>" )
            .addClass( "arrow" )
            .addClass( feedback.vertical )
            .addClass( feedback.horizontal )
            .appendTo( this );
        }
      }
    });
  });
</script>

