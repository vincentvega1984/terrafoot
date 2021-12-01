<?php
// защита от прямого доступа к файлу
defined('_JEXEC') or die;
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" >
<head>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-131023173-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-131023173-1');
</script>
<meta charset=utf-8 />
<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport">
<jdoc:include type="head" />

<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/template.css" type="text/css"  />
</head>

<?php
  $itemid = JRequest::getVar('Itemid');
  $menu = &JFactory::getApplication()->getMenu();
  $active = $menu->getItem($itemid);
  $params = $menu->getParams( $active->id );
  $pageclass = $params->get( 'pageclass_sfx' );
?>

<body class="<?php echo $pageclass ? htmlspecialchars($pageclass) : 'default'; ?>">

<!--header-->
<header>
    <div class="top-info">
        <div class="container">
            <jdoc:include type="modules" name="topinfo" style="xhtml" />
        </div>
    </div>
    <div class="topbar">
        <div class="container">
            <div class="header-wrapper">
                <div class="trigger-mobile">
                    <jdoc:include type="modules" name="trigger-mobile" style="xhtml" />
                </div>
                <div class="logo">
                    <jdoc:include type="modules" name="logo" style="xhtml" />
                </div>
                <div class="contacts">
                    <jdoc:include type="modules" name="contacts" style="xhtml" />
                </div>
            </div>
        </div>
        <nav class="mainmenu">
            <div class="container">
                <div class="mainmenu-wrapper">
                    <jdoc:include type="modules" name="mainmenu" style="xhtml" />
                </div>
            </div>
        </nav>
    </div>
</header>
<!--end of header-->

<!--slider-->
<?php if ($this->countModules( 'slider' )) : ?>
    <div class="slider">
        <jdoc:include type="modules" name="slider" style="xhtml" />
        
        <!--For winter slider snow only-->
         <div class="winter-is-coming">
            <div class="snow snow--near"></div>
            <div class="snow snow--near snow--alt"></div>
            <div class="snow snow--mid"></div>
            <div class="snow snow--mid snow--alt"></div>
            <div class="snow snow--far"></div>
            <div class="snow snow--far snow--alt"></div>
        </div> 

    </div>
<?php endif; ?>
<!--end of slider-->

<!--slider-bottom-->
<?php if ($this->countModules( 'slider-bottom' )) : ?>
    <div class="slider-bottom">
        <div class="container">
            <jdoc:include type="modules" name="slider-bottom" style="xhtml" />
        </div>
    </div>
<?php endif; ?>
<!--end of slider-bottom-->

<?php if ($this->countModules( 'top-1' )) : ?>
    <div class="top-1">
        <div class="container">
            <jdoc:include type="modules" name="top-1" style="xhtml" />
        </div>
    </div>
<?php endif; ?>

<?php if ($this->countModules( 'top-2' )) : ?>
    <div class="top-2" data-parallax="scroll" data-image-src="<?php echo $this->baseurl; ?>/images/parallax/parallax-1.jpg">
        <div class="container">
            <jdoc:include type="modules" name="top-2" style="xhtml" />
        </div>
    </div>
<?php endif; ?>

<?php if ($this->countModules( 'top-3' )) : ?>
    <div class="top-3" data-parallax="scroll" data-image-src="<?php echo $this->baseurl; ?>/images/parallax/parallax-2.jpg">
        <div class="container">
            <jdoc:include type="modules" name="top-3" style="xhtml" />
        </div>
    </div>
<?php endif; ?>

<!--top_block-->


<?php if ($this->countModules( 'top-4' )) : ?>
    <div class="top-4">
        <div class="container">
            <jdoc:include type="modules" name="top-4" style="xhtml" />
        </div>
    </div>
<?php endif; ?>
<!--end of top block-->

<!--mainpage-->
<div class="maincontent">
    <?php if ($this->countModules( 'breadcrumbs' )) : ?>
          <div class="breadcrumbs">
            <div class="container">
              <jdoc:include type="modules" name="breadcrumbs" style="xhtml" />
            </div>
        </div>
    <?php endif; ?>

    <div class="container">
    	<jdoc:include type="component" />
    </div>
</div>
<!--end of mainpage-->

<!--bottom-block-->
  <?php if ($this->countModules( 'bottom-1' )) : ?>
    <div class="bottom-1">
      <div class="container">
        <jdoc:include type="modules" name="bottom-1" style="xhtml" />
      </div>
    </div>
  <?php endif; ?>

  <?php if ($this->countModules( 'bottom-2' )) : ?>
    <div class="bottom-2">
        <jdoc:include type="modules" name="bottom-2" style="xhtml" />
    </div>
  <?php endif; ?>

  <?php if ($this->countModules( 'bottom-3' )) : ?>
    <div class="bottom-3">
        <div class="container">
            <jdoc:include type="modules" name="bottom-3" style="xhtml" />
        </div>
    </div>
  <?php endif; ?>
<!--end of bottom block-->


<!--footer-->

    <div class="footer">
        <div class="footer-top">
            <div class="container">
                <jdoc:include type="modules" name="footer-top" style="xhtml" />
            </div>
        </div>
        <div class="footer-middle">
            <div class="container">
                <jdoc:include type="modules" name="footer-middle" style="xhtml" />
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container">
                <jdoc:include type="modules" name="footer-bottom" style="xhtml" />
            </div>
        </div>
    </div>

<!--end of footer-->
<div class="overlay-mobile"></div>
<a href="javascript:" id="return-to-top"></a>

<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/slick.min.js"></script>
<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/jquery.sticky.js"></script>
<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/parallax.min.js"></script>
<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/wow.min.js"></script>
<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/jquery.cookie.js"></script>
<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/jquery.tablesorter.js"></script>
<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/jquery.tablesorter.widgets.js"></script>
<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/main.js"></script>
</body>
</html>
