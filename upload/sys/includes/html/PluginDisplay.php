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

  class PluginDisplay extends Helper {
      
    function Build($strHTML) {
      global $CMS;
      global $strDBHost;
      global $strDBSchema;
      global $strDBAdminUser;
      global $strDBAdminPass;
      $strItemData = "";
      $strItemCode = "";
      $intPluginCount   = $CMS->WGT->GetPluginCount();
      $intCurrentUserID = $CMS->RES->GetCurrentUserID();
      if ($intPluginCount > 0) {
        $arrPlugins = $CMS->WGT->GetAllVarDesc();
        for ($i=0; $i<count($arrPlugins); $i++) {
          $strPluginCode = "";
          $strItemData   = "";
          $strVariable  = $arrPlugins[$i]['widget_variable'];
          $intConnID    = $arrPlugins[$i]['conn_id'];
          $intItemLimit = $arrPlugins[$i]['item_limit'];
          if (strpos($strHTML, $strVariable) !== false) {
            $strSQL = $arrPlugins[$i]['query_string'];
            $strUSQL = strtoupper($strSQL);
            if ((strpos($strUSQL, "LIMIT") === false) && ($intItemLimit > 0)) {
              $strSQL .= " LIMIT $intItemLimit";
            }
            if (strpos($strSQL, '$intCurrentUserID') !== false) {
              if (empty($intCurrentUserID)) {
                $intCurrentUserID = 0;
              }
              $strSQL = str_replace('$intCurrentUserID', $intCurrentUserID, $strSQL);
            }
            if (strpos($strSQL, '$intUserID') !== false) {
              $strSQL = str_replace('$intUserID', $CMS->MV->intUserProfileID, $strSQL);
            }
            if (!empty($intConnID)) {
              $arrConn = $CMS->CON->Get($intConnID);
              $tempQuery->Connect($arrConn['conn_host'], $arrConn['conn_schema'], $arrConn['conn_user'], $arrConn['conn_pass']);
              $arrResult = $tempQuery->DoResultQuery($strSQL, __CLASS__ . "::" . __FUNCTION__, __LINE__);
            } else {
              $arrResult = $CMS->ResultQuery($strSQL, __CLASS__ . "::" . __FUNCTION__, __LINE__);
            }
            //$arrResult = $CMS->ResultQuery($strSQL, __CLASS__ . "::" . __FUNCTION__, __LINE__);
            if (count($arrResult) > 0) {
              // Get template code
              $strPluginCode = $arrPlugins[$i]['widget_template'];
              if ((strpos($strPluginCode, '<cms:plugin-list>') !== false) &&
                  (strpos($strPluginCode, '</cms:plugin-list>') !== false)) {
                $strPluginCode = str_replace('<cms:plugin-list>', "", $strPluginCode);
                $strPluginCode = str_replace('</cms:plugin-list>', "", $strPluginCode);
              }
              if ((strpos($strPluginCode, '<cms:plugin-list-item>') !== false) &&
                  (strpos($strPluginCode, '</cms:plugin-list-item>') !== false)) {
                // Get list item code
                $intStart    = strpos($strPluginCode, '<cms:plugin-list-item>') + strlen('<cms:plugin-list-item>');
                $intEnd      = strpos($strPluginCode, '</cms:plugin-list-item>');
                $strItemCode = substr($strPluginCode, $intStart, $intEnd - $intStart);
                // Loop through items
                for ($j=0; $j<count($arrResult); $j++) {
                  $intItemID = 0;
                  $strItem = $strItemCode;
                  foreach ($arrResult[$j] as $key => $value) {
                    if ($key == "id") {
                      $intItemID = $value;
                    }
                    if (strpos($strItem, '{SQL_'.$key.'}') !== false) {
                      $strItem = str_replace('{SQL_'.$key.'}', $value, $strItem);
                    }
                  }
                  $strItem = str_replace('$intItemCount', ($j+1), $strItem);
                  if (strpos($strItem, '{CMS_PL_VIEWARTICLE}') !== false) {
                    $strArticleLink = $CMS->PL->ViewArticle($intItemID);
                    $strTitle = $CMS->ART->GetTitle($intItemID);
                    $strItem = str_replace('{CMS_PL_VIEWARTICLE}', "<a href=\"$strArticleLink\">$strTitle</a>", $strItem);
                  }
                  if (strpos($strItem, '{CMS_PL_VIEWCOMMENT}') !== false) {
                    $strCommentLink = $CMS->PL->ViewComment($intItemID);
                    $strThreadTitle = $CMS->COM->GetThreadTitle($intItemID);
                    $strItem = str_replace('{CMS_PL_VIEWCOMMENT}', "<a href=\"$strCommentLink\">$strThreadTitle</a>", $strItem);
                  }
                  if (strpos($strItem, '{CMS_PL_VIEWUSER}') !== false) {
                    $strUserLink = $CMS->PL->ViewUser($intItemID);
                    $strUsername = $CMS->US->GetNameFromID($intItemID);
                    $strItem = str_replace('{CMS_PL_VIEWUSER}', "<a href=\"$strUserLink\">$strUsername</a>", $strItem);
                  }
                  $strItemData .= $strItem;
                }
              }
            } else {
              $strPluginCode = "";
            }
            $strPluginCode = str_replace($strItemCode, $strItemData, $strPluginCode);
            $strPluginCode = str_replace('<cms:plugin-list-item>', "", $strPluginCode);
            $strPluginCode = str_replace('</cms:plugin-list-item>', "", $strPluginCode);
            $strHTML = str_replace($strVariable, $strPluginCode, $strHTML);
            if ($intConnID > 0) {
                $tempQuery->Disconnect();
            }
          }
        }
      }
      return $strHTML;
    }
    
    // ** New format for single plugin output ** //
    function BuildNamedPlugin($strPluginName) {
      global $CMS;
      global $strDBHost;
      global $strDBSchema;
      global $strDBAdminUser;
      global $strDBAdminPass;
      $strPluginCode = "";
      $strItemData = "";
      $strItemCode = "";
      $intCurrentUserID = $CMS->RES->GetCurrentUserID();
      $arrPluginData = $CMS->WGT->GetByVar($strPluginName);
      if (is_array($arrPluginData)) {
        $strVariable  = $arrPluginData['widget_variable'];
        $intConnID    = $arrPluginData['conn_id'];
        $intItemLimit = $arrPluginData['item_limit'];
        $strSQL       = $arrPluginData['query_string'];
        $strUSQL = strtoupper($strSQL);
        if ((strpos($strUSQL, "LIMIT") === false) && ($intItemLimit > 0)) {
          $strSQL .= " LIMIT $intItemLimit";
        }
        if (strpos($strSQL, '$intCurrentUserID') !== false) {
          if (empty($intCurrentUserID)) {
            $intCurrentUserID = 0;
          }
          $strSQL = str_replace('$intCurrentUserID', $intCurrentUserID, $strSQL);
        }
        if (strpos($strSQL, '$intUserID') !== false) {
          $strSQL = str_replace('$intUserID', $CMS->MV->intUserProfileID, $strSQL);
        }
        if (!empty($intConnID)) {
          $arrConn = $CMS->CON->Get($intConnID);
          $CMS->IQ->Disconnect();
          $CMS->IQ->Connect($arrConn['conn_host'], $arrConn['conn_schema'], $arrConn['conn_user'], $arrConn['conn_pass']);
        }
        $arrResult = $CMS->ResultQuery($strSQL, __CLASS__ . "::" . __FUNCTION__, __LINE__);
        if (count($arrResult) > 0) {
          // Get template code
          $strPluginCode = $arrPluginData['widget_template'];
          if ((strpos($strPluginCode, '<cms:plugin-list>') !== false) &&
              (strpos($strPluginCode, '</cms:plugin-list>') !== false)) {
            $strPluginCode = str_replace('<cms:plugin-list>', "", $strPluginCode);
            $strPluginCode = str_replace('</cms:plugin-list>', "", $strPluginCode);
          }
          if ((strpos($strPluginCode, '<cms:plugin-list-item>') !== false) &&
              (strpos($strPluginCode, '</cms:plugin-list-item>') !== false)) {
            // Get list item code
            $intStart    = strpos($strPluginCode, '<cms:plugin-list-item>') + strlen('<cms:plugin-list-item>');
            $intEnd      = strpos($strPluginCode, '</cms:plugin-list-item>');
            $strItemCode = substr($strPluginCode, $intStart, $intEnd - $intStart);
            // Loop through items
            for ($j=0; $j<count($arrResult); $j++) {
              $intItemID = 0;
              $strItem = $strItemCode;
              foreach ($arrResult[$j] as $key => $value) {
                if ($key == "id") {
                  $intItemID = $value;
                }
                if (strpos($strItem, '{SQL_'.$key.'}') !== false) {
                  $strItem = str_replace('{SQL_'.$key.'}', $value, $strItem);
                }
              }
              $strItem = str_replace('$intItemCount', ($j+1), $strItem);
              if (strpos($strItem, '{CMS_PL_VIEWARTICLE}') !== false) {
                $strArticleLink = $CMS->PL->ViewArticle($intItemID);
                $strTitle = $CMS->ART->GetTitle($intItemID);
                $strItem = str_replace('{CMS_PL_VIEWARTICLE}', "<a href=\"$strArticleLink\">$strTitle</a>", $strItem);
              }
              if (strpos($strItem, '{CMS_PL_VIEWCOMMENT}') !== false) {
                $strCommentLink = $CMS->PL->ViewComment($intItemID);
                $strThreadTitle = $CMS->COM->GetThreadTitle($intItemID);
                $strItem = str_replace('{CMS_PL_VIEWCOMMENT}', "<a href=\"$strCommentLink\">$strThreadTitle</a>", $strItem);
              }
              if (strpos($strItem, '{CMS_PL_VIEWUSER}') !== false) {
                $strUserLink = $CMS->PL->ViewUser($intItemID);
                $strUsername = $CMS->US->GetNameFromID($intItemID);
                $strItem = str_replace('{CMS_PL_VIEWUSER}', "<a href=\"$strUserLink\">$strUsername</a>", $strItem);
              }
              $strItemData .= $strItem;
            }
          }
        } else {
          $strPluginCode = "";
        }
        $strPluginCode = str_replace($strItemCode, $strItemData, $strPluginCode);
        $strPluginCode = str_replace('<cms:plugin-list-item>', "", $strPluginCode);
        $strPluginCode = str_replace('</cms:plugin-list-item>', "", $strPluginCode);
        //$strHTML = str_replace($strVariable, $strPluginCode, $strHTML);
        if ($intConnID > 0) {
          $CMS->IQ->Disconnect();
          $CMS->IQ->Connect($strDBHost, $strDBSchema, $strDBAdminUser, $strDBAdminPass);
        }
      }
      return $strPluginCode;
    }
  }

?>