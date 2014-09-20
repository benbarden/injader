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

  require 'sys/header.php';

  $intTagThreshold = $CMS->SYS->GetSysPref(C_PREF_TAG_THRESHOLD);
  $intMaxCount = $CMS->TG->GetMaxCount();
  $arrTagCounts = $CMS->TG->GetAllTagCounts();
  $intMinSize = 80;
  $intMaxSize = 300;
  $intNumTags = count($arrTagCounts);
  for ($i=0; $i<$intNumTags; $i++) {
    $intTagCount = $arrTagCounts[$i]['tag_count'];
    $intTagSize = ($intTagCount / $intMaxCount) * $intMaxSize;
    if ($intTagSize < $intMinSize) {
      $intTagSize = $intMinSize;
    }
    $arrTagSizes[$intTagCount] = $intTagSize."%";
  }

  // Get all tags
  $strTagData = "";
  $arrTags = $CMS->TG->GetAll();
  for ($i=0; $i<count($arrTags); $i++) {
    $strTag = $arrTags[$i]['tag'];
    $strTagLink = str_replace(" ", "%20", $strTag);
    $intTagCount = $arrTags[$i]['tag_count'];
    if ($intTagCount >= $intTagThreshold) {
      $intSize = $arrTagSizes[$intTagCount];
      $strTagData .= <<<TagData
<span class="item" style="font-size: $intSize;"><a href="{FN_SEARCH}?go=yes&amp;t=$strTagLink">$strTag</a></span> 
TagData;
    }
  }
  if (count($arrTags) == 0) {
    $strHTML = <<<NoTags
<div id="pagecontent">
<h1>Tag Map</h1>
<p>No tags found!</p>
</div>
NoTags;
  } else {
    $strHTML = <<<TagMap
<div id="pagecontent">
<h1>Tag Map</h1>
<p>The Tag Map provides quick access to site content. Click on a tag to display a list of all the pages with that tag. The larger the tag, the more pages will come up. You can also <a href="{FN_SEARCH}">search this site</a>.</p>
<div id="tagmap">
$strTagData
</div>
</div>
TagMap;
  }
  $strHTML = $CMS->RC->DoAll($strHTML);
  $CMS->MV->DefaultPage("Tag Map", $strHTML);
?>