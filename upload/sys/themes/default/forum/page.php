<?php
  /*
    Injader Default Theme
    Layout: Forum Theme
    Page Template
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
<?php print($CMS->TH->GetSysBreadcrumbs()); ?>
<p><?php print($CMS->TH->GetContentPrevLink()); ?> : <?php print($CMS->TH->GetContentNextLink()); ?></p>
<table cellspacing="1" style="background-color: #000; color: #fff;">
  <tr>
    <td style="background-color: #BDD5C3; color: #000; margin: 0; padding: 5px; text-align: center; vertical-align: top; width: 175px;">
      <!-- $strAvatar -->
      <p><b><?php print($CMS->TH->GetContentAuthorName()); ?></b><br /><span class="navtext"><a href="<?php print($CMS->TH->GetContentLinkURL()); ?>">Permalink</a></span></p>
      <?php print($CMS->TH->GetContentEditLink()); ?> <?php print($CMS->TH->GetContentDeleteLink()); ?> <?php print($CMS->TH->GetContentLockLink()); ?>
    </td>
    <td style="background-color: #fff; color: #000; margin: 0; text-align: left; vertical-align: top;">
      <p style="background-color: #DCEBD9; color: #000; font-style: italic; padding: 5px; margin: 0;">Created: <?php print($CMS->TH->GetContentCreateDate()); ?> <!-- / Edited: $dteEditDate --></p>
      <div style="padding: 5px;">
      <h2><?php print($strContTitle); ?></h2>
      <?php print($strContBody); ?>
      </div>
      <p style="background-color: #eee; color: #000; font-style: italic; padding: 5px; margin: 0;">Tags: <?php
  $strTags = $CMS->TH->GetContentLinkedTags();
  if ($strTags) {
    print($strTags);
  } else {
    print("None.");
  }
?></p>
    </td>
  </tr>
  <tr id="cList">
    <td colspan="2">
    </td>
  </tr>
<?php
  if ($CMS->TH->GetCommentCount() > 0) {
    while ($CMS->TH->NextCommentItem() < $CMS->TH->GetCommentCount()) {
      $strCommentBody = $CMS->TH->GetCommentBody();
      if (!$strCommentBody) {
        // It's possible to rate an article without commenting -
        // you can customise the resulting text below.
        $strCommentBody = "This user did not leave a comment.";
      }
?>
  <tr id="c<?php print($CMS->TH->GetCommentID()); ?>" class="DiscussionComment comment">
    <td style="background-color: #BDD5C3; color: #000; margin: 0; padding: 5px; text-align: center; vertical-align: top;">
<?php
      $intAvatarID = $CMS->TH->GetCommentAvatarID();
      if ($intAvatarID) {
        $strAvatarURL = FN_FILE_DOWNLOAD."?id=$intAvatarID";
?>
      <img src="<?php print($strAvatarURL); ?>" alt="<?php print($CMS->TH->GetCommentAuthor()); ?>'s avatar" />
<?php
      }
?>
      <p><b><?php print($CMS->TH->GetCommentAuthor()); ?></b><br /><span class="navtext"><a href="<?php print($CMS->TH->GetCommentPermalink()); ?>">Permalink</a></span></p>
      <?php print($CMS->TH->GetCommentEditLink()); ?> <?php print($CMS->TH->GetCommentDeleteLink()); ?>
      <br /><?php print($CMS->TH->GetCommentIP()); ?>
    </td>
    <td style="background-color: #fff; color: #000; margin: 0; text-align: left; vertical-align: top;">
<div style="float: right; font-size: 400%; font-family: 'Times New Roman', serif; padding: 5px;"><?php print($CMS->TH->GetCommentNumber()); ?></div>
      <p style="background-color: #DCEBD9; color: #000; font-style: italic; padding: 5px; margin: 0;">Created: <?php print($CMS->TH->GetCommentCreateDate()); ?></p>
      <div style="padding: 5px;">
      <?php print($strCommentBody); ?>
      </div>
    </td>
  </tr>
<?php
    } // Comment loop
  } // Comments > 0
?>
</table>
<?php

  // ** Begin comment form ** //
  if ($CMS->TH->CanAddComments()) {
    print($CMS->TH->GetCommentForm());
  } else {
    if ($CMS->TH->IsLoggedIn()) {
      $strLoginLink    = FN_LOGIN;
      $strRegisterLink = FN_REGISTER;
?>
<p>You must be registered and logged in to post comments. <a href="<?php print($strLoginLink); ?>">Log in here</a>.<br />Not registered? <a href="<?php print($strRegisterLink); ?>">Register now!</a></p>
<?php
    } else {
      if ($CMS->TH->GetContentLocked() == "Y") {
?>
<p>This article is locked; comments cannot be added.</p>
<?php
      } else {
?>
<p>You do not have access to post comments in this area.</p>
<?php
      }
    }
  }
?>