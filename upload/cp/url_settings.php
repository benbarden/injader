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
    $strPageTitle = "URL Settings";
    
    $CMS->AP->SetTitle($strPageTitle);
    
    $intLinkStyle = "";
    
    if ($_POST) {
        $intLinkStyle = empty($_POST['optLink']) ? "1" : $CMS->FilterNumeric($_POST['optLink']);
        if (!$intLinkStyle) {
            $intLinkStyle = "1";
        }
        // Rebuild the cache
        $CMS->SYS->RebuildCache();
        // Save the setting
        if ($CMS->SYS->GetSysPref(C_PREF_LINK_STYLE) != $intLinkStyle) {
            $CMS->SYS->WriteSysPref(C_PREF_LINK_STYLE, $intLinkStyle);
        }
        // Rebuild the URL mappings table
        $CMS->UM->rebuildAll();
        // Confirm
        $strConfirmMsg = "<p><b>Settings updated successfully.</b></p>\n\n";
    } else {
        if (!isset($CMS->SYS->arrSysPrefs[C_PREF_SITE_TITLE])) {
            $CMS->SYS->GetAllSysPrefs();
        }
        $arrSysPrefs = $CMS->SYS->arrSysPrefs;
        foreach ($arrSysPrefs as $strKey => $strValue) {
            switch ($strKey) {
                case C_PREF_LINK_STYLE: $intLinkStyle = $strValue; break;
            }
        }
        $strConfirmMsg = "";
    }
    
    $strLinkStyle1Checked = "";
    $strLinkStyle2Checked = "";
    $strLinkStyle3Checked = "";
    $strLinkStyle4Checked = "";
    $strLinkStyle5Checked = "";
    switch ($intLinkStyle) {
        case "1": $strLinkStyle1Checked = ' checked="checked"'; break;
        case "2": $strLinkStyle2Checked = ' checked="checked"'; break;
        case "3": $strLinkStyle3Checked = ' checked="checked"'; break;
        case "4": $strLinkStyle4Checked = ' checked="checked"'; break;
        case "5": $strLinkStyle5Checked = ' checked="checked"'; break;
    }
    
    $strSubmitButton = $CMS->AC->SubmitButton();
    $strCancelButton = $CMS->AC->CancelButton();
    
    // Main form
    $strHTML = <<<END
<p>Settings: <a href="{FN_ADM_GENERAL_SETTINGS}" title="General Settings">General</a> | 
<a href="{FN_ADM_CONTENT_SETTINGS}" title="Content Settings">Content</a> | 
<a href="{FN_ADM_FILES_SETTINGS}" title="File Settings">Files</a> | <b>URLs</b></p>
<h1>$strPageTitle</h1>
$strConfirmMsg
<p>This screen allows you to choose the style to use for your links.</p>
<p>If you initially use one link style and you wish to change to a different style in 
the future, Injader will ensure that your old links do not break. Any requests for the 
old links will automatically redirect to the new links.</p>
<form id="frmSystemPrefs" action="{FN_ADM_URL_SETTINGS}" method="post">
<table class="DefaultTable PageTable" cellspacing="1">
    <colgroup>
        <col class="InfoColour MediumCell" />
        <col class="BaseColour" />
    </colgroup> 
    <tr>
        <th class="HeadColour SpanCell Left" colspan="2">URL Settings</th>
    </tr>
    <tr>
        <td>
            <b>Link Style</b>
            <br />This will change the style of the links used on your site.
        </td>
        <td>
            <input type="radio" id="optLink1" name="optLink" value="1"$strLinkStyle1Checked />
            <label for="optLink1">#1: yoursite.com/index.php/article/1/hello-world - full-length</label>
            <br />
            <input type="radio" id="optLink2" name="optLink" value="2"$strLinkStyle2Checked />
            <label for="optLink2">#2: yoursite.com/article/1/hello-world - no view.php</label>
            <br />
            <input type="radio" id="optLink3" name="optLink" value="3"$strLinkStyle3Checked />
            <label for="optLink3">#3: yoursite.com/hello-world - title only</label>
            <br />
            <input type="radio" id="optLink4" name="optLink" value="4"$strLinkStyle4Checked />
            <label for="optLink4">#4: yoursite.com/area-name/hello-world - area and title</label>
            <br />
            <input type="radio" id="optLink5" name="optLink" value="5"$strLinkStyle5Checked />
            <label for="optLink5">#5: yoursite.com/2009/12/31/hello-world - date and title</label>
        </td>
    </tr>
    <tr>
        <td class="FootColour SpanCell Centre" colspan="2">
            $strSubmitButton $strCancelButton
        </td>
    </tr>
</table>
</form>

END;
    $CMS->AP->Display($strHTML);
?>