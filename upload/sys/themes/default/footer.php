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
  $arrNavLinks[$intIndex++] = '<a href="'.URL_ROOT.'cms/archives">Archives</a>';
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
<h2><?php print($CMS->TS->Get("SidebarHeader1")); ?></h2>
<?php
  $CMS->MV->DoAllLevelNavBar(C_NAV_SECONDARY);
  if ($CMS->TH->GetNavigationCount() > 0) {
?>

<?php
    while ($CMS->TH->NextNavigationItem() < $CMS->TH->GetNavigationCount()) {
?>
<p style="margin: 0 0 5px 0;"><a href="<?php print($CMS->TH->GetNavigationLink()); ?>"><?php print($CMS->TH->GetNavigationName()); ?></a></p>
<?php
    }
?>

<?php
  }
?>
<h2><?php print($CMS->TS->Get("SidebarHeader2")); ?></h2>
<?php
  $CMS->MV->DoAllLevelNavBar(C_NAV_TERTIARY);
  if ($CMS->TH->GetNavigationCount() > 0) {
?>

<?php
    while ($CMS->TH->NextNavigationItem() < $CMS->TH->GetNavigationCount()) {
?>
<p style="margin: 0 0 5px 0;"><a href="<?php print($CMS->TH->GetNavigationLink()); ?>"><?php print($CMS->TH->GetNavigationName()); ?></a></p>
<?php
    }
?>

<?php
  }
?>
<!-- End Right Sidebar -->
</td>
</tr>
<tr>
<td id="tplMajFooter" class="footer" style="padding: 4px; text-align: center;" colspan="3">
Copyright &copy; <?php print(date('Y')); ?> <?php print($CMS->TH->GetHeaderSiteTitle()); ?> : Powered by <a href="http://www.injader.com" title="Injader - Content management for everyone">Injader</a>
</td>
</tr>
</table>
<?php print($CMS->TH->GetSysWrapperEnd()); ?>
</body>
</html>
<!--
<?php
  global $objPageStart;
  $dblMemUsed  = memory_get_usage(true) / 1024 / 1024;
  $dblTimeUsed = round((microtime(true) - $objPageStart), 4);
  print("Page rendered in $dblTimeUsed seconds.\n");
  print("Page consumed $dblMemUsed MB.\n");
?>
-->