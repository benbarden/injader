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

  require 'sys/header.php';
  
  // Get all public content areas
  $arrAreas = $CMS->AT->BuildAreaArray(1, ""); // Just get the values
  $j = 0; // List count
  for ($i=0; $i<count($arrAreas); $i++) {
    $intAreaID = $arrAreas[$i]['id'];
    if ($arrAreas[$i]['type'] == "Content") {
        if ($CMS->AR->CountContentInArea($intAreaID, C_CONT_PUBLISHED) > 0) {
          $arrSiteMapAreas[$j] = $arrAreas[$i];
          $j++;
        }
    }
  }
  
  // ** Sitemap header ** //
  
  $strData = <<<SiteMapHeader
<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

SiteMapHeader;

  // ** Exit if empty ** //

  if ($j == 0) {
    $strData .= "</sitemapindex>\n";
    header("Content-type: text/xml");
    exit($strData);
  }
  
  // ** Process sitemap data ** //
  
  if (is_array($arrSiteMapAreas)) {
    for ($i=0; $i<count($arrSiteMapAreas); $i++) {
      $intID = $arrSiteMapAreas[$i]['id'];
      $strURL = "http://".SVR_HOST.FN_SITEMAP."?id=$intID";
      $strData .= <<<SiteMapItem
  <sitemap>
    <loc>$strURL</loc>
  </sitemap>

SiteMapItem;
    }
    $strData .= "</sitemapindex>\n";
    header("Content-type: text/xml");
    exit($strData);
  }
  
?>