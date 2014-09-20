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
  $CMS->RES->Admin();
  if ($CMS->RES->IsError()) {
    $CMS->Err_MFail(M_ERR_UNAUTHORISED, "Admin");
  }
  $strPageTitle = "Sitemap";

  $CMS->AP->SetTitle($strPageTitle);
  
  $strSiteMapURL = "http://".SVR_HOST.FN_SITEMAPINDEX;

  $strHTML = <<<END
<h1>$strPageTitle</h1>
<p>A sitemap can help search engines locate every page on your site. It also helps search engines to identify new and updated content.</p>
<p>Content will only be included in the sitemap if it is both public and published. Without a sitemap, some pages can be missed out.</p>
<h2>Your sitemap URL</h2>
<p>Your sitemap is located here: <b>$strSiteMapURL</b></p>
<h2>Helping search engines to find your sitemap</h2>
<p>To get indexed, you'll need to submit this URL to one or more search engines.</p>
<p>For instance, you can submit it to Google by using <a href="https://www.google.com/webmasters/tools/">Google Webmaster Tools</a>.</p>
<p>Another way to show search engines your sitemap is to put a line in your <i>robots.txt</i> file:</p>
<p><i>Sitemap: $strSiteMapURL</i></p>
<p>Soon, there will be a handy option where this can be done at the click of a button... until then, please bear with us!</p>

END;

  $CMS->AP->Display($strHTML);
?>