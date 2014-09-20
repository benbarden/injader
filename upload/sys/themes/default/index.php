<?php
  /*
    Injader Default Theme
    Index Template
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

  /* ********************************************** */
  /* *              Page Wrapper                    */
  /* ********************************************** */

  $strBreadcrumbs = $CMS->TH->GetSysBreadcrumbs();
  if ($strBreadcrumbs != $strAreaName) {
    print($strBreadcrumbs);
  }
?>
<h1><?php print($strAreaName); ?></h1>
<?php
  if ($strAreaDesc) {
    print($strAreaDesc);
  }
?>
<?php print($strPageLinks); ?>
<?php
  if ($CMS->TH->GetSysWriteLink()) {
    $strAddText = "New Article";
?>
    <a href="<?php print($CMS->TH->GetSysWriteLink()); ?>"><?php print($strAddText); ?></a>
<?php
  }
?>
<?php

  /* ********************************************** */
  /* *              Content Wrapper                 */
  /* ********************************************** */

  if ($CMS->TH->GetContentCount() > 0) {
    $strContentWrapperStart = <<<ContentWrapperStart
<div class="content-listing">

ContentWrapperStart;
    print($strContentWrapperStart);

  /* ********************************************** */
  /* *              Content Items                   */
  /* ********************************************** */

    while ($CMS->TH->NextContentItem() < $CMS->TH->GetContentCount()) {
  /* ******************************************************* */
  /* *              Thumbnail Display                        */
  /* * Articles with a JPG or PNG attachment can display the */
  /* * attachment as a thumbnail. To disable this, uncomment */
  /* * the first line (remove the // at the start) and       */
  /* * comment out the other lines. To use a different size  */
  /* * for the thumbnail, comment out the Medium or Large    */
  /* * line shown below. Ensure only one of the four lines   */
  /* * is uncommented or the last of the four options will   */
  /* * be used.                                              */
  /* ******************************************************* */
  //$strThumbnail = "";
  $strThumbnail = $CMS->TH->GetContentThumbSmall();
  //$strThumbnail = $CMS->TH->GetContentThumbMedium();
  //$strThumbnail = $CMS->TH->GetContentThumbLarge();
  if ($strThumbnail) {
?>
<div style="border: 1px solid #000; float: right;"><?php print($strThumbnail); ?></div>
<?php
  }
  /* ********************************************** */
  /* *              Content Detail                  */
  /* ********************************************** */
?>
<div class="item-content" id="item<?php print($CMS->TH->GetContentID()); ?>"> 
<?php
  $intAvatarID = $CMS->TH->GetContentAuthorAvatarID();
  if ($intAvatarID) {
    $strAvatarURL = FN_FILE_DOWNLOAD."?id=$intAvatarID";
?>
<div class="comment-avatar">
<img src="<?php print($strAvatarURL); ?>" alt="<?php print($CMS->TH->GetContentAuthorName()); ?>'s avatar" />
</div>
<?php
  }
?>
<h2><?php
  print($CMS->TH->GetContentIsUnread() ? "<b>New</b> " : "");
?><a href="<?php print($CMS->TH->GetContentLinkURL()); ?>" class="item-title"><?php print($CMS->TH->GetContentTitle()); ?></a></h2>
<?php
  print($CMS->TH->GetContentExcerpt(100, " [...]"));
?>
<?php
  if ($CMS->TH->GetContentDownloadLink()) {
?>
    <p><span class="attach-download"><a href="<?php print($CMS->TH->GetContentDownloadLink()); ?>">Download Attachment</a> (<?php print($CMS->TH->GetContentDownloadSize()); ?> <?php print($CMS->TH->GetContentDownloadType()); ?>) Hits: <?php print($CMS->TH->GetContentDownloadHits()); ?></span></p>
<?php
  }
  $intCommentCount = $CMS->TH->GetContentCommentCount();
  if ($intCommentCount == 1) {
    $strCommentCount = "1 comment";
  } else {
    $strCommentCount = "$intCommentCount comments";
  }
  $strContentAuthorLink = $CMS->TH->GetContentAuthorProfileLink();
  if ($strContentAuthorLink) {
    $strContentAuthorLink = " | ".$strContentAuthorLink;
  }
  $strContentAuthorName = $CMS->TH->GetContentAuthorName();
?>
<p class="smalltext byline"><span class="author">Posted by <?php print($strContentAuthorName); ?> on <?php print($CMS->TH->GetContentCreateDate()); ?></span><?php print($strContentAuthorLink); ?> | <a href="<?php print($CMS->TH->GetContentLinkURL()); ?>#cList"><?php print($strCommentCount); ?></a> | <a href="<?php print($CMS->TH->GetContentLinkURL()); ?>">Permalink</a> | <?php print($CMS->TH->GetContentEditLink()); ?> <?php print($CMS->TH->GetContentDeleteLink()); ?></p>
</div>
<?php
    }

  /* ********************************************** */
  /* *              Content Wrapper End             */
  /* ********************************************** */

      $strContentWrapperEnd = <<<ContentWrapperEnd
</div>

ContentWrapperEnd;
    print($strContentWrapperEnd);
  } else {
    $strNoContent = <<<NoContent
<p>No content in this area.</p>

NoContent;
    /*
       Remove the slashes from the next line
       if you wish to display a message
       when an area has no content
    */
    //print($strNoContent);
  }

?>