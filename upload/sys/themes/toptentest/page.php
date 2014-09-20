<?php
  /*
    Injader Default Theme
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
<?php
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
?>
<h1 class="item-title"><?php print($strContTitle); ?></h1>
<p class="smalltext item-nav"><?php print($CMS->TH->GetContentPrevLink()); ?> | <a href="<?php print($CMS->TH->GetContentAreaLink()); ?>"><?php print($CMS->TH->GetContentAreaName()); ?></a> | <?php print($CMS->TH->GetContentNextLink()); ?></p>
<?php
  if ($CMS->TH->GetContentDownloadLink()) {
?>
    <p><span class="attach-download"><a href="<?php print($CMS->TH->GetContentDownloadLink()); ?>">Download Attachment</a> (<?php print($CMS->TH->GetContentDownloadSize()); ?> <?php print($CMS->TH->GetContentDownloadType()); ?>) Hits: <?php print($CMS->TH->GetContentDownloadHits()); ?></span></p>
<?php
  }
  $strContURL = $CMS->TH->GetContentURL();
  if ($strContURL) {
?>
<p>Supporting information: <a href="<?php print($strContURL); ?>"><?php print($strContURL); ?></a></p>
<?php
  }
?>
<?php print($strContBody); ?>
<!-- $cmsDownloadAttach -->
<p class="smalltext byline"><span class="author">Posted by <?php print($CMS->TH->GetContentAuthorName()); ?> on <?php print($CMS->TH->GetContentCreateDate()); ?></span> | <a href="<?php print($CMS->TH->GetContentLinkURL()); ?>">Permalink</a> | Hits: <?php print($intHits); ?><br />
Tags: 
<?php
  $strTags = $CMS->TH->GetContentLinkedTags();
  if ($strTags) {
    print($strTags);
  } else {
    print("None.");
  }
?>
</p>
<?php print($CMS->TH->GetContentEditLink()); ?> <?php print($CMS->TH->GetContentDeleteLink()); ?> <?php print($CMS->TH->GetContentLockLink()); ?>
<?php
  if ($CMS->TH->GetRelatedContentCount() > 0) {
?>
<div id="mRelated">
<h2>Related Content</h2>
<ul class="mRelatedList">
<?php
    while ($CMS->TH->NextRelatedContentItem() < $CMS->TH->GetRelatedContentCount()) {
      $intWeight = $CMS->TH->GetRelatedContentWeight();
      $strMatchesText = $intWeight == 1 ? "match" : "matches";
?>
<li class="mRelatedItem"><a href="<?php print($CMS->TH->GetRelatedContentLinkURL()); ?>"><?php print($CMS->TH->GetRelatedContentTitle()); ?></a> - <span class="mRelatedMatches"><?php print($intWeight); ?> <?php print($strMatchesText); ?></span></li>
<?php
    }
?>
</ul>
</div>
<?php
  } else {
?>
<p>No related content found.</p>
<?php
  }
?>
<h2 id="cList">Comments on <?php print($strContTitle); ?></h2>
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
<div id="c<?php print($CMS->TH->GetCommentID()); ?>" class="comment">
<div style="float: right;"><?php print($CMS->TH->GetCommentNumber()); ?></div>
<?php
  $intAvatarID = $CMS->TH->GetCommentAvatarID();
  if ($intAvatarID) {
    $strAvatarURL = FN_FILE_DOWNLOAD."?id=$intAvatarID";
?>
<div class="comment-avatar">
<img src="<?php print($strAvatarURL); ?>" alt="<?php print($CMS->TH->GetCommentAuthor()); ?>'s avatar" />
</div>
<?php
  }
  $strCommentAuthorHomepageLink = $CMS->TH->GetCommentAuthorHomepageLink();
  if ($strCommentAuthorHomepageLink) {
    $strCommentAuthorHomepageLink .= " | ";
  }
  $strCommentAuthorProfileLink = $CMS->TH->GetCommentAuthorProfileLink();
  if ($strCommentAuthorProfileLink) {
    $strCommentAuthorProfileLink .= " | ";
  }
?>
<div class="comment-author">
<b>Posted by <?php print($CMS->TH->GetCommentAuthor()); ?></b> | <?php print($CMS->TH->GetCommentCreateDate()); ?> | <?php print($strCommentAuthorHomepageLink); ?> <?php print($strCommentAuthorProfileLink); ?> | <a href="<?php print($CMS->TH->GetCommentPermalink()); ?>">Permalink</a></div>
<p><?php print($strCommentBody); ?></p>
<p><?php print($CMS->TH->GetCommentEditLink()); ?> <?php print($CMS->TH->GetCommentDeleteLink()); ?> <?php print($CMS->TH->GetCommentIP()); ?></p>
<?php
  $dteCommentEdited = $CMS->TH->GetCommentEditDate();
  if ($dteCommentEdited) {
?>
<p class="comment-edited">Edited: <?php print($dteCommentEdited); ?></p>
<?php
  }
?>
<div style="clear: both"></div>
</div>
<?php
    }
?>
<?php
  } // End comments > 0

  // ** Begin comment form ** //
  if ($CMS->TH->CanAddComments()) {
    print($CMS->TH->GetCommentForm());
  } else {
    if ($CMS->TH->IsLoggedIn()) {
      if ($CMS->TH->GetContentLocked() == "Y") {
?>
<p>This article is locked; comments cannot be added.</p>
<?php
      } else {
?>
<p>You do not have access to post comments in this area.</p>
<?php
      }
    } else {
      $strLoginLink    = FN_LOGIN;
      $strRegisterLink = FN_REGISTER;
?>
<p>You must be registered and logged in to post comments. <a href="<?php print($strLoginLink); ?>">Log in here</a>.<br />Not registered? <a href="<?php print($strRegisterLink); ?>">Register now!</a></p>
<?php
    }
  }
?>