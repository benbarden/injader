<?php
  /*
    Injader Default Theme
    Header template
    Coded by Ben Barden
    
    Feel free to copy this theme and use it to build new themes.
    The whole point of it is to provide a basic functional site
    that can be modified to suit your needs.
    
    If you've got suggestions for this theme, please go to
    http://www.injader.com and you can post them in the
    Suggestions forum.
  */
  if (!defined('C_SYS_LATEST_VERSION')) {
    exit("Error: This file cannot be viewed directly.");
  }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title><?php print($CMS->TH->GetHeaderPageTitle()); ?> : <?php print($CMS->TH->GetHeaderSiteTitle()); ?></title>
<?php
  print($CMS->TH->GetHeaderMetaDesc());
  print($CMS->TH->GetHeaderMetaKeywords());
  print($CMS->TH->GetHeaderMetaGenerator());
  print($CMS->TH->GetHeaderSiteFeed());
  print($CMS->TH->GetHeaderAreaFeed());
  print($CMS->TH->GetHeaderArticleFeed());
  print($CMS->TH->GetHeaderCoreStyles());
  print($CMS->TH->GetHeaderAreaStyles());
  print($CMS->TH->GetHeaderScripts());
  print($CMS->TH->GetHeaderCustomTags());
?>
</head>
<body>
<?php print($CMS->TH->GetSysWrapperStart()); ?>
<table id="tplMajWrapper" class="wrapper" cellpadding="0" cellspacing="0" summary="Simple Three Column Layout">
<tr>
<td id="tplMajNavbar" class="header" colspan="3">
<?php
  if ($CMS->TH->GetNavigationCount() > 0) {
?>
<ul class="navtext">
<?php
    while ($CMS->TH->NextNavigationItem() < $CMS->TH->GetNavigationCount()) {
      if ($CMS->TH->GetNavigationID() == $CMS->TH->GetNavigationSelectedItem()) {
?>
<li id="uNv<?php print($CMS->TH->GetNavigationID()); ?>" class="on">
<a class="on" href="<?php print($CMS->TH->GetNavigationLink()); ?>"><?php print($CMS->TH->GetNavigationName()); ?></a>
</li>
<?php
      } else {
?>
<li id="uNv<?php print($CMS->TH->GetNavigationID()); ?>" class="off">
<a onmouseover="document.getElementById('uNv<?php print($CMS->TH->GetNavigationID()); ?>').className = 'on';" onmouseout="document.getElementById('uNv<?php print($CMS->TH->GetNavigationID()); ?>').className = 'off';" href="<?php print($CMS->TH->GetNavigationLink()); ?>"><?php print($CMS->TH->GetNavigationName()); ?></a>
</li>
<?php
      }
?>
<?php
    }
?>
</ul>
<?php
  }
?>
</td>
</tr>
<tr>
<td id="tplMajHeader" class="header" colspan="3">
<?php
  $strSearch = $CMS->TH->GetSysSearchForm();
  if ($strSearch) {
?>
<div style="float: right;">
<?php
    print($strSearch);
?>
</div>
<?php
  }
?>
<!-- Begin Header -->
<h1><?php print($CMS->TH->GetHeaderSiteTitle()); ?></h1>
<!-- End Header -->
</td>
</tr>
<tr>
<td id="tplMajLeftSide" class="sidebar">
<!-- Begin Left Sidebar -->
<?php
  /* ********************************************** */
  /* *              Subareas                        */
  /* ********************************************** */
  $intSubareaCount = $CMS->TH->GetSubareaCount();
  if ($intSubareaCount > 0) {
    $strSubareaWrapperStart = <<<SubareaWrapperStart
<div class="subarea-listing">
<h2>Subareas</h2>

SubareaWrapperStart;
    print($strSubareaWrapperStart);

  /* ********************************************** */
  /* *              Subarea Items                   */
  /* ********************************************** */

    while ($CMS->TH->NextSubareaItem() < $CMS->TH->GetSubareaCount()) {
      $strSubareaDesc = $CMS->TH->GetSubareaDesc();
      $intNumItems    = $CMS->AR->CountContentInArea($CMS->TH->GetSubareaID(), "Published");
      $intGraphicID   = $CMS->TH->GetSubareaGraphicID();
      $strLocation = "";
      if (($intGraphicID) && ($intGraphicID != '0')) {
        $arrGraphic  = $CMS->FL->GetFile($intGraphicID);
        /*
           Remove the slashes from the next line
           if you wish to display area graphics
           for your subareas.
        */
        //$strLocation = "http://".SVR_HOST.URL_ROOT.$arrGraphic['location'];
        $strTitle    = $arrGraphic['title'];
      } else {
        $strLocation = "";
      }
?>
<div class="<?php print($CMS->TH->GetSubareaItemClassList()); ?>" id="subarea-item<?php print($CMS->TH->GetSubareaID()); ?>">
<?php
      if ($strLocation) {
?>
<div style="float: right;"><img src="<?php print($strLocation); ?>" alt="<?php print($strTitle); ?>" /></div>
<?php
      }
?>
<span class="subarea-name"><a href="<?php print($CMS->TH->GetSubareaLinkURL()); ?>"><?php print($CMS->TH->GetSubareaName()); ?></a></span><br />
<?php
        if ($strSubareaDesc) {
?>
<span class="subarea-desc"><?php print($strSubareaDesc); ?></span>
<?php
        }
?>
</div>
<?php
    }

  /* ********************************************** */
  /* *              Subarea Wrapper End             */
  /* ********************************************** */

    $strSubareaWrapperEnd = <<<SubareaWrapperEnd
</div>

SubareaWrapperEnd;
    print($strSubareaWrapperEnd);
  } else {
    $strNoSubareas = <<<NoSubareas
<p>No subareas.</p>

NoSubareas;
    /*
       Remove the slashes from the next line
       if you wish to display a message
       when an area has no content
    */
    //print($strNoSubareas);
  }

  /* ******************************************************* */
  /* *                 New Content plugin                  * */
  /* ******************************************************* */
  $strNewContent = $CMS->TH->GetPlugin('$plgNewContent');
  $blnNewContentPlugin = $strNewContent ? true : false;
  if ($blnNewContentPlugin) {
    print($CMS->TH->GetPlugin('$plgNewContent'));
  }
?>
<!-- End Left Sidebar -->
</td>
<td id="tplMajContent" class="content">
