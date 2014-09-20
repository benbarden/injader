<?php
/*
  Injader - Content management for everyone
  Copyright (c) 2005-2009 Ben Barden
  Please go to http://www.injader.com if you have questions or need help.

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

  class InjaderPage {
    function Display($strHTML, $strTitle) {
      $strPageHeader = <<<PageHeader
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>$strTitle</title>
<link href="../sys/cp.css" rel="stylesheet" type="text/css" />
</head>
<body id="cp-body">
<!-- Begin header -->
<div id="tplPageWrapper">
  <div id="tplHeadContent">
    <img src="../sys/images/ij_header.jpg" alt="Injader" border="0" />
  </div>
  <div id="navbar">
    <a href="http://www.injader.com">Injader.com</a>
  </div>
  <div id="tplMainContent" style="font-size: 80%;">

PageHeader;

      $strPageFooter = <<<PageFooter
  </div>
</div>
</body>
</html>

PageFooter;
      print($strPageHeader.$strHTML.$strPageFooter);
      exit;
    }
  }
?>