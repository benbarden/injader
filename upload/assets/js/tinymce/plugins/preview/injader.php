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

  require '../../../../../sys/header.php';
  $strPageTitle = "Preview";
  
  $strTinyMCEHeader = <<<HeaderTags
<script type="text/javascript" src="{URL_ROOT}assets/js/tinymce/tiny_mce_popup.js"></script>
<script type="text/javascript" src="{URL_ROOT}assets/js/tinymce/plugins/preview/jscripts/embed.js"></script>
<script type="text/javascript">
  tinyMCE.init({
    theme : "advanced",
    skin : "injader"
  });
  tinyMCEPopup.onInit.add(function(ed) {
    var dom = tinyMCEPopup.dom;
    // Load editor content_css
    //tinymce.each(ed.settings.content_css.split(','), function(u) {
    //  dom.loadCSS(ed.documentBaseURI.toAbsolute(u));
    //});
    // Place contents inside div container
    dom.setHTML('content', ed.getContent());
  });
</script>

HeaderTags;
  $strTinyMCEHeader = $CMS->RC->DoAll($strTinyMCEHeader);
  $strCurrentHeader = $CMS->SYS->GetSysPref(C_PREF_SITE_HEADER);
  $CMS->TH->SetHeaderCustomTags($strCurrentHeader."\r\n".$strTinyMCEHeader);
  
  $strHTML = <<<PreviewPage
<div id="content">
<!-- Gets filled with editor contents -->
</div>

PreviewPage;

  $CMS->MV->DefaultPage($strPageTitle, $strHTML);
?>