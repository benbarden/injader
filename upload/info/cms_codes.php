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
  $strPageTitle = "Help - CMS Codes";
  $strHTML = <<<CMSCodes
<div id="Info-CMSCodes" class="mPage">
<h1>$strPageTitle</h1>
<p>CMS Codes provide limited formatting in comments.</p>
<table class="DefaultTable MediumTable FixedTable" cellspacing="1">
  <colgroup>
    <col class="InfoColour NarrowCell" />
    <col class="BaseColour" />
  </colgroup> 
  <tr>
    <th class="InfoColour">CMS Code</th>
    <th>Description</th>
  </tr>
  <tr>
    <td class="InfoColour">[link=url]text[/link]</td>
    <td>Hyperlink; "url" is the address of the link, "text" is the clickable text</td>
  </tr>
  <tr>
    <td class="InfoColour">[img src=url]text[/img]</td>
    <td>Image; "url" is the address of the image, "text" is a description of the image. Important: do not use double quotes in the description! Single quotes are OK.</td>
  </tr>
  <tr>
    <td class="InfoColour">[quote]text[/quote]</td>
    <td>Quotes; these are given a class of "cmsQuote" and can be formatted with CSS</td>
  </tr>
  <tr>
    <td class="InfoColour">[b] ... [/b]</td>
    <td>Bold</td>
  </tr>
  <tr>
    <td class="InfoColour">[i] ... [/i]</td>
    <td>Italic</td>
  </tr>
  <tr>
    <td class="InfoColour">[code] ... [/code]</td>
    <td>Code</td>
  </tr>
</table>
</div>

CMSCodes;
  $CMS->LP->SetTitle($strPageTitle);
  $CMS->LP->Display($strHTML);
?>