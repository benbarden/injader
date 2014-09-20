<?php
  /*
    Injader Default Theme
    Layout: Forum Theme
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

?>
<?php print($CMS->TH->GetSysBreadcrumbs()); ?>
<h1><?php print($strAreaName); ?></h1>
<?php
  if ($strAreaDesc) {
?>
<p><?php print($strAreaDesc); ?></p>
<?php
  }
?>
<?php print($strPageLinks); ?>
<?php
  if ($CMS->TH->GetSysWriteLink()) {
    $strAddText = "New Topic";
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
<table class="DiscussionTable" cellspacing="1" width="740" style="clear: both">
  <tr>
    <th>Title</th>
    <th>Comments</th>
    <th>Author</th>
  </tr>

ContentWrapperStart;
    print($strContentWrapperStart);

  /* ********************************************** */
  /* *              Content Items                   */
  /* ********************************************** */

    while ($CMS->TH->NextContentItem() < $CMS->TH->GetContentCount()) {
      // ** Forum Content Item ** //
      $strUnreadText = $CMS->TH->GetContentIsUnread() ? "<b>New</b> " : "";
      $intCommentCount = $CMS->TH->GetContentCommentCount();
      if ($intCommentCount == 1) {
        $strCommentCount = "1 comment";
      } else {
        $strCommentCount = "$intCommentCount comments";
      }
?>
  <tr>
    <td class="TitleCell"><?php print($strUnreadText); ?> <a href="<?php print($CMS->TH->GetContentLinkURL()); ?>" class="item-title"><?php print($CMS->TH->GetContentTitle()); ?></a></td>
    <td class="CommentCell"><?php print($strCommentCount); ?></td>
    <td class="AuthorCell"><?php print($CMS->TH->GetContentAuthorName()); ?></td>
  </tr>
<?php
    }

  /* ********************************************** */
  /* *              Content Wrapper End             */
  /* ********************************************** */

      $strContentWrapperEnd = <<<ContentWrapperEnd
</table>

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

  /* ********************************************** */
  /* *              Subareas                        */
  /* ********************************************** */
  $intSubareaCount = $CMS->TH->GetSubareaCount();
  if ($intSubareaCount > 0) {
    $strSubareaWrapperStart = <<<SubareaWrapperStart
<table class="DiscussionTable" cellspacing="1" width="600" style="clear: both">
  <tr>
    <th>Title</th>
    <th>Topics</th>
  </tr>

SubareaWrapperStart;
    print($strSubareaWrapperStart);

  /* ********************************************** */
  /* *              Subarea Items                   */
  /* ********************************************** */

    while ($CMS->TH->NextSubareaItem() < $CMS->TH->GetSubareaCount()) {
      $strSubareaDesc = $CMS->TH->GetSubareaDesc();
      $intNumItems    = $CMS->AR->CountContentInArea($CMS->TH->GetSubareaID(), "Published");
?>
  <tr>
    <td class="TitleCell">
      <b><a href="<?php print($CMS->TH->GetSubareaLinkURL()); ?>"><?php print($CMS->TH->GetSubareaName()); ?></a></b>
<?php
        if ($strSubareaDesc) {
?>
<br /><?php print($strSubareaDesc); ?>
<?php
        }
?>
    </td>
    <td class="TopicCell NarrowCell"><?php print($intNumItems); ?></td>
  </tr>
<?php
    }

  /* ********************************************** */
  /* *              Subarea Wrapper End             */
  /* ********************************************** */

    $strSubareaWrapperEnd = <<<SubareaWrapperEnd
</table>

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

?>