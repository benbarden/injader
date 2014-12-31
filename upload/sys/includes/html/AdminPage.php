<?php
/*
  Injader
  Copyright (c) 2005-2015 Ben Barden


  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

  class AdminPage extends Helper {
    var $strTitle = "Injader";
    var $strContent;
    // Main functions
    function SetTitle($strNewTitle) {
      $this->strTitle = $strNewTitle;
    }
    function GetTitle() {
      return $this->strTitle;
    }
    function Display($strContentOverride = "") {
      $dteStartTime = $this->MicrotimeFloat();
      global $CMS;
      $RC = new ReplaceConstants;
      if ($strContentOverride) {
        $strBody = $strContentOverride;
      } else {
        $strBody = $this->strContent;
      }
      $strHTMLToPrint = $this->GetHeader().$strBody.$this->GetFooter();
      $strHTMLToPrint = $RC->DoAll($strHTMLToPrint);
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      global $strExecutionTime; // Set in header.php
      if ($strExecutionTime) {
        $strQueryTimeData = <<<ExecTime
<div id="majQueryTimeData">
<p>Query Execution Time</p>
<ol>
$strExecutionTime
</ol>
</div>
ExecTime;
      } else {
        $strQueryTimeData = "";
      }
      global $blnOverrideQT; // Allows the variable to be put in sys templates without being evaluated
      if ($blnOverrideQT) {
        $strHTMLToPrint = str_replace('$cmsQueryTime', "", $strHTMLToPrint);
      } else {
        if (C_TEST_MODE <> "") {
          $strHTMLToPrint = str_replace('$cmsQueryTime', $strQueryTimeData, $strHTMLToPrint);
        } else {
          $strHTMLToPrint = str_replace('$cmsQueryTime', "", $strHTMLToPrint);
        }
      }
      print($strHTMLToPrint);
      $CMS->IQ->Disconnect();
      exit;
    }
    function GetHeader() {
      global $CMS;
      $dteStartTime = $this->MicrotimeFloat();
      $strPageTitle = $this->GetTitle();

      // Index
      $strSiteTitle  = $CMS->SYS->GetSysPref(C_PREF_SITE_TITLE);
      $strIndexURL   = str_replace("index".F_EXT_PHP, "", FN_INDEX);

      // CP LINKS
      $controlPanelLinks = array();

      $currentUserId = $CMS->RES->GetCurrentUserID();
      $viewProfileLink = $CMS->PL->ViewUser($currentUserId);

      // New article
      // Content
        $strNewArticleLink = "";
        $CMS->RES->ViewManageContent();
      if ($CMS->RES->IsError()) {
        $strManageContent = "";
      } else {
        // Create article?
        if ($CMS->RES->CountTotalWriteAccess() > 0) {
          $strManageContent = $strNewArticleLink." | <a href=\"{FN_ADM_CONTENT_MANAGE}\" title=\"Manage Content\">Content</a>";
          /*
          $controlPanelLinks[] = array(
              'URL' => '{FN_ADM_WRITE}?action=create',
              'Title' => 'Add new content to the site',
              'Text' => 'New Article'
          );
          */
          $strNewArticleLink = "<li><a href=\"{FN_ADM_WRITE}?action=create\" title=\"Add new content to the site\">+ Article</a></li>";
          $controlPanelLinks[] = array(
              'URL' => '{FN_ADM_CONTENT_MANAGE}',
              'Title' => 'Manage Content',
              'Text' => 'Content'
          );
        } else {
          $strManageContent = "";
        }
      }
      // Admin
      $CMS->RES->Admin();
      if ($CMS->RES->IsError()) {
        $strAdminLinks = $strManageContent;
      } else {
          $strAdminLinks = <<<AdminLinks
                    <!-- Content -->
                    <li><a href="/cp/content_manage.php?area=0&amp;status=0&amp;user=" title="Manage Content">Content</a></li>
                    <!-- Areas -->
                    <li><a href="/cp/areas.php" title="Manage your site areas">Areas</a></li>
                    <!-- Files -->
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Files <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="/cp/files.php?type=site">Site Files</a></li>
                            <li><a href="/cp/files.php?type=attach">Attachments</a></li>
                            <li><a href="/cp/files.php?type=avatar">Avatars</a></li>
                            <li class="divider"></li>
                            <li><a href="/cp/files_site_upload.php?action=create">+ Add New File</a></li>
                        </ul>
                    </li>
                    <!-- Comments -->
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Comments <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="/cp/comments.php?type=">Manage Comments</a></li>
                            <li><a href="/cp/comments.php?type=approved">Approved</a></li>
                            <li><a href="/cp/comments.php?type=pending">Pending</a></li>
                            <li><a href="/cp/comments.php?type=spam">Spam</a></li>
                            <li class="divider"></li>
                            <li><a href="/cp/tools_spam_stats.php">Spam Stats</a></li>
                        </ul>
                    </li>
                    <!-- Users -->
                    <li><a href="/cp/users.php?action=showall" title="Manage user accounts, roles, and permissions">Users</a></li>
                    <!-- Tools -->
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Tools <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="/cp/spam_rules.php">Spam Rules</a></li>
                            <li><a href="/cp/tools_spam_stats.php">Comment Spam Stats</a></li>
                            <li><a href="/cp/tools_user_sessions.php">User Sessions</a></li>
                            <li><a href="/cp/access_log.php">Access Log</a></li>
                            <li><a href="/cp/error_log.php">Error Log</a></li>
                        </ul>
                    </li>
                    <!-- Settings -->
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Settings <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="/cp/general_settings.php">General Settings</a></li>
                            <li><a href="/cp/content_settings.php">Content Settings</a></li>
                            <li><a href="/cp/files_settings.php">File Settings</a></li>
                            <li><a href="/cp/url_settings.php">URL Settings</a></li>
                            <li class="divider"></li>
                            <li><a href="/cp/themes.php">Themes</a></li>
                            <li class="divider"></li>
                            <li><a href="/cp/permissions.php">Permissions</a></li>
                            <li><a href="/cp/user_roles.php">User Roles</a></li>
                        </ul>
                    </li>

AdminLinks;
      }
      // Meta generator
      $strMetaGenerator = "<meta name=\"generator\" content=\"".PRD_PRODUCT_NAME." - ".PRD_PRODUCT_URL."\" />";
      // Build code
      $strHeaderHTML = <<<CMSHeader
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>$strPageTitle</title>

    <script type="text/javascript" src="{URL_SYS_ROOT}scripts.js"></script>
    <script type="text/javascript" src="{URL_ROOT}assets/js/jquery/jquery-1.11.1.min.js"></script>
    <script type="text/javascript" src="{URL_ROOT}assets/js/jqueryui/jquery-ui.min.js"></script>
    <link href="{URL_ROOT}assets/css/jqueryui/jquery-ui.min.css" rel="stylesheet">
    <link href="{URL_ROOT}assets/css/jqueryui/jquery-ui.structure.min.css" rel="stylesheet">
    <link href="{URL_ROOT}assets/css/jqueryui/jquery-ui.theme.min.css" rel="stylesheet">

    <!-- Bootstrap core CSS -->
    <link href="{URL_ROOT}assets/css/bootstrap/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="{URL_ROOT}assets/css/bootstrap/dashboard.css" rel="stylesheet">

    <!-- Custom Injader styles -->
    <link href="{URL_ROOT}assets/css/injader-cp/injader-cp.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <!-- 3.7.2 -->
      <script src="{URL_ROOT}assets/js/bootstrap/html5shiv.min.js"></script>
      <!-- 1.4.2 -->
      <script src="{URL_ROOT}assets/js/bootstrap/respond.min.js"></script>
    <![endif]-->
  </head>

</head>

<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <p class="navbar-text"><a href="$strIndexURL">&lt; view site</a></p>
        </div> <!-- /navbar-header -->
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
                <li>
                <a href="{FN_ADM_INDEX}"><span class="glyphicon glyphicon-home" aria-hidden="true"></span> Dashboard</a>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">My Settings <span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="/cp/edit_profile.php">Edit Profile</a></li>
                        <li><a href="$viewProfileLink">View Profile</a></li>
                        <li><a href="/cp/change_password.php">Change Password</a></li>
                        <li><a href="/cp/manage_avatars.php">Manage Avatars</a></li>
                    </ul>
                </li>
                $strNewArticleLink
                $strAdminLinks
            </ul>
        </div> <!-- /navbar-collapse -->
    </div>
</div> <!-- /navbar -->

<div class="container-fluid">
    <div class="row">
        <div class="main">

CMSHeader;
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $strHeaderHTML;
    }
    // ** Footer ** //
    function GetFooter() {
      global $CMS;
      $dteStartTime = $this->MicrotimeFloat();
      $strFooter = <<<Footer
</div>
</div>
</div>

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="{URL_ROOT}assets/js/bootstrap/bootstrap.min.js"></script>
<script src="{URL_ROOT}assets/js/bootstrap/docs.min.js"></script>
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="{URL_ROOT}assets/js/bootstrap/ie10-viewport-bug-workaround.js"></script>

</body>
</html>

Footer;
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $strFooter;
    }
  }
?>