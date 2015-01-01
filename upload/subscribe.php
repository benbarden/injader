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
  $strPageTitle = "Subscription Manager";
  $strAction = empty($_GET['action']) ? "" : $_GET['action'];
  $strConfMsg = "";
  
  // POST data
  if ($_POST) {
    switch ($strAction) {
      case "setemail":
        $strEmail = empty($_POST['txtEmail']) ? "" : $_POST['txtEmail'];
        if ($CMS->IsValidEmail($strEmail)) {
          $intCookieDuration = $CMS->SYS->GetGuestCookieDuration();
          if ($CMS->CK->Get(C_CK_COMMENT_EMAIL) != $strEmail) {
            $CMS->CK->Set(C_CK_COMMENT_EMAIL, $strEmail, $intCookieDuration);
          }
          $strHTML = <<<SubscribeOptions
<h1>$strPageTitle</h1>
<p>Thanks for entering your email address. <a href="{FN_SUBSCRIBE}">Proceed to the Subscription Manager</a></p>

SubscribeOptions;
          $strHTML = $CMS->RC->DoAll($strHTML);
          $CMS->MV->DefaultPage($strPageTitle, $strHTML);
        } else {
          $strEmail = "";
          $strConfMsg = "<p><b>This is not a valid email address. Please try again.</b></p>";
        }
        break;
      case "clearcookies":
        $CMS->CK->Clear(C_CK_COMMENT_NAME);
        $CMS->CK->Clear(C_CK_COMMENT_EMAIL);
        $CMS->CK->Clear(C_CK_COMMENT_URL);
        $strHTML = <<<SubscribeOptions
<h1>$strPageTitle</h1>
<p>Successfully cleared all comment form cookies. <a href="{FN_SUBSCRIBE}">Proceed to the Subscription Manager</a></p>

SubscribeOptions;
        $strHTML = $CMS->RC->DoAll($strHTML);
        $CMS->MV->DefaultPage($strPageTitle, $strHTML);
        break;
      case "unsubscribe":
        // Get current email address
        $strEmail = $CMS->UST->GetEmail();
        // Get user subscriptions
        $strMySubscriptions = $CMS->UST->GetSubscriptions($strEmail);
        // Compare to selected options
        $arrSubs = explode(",", $strMySubscriptions);
        $strNewSubs = "";
        $j = 0;
        for ($i=0; $i<count($arrSubs); $i++) {
          $intCurrentID = $arrSubs[$i];
          if (!isset($_POST['chkUnsubList'.$intCurrentID])) {
            if ($j == 0) {
              $strNewSubs = $intCurrentID;
            } else {
              $strNewSubs .= ",".$intCurrentID;
            }
            $j++;
          }
        }
        // Update subscriptions
        if ($strNewSubs != $strMySubscriptions) {
          // Manual update to avoid auto-appending of commas
          $CMS->Query("
          UPDATE {IFW_TBL_USER_STATS}
          SET article_subscriptions = '$strNewSubs'
          WHERE user_email = '$strEmail'
          ", basename(__FILE__), __LINE__);
        }
        // Confirm
        $strHTML = <<<SubscribeOptions
<h1>$strPageTitle</h1>
<p>Successfully unsubscribed from the selected articles. <a href="{FN_SUBSCRIBE}">Proceed to the Subscription Manager</a></p>

SubscribeOptions;
        $strHTML = $CMS->RC->DoAll($strHTML);
        $CMS->MV->DefaultPage($strPageTitle, $strHTML);
        break;
    }
  }
  
  // Get current email address
  $strEmail = $CMS->UST->GetEmail();
  
  switch ($strAction) {
    case "clearcookies":
      $strHTML = <<<ClearCookies
<h1>$strPageTitle</h1>
<p>This will clear the comment form cookies (name, email, and URL) for this site.</p>
<form action="{FN_SUBSCRIBE}?action=clearcookies" method="post">
<input type="hidden" name="dummy" value="dummy" />
<input type="submit" value="Proceed" />
</form>

ClearCookies;
      break;
    default:
      if ($strEmail) {
        // Subscription Manager
        $strHTML = <<<SubscribeOptions
<h1>$strPageTitle</h1>
<p>Your email is: <b>$strEmail</b></p>
<p>If this is not your email address or you wish to check another account, you can
<a href="{FN_SUBSCRIBE}?action=clearcookies">clear the comment form cookies for this site</a>.</p>

SubscribeOptions;
        // Get user subscriptions
        $blnShowForm = false;
        $strMySubscriptions = $CMS->UST->GetSubscriptions($strEmail);
        if ($strMySubscriptions) {
          $arrSubArticleData = $CMS->ResultQuery("
          SELECT id, title, permalink
          FROM {IFW_TBL_CONTENT}
          WHERE id IN ($strMySubscriptions)
          AND content_status = '{C_CONT_PUBLISHED}'
          ORDER BY create_date DESC
          ", basename(__FILE__), __LINE__);
          for ($i=0; $i<count($arrSubArticleData); $i++) {
            if ($i == 0) {
              $blnShowForm = true;
              $strHTML .= <<<UnsubForm
<h2>Your Subscriptions</h2>
<p>You are currently subscribed to the content shown below. To unsubscribe, check the item(s) you wish to remove,
then click the Unsubscribe button.</p>
<form action="{FN_SUBSCRIBE}?action=unsubscribe" method="post">

UnsubForm;
            }
            $intSubArticleID    = $arrSubArticleData[$i]['id'];
            $strSubArticleTitle = $arrSubArticleData[$i]['title'];
            $permalink  = $arrSubArticleData[$i]['permalink'];
            $strHTML .= <<<UnsubItem
<input type="checkbox" name="chkUnsubList$intSubArticleID" id="chkUnsubList$intSubArticleID" />
<label for="chkUnsubList$intSubArticleID">$strSubArticleTitle</label> -
<a href="$permalink">view article: $strSubArticleTitle</a>
<br />

UnsubItem;
          }
        }
        if ($blnShowForm) {
          $strHTML .= <<<UnsubForm
<br /><input type="submit" value="Unsubscribe" />
</form>

UnsubForm;
        } else {
          $strHTML .= <<<SubscribeList
<h2>Your Subscriptions</h2>
<p>You are not subscribed to any content on this site.</p>

SubscribeList;
        }
      } else {
        // Enter email address
        $strHTML = <<<EnterYourEmail
<h1>$strPageTitle</h1>
$strConfMsg
<p>To access subscription options for this site, please enter your email address.</p>
<form action="{FN_SUBSCRIBE}?action=setemail" method="post">
<label for="txtEmail">Email:</label>
<input type="text" id="txtEmail" name="txtEmail" size="30" maxlength="100" />
<input type="submit" value="Submit" />
</form>

EnterYourEmail;
      }
      break;
  }
  $strHTML = $CMS->RC->DoAll($strHTML);
  $CMS->MV->DefaultPage($strPageTitle, $strHTML);
?>