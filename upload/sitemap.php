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
  
  $intID = empty($_GET['id']) ? "" : $CMS->FilterNumeric($_GET['id']);
  if (!$intID) {
    exit("No ID specified!");
  }
  
  $CMS->RES->ViewArea($intID);
  if ($CMS->RES->IsError()) {
    exit("No access to view this area!");
  }
  
  $strXMLData = "";
  
  $arrResult = $CMS->ResultQuery("SELECT id, permalink, last_updated FROM {IFW_TBL_CONTENT} con WHERE content_area_id = $intID AND content_status = '{C_CONT_PUBLISHED}' ORDER BY create_date DESC", basename(__FILE__), __LINE__);
  
  if (count($arrResult) == 0) {
    exit("There is no content on your site!");
  }
  
  $strXMLData = <<<XMLHeader
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

XMLHeader;

  for ($i=0; $i<count($arrResult); $i++) {
    $intID       = $arrResult[$i]['id'];
    $permalink   = $arrResult[$i]['permalink'];
    $dteUpdated  = $arrResult[$i]['last_updated'];
    $strItemURL = "http://".SVR_HOST.$permalink;
    $strXMLData .= <<<XMLItem
  <url>
    <loc>$strItemURL</loc>
  </url>

XMLItem;
  }
  
  $strXMLData .= <<<XMLFooter
</urlset>

XMLFooter;
  header("Content-type: text/xml");
  exit($strXMLData);
?>