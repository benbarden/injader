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

  require '../sys/header.php';
  $strPageTitle = "Help - Cookies";
  $strHTML = <<<MainContentEnd
<div id="Info-Cookies" class="mPage">
<h1>$strPageTitle</h1>
<p>This site uses cookies. You must have cookies enabled in your browser or you will not be able to log in.</p>
<h2>How to enable cookies in Internet Explorer</h2>
<ul>
<li>Go to Tools - Internet Options - Privacy</li>
<li>Click Advanced</li>
<li>Check the option "Override automatic cookie handling"</li>
<li>Change "First-party Cookies" to "Prompt"</li>
<li>Change "Third-party Cookies" to "Prompt"</li>
<li>Click OK</li>
</ul>
<h2>How to enable cookies in Firefox</h2>
<ul>
<li>Click Tools - Options - Privacy</li>
<li>Under cookies, check the option "Allow cookies from sites"</li>
<li>Click OK</li>
</ul>
</div>

MainContentEnd;
  $CMS->LP->SetTitle($strPageTitle);
  $CMS->LP->Display($strHTML);
?>