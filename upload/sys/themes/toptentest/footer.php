<?php
  /*
    Injader Default Theme
    Footer template
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
</td>
<td id="tplMajRightSide" class="sidebar">
<!-- Begin Right Sidebar -->
<?php
  if ($CMS->TH->IsLoggedIn()) {
    $strCurrentUser = $CMS->RES->GetCurrentUser();
  } else {
    $strCurrentUser = "Guest";
  }
?>
<h2>Welcome <?php print($strCurrentUser); ?>!</h2>
<?php
  $intIndex = 0;
  $strTagMapLink   = $CMS->TH->GetSysTagMapLink();
  $strLoginLink    = $CMS->TH->GetSysLoginLink();
  $strAdminCPLink  = $CMS->TH->GetSysAdminCPLink();
  $strLogoutLink   = $CMS->TH->GetSysLogoutLink();
  $strRegisterLink = $CMS->TH->GetSysRegisterLink();
  $strContactLink  = FN_CST_CONTACT;
  if ($strContactLink) {
    $arrNavLinks[$intIndex++] = "<a href=\"$strContactLink\">Contact</a>";
  }
  if ($strRegisterLink) {
    $arrNavLinks[$intIndex++] = "<a href=\"$strRegisterLink\">Register</a>";
  }
  if ($strLoginLink) {
    $arrNavLinks[$intIndex++] = "<a href=\"$strLoginLink\">Login</a>";
  }
  if ($strAdminCPLink) {
    $arrNavLinks[$intIndex++] = "<a href=\"$strAdminCPLink\">Control Panel</a>";
  }
  if ($strLogoutLink) {
    $arrNavLinks[$intIndex++] = "<a href=\"$strLogoutLink\">Logout</a>";
  }
  if ($strTagMapLink) {
    $arrNavLinks[$intIndex++] = "<a href=\"$strTagMapLink\">Tag Map</a>";
  }
  $intCount = 0;
  $strNavList = "";
  while ($intCount < $intIndex) {
    if ($intCount == 0) {
      $strNavList .= "<ul>\n";
    }
    $strNavList .= "<li>".$arrNavLinks[$intCount]."</li>\n";
    $intCount++;
  }
  if ($strNavList) {
    $strNavList .= "</ul>\n";
  }
  print($strNavList);
?>
<!-- End Right Sidebar -->
</td>
</tr>
<tr>
<td id="tplMajFooter" class="footer" style="padding: 4px; text-align: center;" colspan="3">
Copyright © <?php print(date('Y')); ?> <?php print($CMS->TH->GetHeaderSiteTitle()); ?> : Powered by <a href="http://www.injader.com" title="Injader - Content management for everyone">Injader</a>
</td>
</tr>
</table>
<?php print($CMS->TH->GetSysWrapperEnd()); ?>
</body>
</html>