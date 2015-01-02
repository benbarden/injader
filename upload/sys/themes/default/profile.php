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
  // Assignments
  $intUserID = $CMS->TH->GetUserID();
  // User Stats
  $intContentCount = $CMS->ART->CountUserContent($intUserID, "AND content_status = 'Published'");
  $intFileCount    = $CMS->FL->CountUserFiles($intUserID);
  // Admin Links
  $CMS->RES->Admin();
  if (!$CMS->RES->IsError()) {
    $strUserEditLink     = FN_ADM_USER."?action=edit&amp;id=".$intUserID;
    $strUserContactLink  = FN_ADM_USER_CONTACT."?id=".$intUserID;
    $strUserActivityLink = FN_ADM_ACCESS_LOG."?user=".$CMS->TH->GetUserName();
    $strAdminLinks = <<<AdminLinks
  <tr>
    <td class="Centre">
      <p style="font-size: 120%; font-weight: bold; margin: 5px 0 0 0;">Admin Links</p>
    </td>
    <td colspan="2">
      <a href="$strUserEditLink">Edit</a> | <a href="$strUserContactLink">Contact</a> | <a href="$strUserActivityLink">Activity</a>
    </td>
  </tr>

AdminLinks;
  } else {
    $strAdminLinks = "";
  }
?>
<div id="UserCP-UserProfile" class="mPage">
<h1>View Profile - <?php print($CMS->TH->GetUserName()); ?></h1>
<table id="UserProfile" class="DefaultTable PageTable" cellspacing="1" style="border: 2px solid #9cf; padding: 4px;">
  <tr>
    <td class="Centre" rowspan="6">
      <?php print($CMS->TH->GetUserAvatarHTML()); ?>
      <p style="font-size: 120%; font-weight: bold; margin: 5px 0 0 0;"><?php print($CMS->TH->GetUserName()); ?></p>
    </td>
    <td class="InfoColour">Name</td>
    <td><?php print($CMS->TH->GetUserForename()); ?> <?php print($CMS->TH->GetUserSurname()); ?></td>
  </tr>
  <tr>
    <td class="InfoColour">Join date</td>
    <td><?php print($CMS->TH->GetUserJoinDate()); ?></td>
  </tr>
  <tr>
    <td class="InfoColour">Location</td>
    <td><?php print($CMS->TH->GetUserLocation()); ?></td>
  </tr>
  <tr>
    <td class="InfoColour">Occupation</td>
    <td><?php print($CMS->TH->GetUserOccupation()); ?></td>
  </tr>
  <tr>
    <td class="InfoColour">Interests</td>
    <td><?php print($CMS->TH->GetUserInterests()); ?></td>
  </tr>
  <tr>
    <td class="InfoColour">Home page</td>
    <td><?php print($CMS->TH->GetUserHomeHTML()); ?></td>
  </tr>
  <tr>
    <td class="InfoColour" colspan="3"></td>
  </tr>
  <tr>
    <td class="Centre" rowspan="3">
      <p style="font-size: 120%; font-weight: bold; margin: 5px 0 0 0;">Statistics</p>
    </td>
    <td class="InfoColour">Content</td>
    <td><?php print($intContentCount); ?></td>
  </tr>
  <tr>
    <td class="InfoColour">Files</td>
    <td><?php print($intFileCount); ?></td>
  </tr>
  <tr>
    <td class="InfoColour" colspan="3"></td>
  </tr>
<?php print($strAdminLinks); ?>
</table>
</div>