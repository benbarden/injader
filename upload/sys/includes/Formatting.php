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

  class Formatting extends Helper {
    // ** This class is only used for CMS codes ** //
    function CMSToHTML($strContent) {
      return $this->ConvertCMSToHTML($strContent);
    }
    function HTMLToCMS($strContent) {
      return $this->ConvertHTMLToCMS($strContent);
    }
    // Copied from elsewhere
    function StripAllTags($strInput){
      $arrSearch = array('@<script[^>]*?>.*?</script>@si', // Strip out javascript
                         '@<style[^>]*?>.*?</style>@siU',  // Strip style tags properly
                         '@<[\/\!]*?[^<>]*?>@si',          // Strip out HTML tags
                         '@<![\s\S]*?--[ \t\n\r]*>@'       // Multiline comments, CDATA
      );
      return preg_replace($arrSearch, "", $strInput);
    }
    // Conversions
    function ConvertHTMLToCMS($strContent) {
      global $CMS;
      $strContent = $CMS->StripSlashesIFW($strContent);
      $strContent = str_replace("<blockquote>",  "[quote]", $strContent);
      $strContent = str_replace("</blockquote>", "[/quote]", $strContent);
      $strContent = str_replace("<b>",    "[b]",    $strContent);
      $strContent = str_replace("</b>",   "[/b]",   $strContent);
      $strContent = str_replace("<i>",    "[i]",    $strContent);
      $strContent = str_replace("</i>",   "[/i]",   $strContent);
      $strContent = str_replace("<code>", "[code]", $strContent);
      $strContent = str_replace("</code>", "[/code]", $strContent);
      // Images
      $strContent = $this->ConvertImageHTMLToCMS($strContent);
      // Hyperlinks
      $strContent = $this->ConvertAnchorHTMLToLinkCMS($strContent);
      return $strContent;
    }
    function ConvertCMSToHTML($strContent) {
      $strContent = str_replace("[quote]",  "<blockquote>", $strContent);
      $strContent = str_replace("[/quote]", "</blockquote>", $strContent);
      $strContent = str_replace("[b]",    "<b>",    $strContent);
      $strContent = str_replace("[/b]",   "</b>",   $strContent);
      $strContent = str_replace("[i]",    "<i>",    $strContent);
      $strContent = str_replace("[/i]",   "</i>",   $strContent);
      $strContent = str_replace("[code]", "<code>", $strContent);
      $strContent = str_replace("[/code]", "</code>", $strContent);
      // Images
      $strContent = $this->ConvertImageCMSToHTML($strContent);
      // Hyperlinks
      $strContent = $this->ConvertLinkCMSToAnchorHTML($strContent);
      return $strContent;
    }
    ///////////////////////////////////////////////////////////////
    function ConvertAnchorHTMLToLinkCMS($strContent) {
      for ($i=0; $i<strlen($strContent); $i++) {
        $strTagStart = "<a href";
        if (substr($strContent, $i, strlen($strTagStart)) == $strTagStart) {
          $intStartPoint = $i;
          $blnFoundCloseTag = False;
          $j = $i;
          $strTagEnd = "</a>";
          while (($blnFoundCloseTag == False) && ($j < strlen($strContent))) {
            if (substr($strContent, $j, strlen($strTagEnd)) == $strTagEnd) {
              $blnFoundCloseTag = True;
              $intEndPoint = $j + strlen($strTagEnd);
            } else {
              $blnFoundCloseTag = False;
              $j++;
            }
          }
          if ($blnFoundCloseTag) {
            $strOldCode = substr($strContent, $intStartPoint, ($intEndPoint - $intStartPoint));
            $strNewCode = $this->ConvertAnchorToLink($strOldCode);
            $strContent = str_replace($strOldCode, $strNewCode, $strContent);
          }
        }
      }
      return $strContent;
    }
    function ConvertAnchorToLink($strContent) {
      global $CMS;
      $strContent = $CMS->StripSlashesIFW($strContent);
      $arrContent = preg_split('<a href=|</a>|>', $strContent);
      $intItemCount = count($arrContent);
      $intUsedItemCount = 1;
      for ($i=0; $i<$intItemCount; $i++) {
        if ($arrContent[$i] != "") {
          switch ($intUsedItemCount) {
            case 1:
              $strURL = str_replace('"', '', $arrContent[$i]);
              $intUsedItemCount++;
              break;
            case 2:
              $strText = $arrContent[$i];
              $intUsedItemCount++;
              break;
          }
        }
      }
      $strLink = "[link=".$strURL."]".$strText."[/link]";
      return $strLink;
    }
    ///////////////////////////////////////////////////////////////
    function ConvertLinkCMSToAnchorHTML($strContent) {
      for ($i=0; $i<strlen($strContent); $i++) {
        $strTagStart = "[link=";
        if (substr($strContent, $i, strlen($strTagStart)) == $strTagStart) {
          $intStartPoint = $i;
          $blnFoundCloseTag = False;
          $j = $i;
          $strTagEnd = "[/link]";
          while (($blnFoundCloseTag == False) && ($j < strlen($strContent))) {
            if (substr($strContent, $j, strlen($strTagEnd)) == $strTagEnd) {
              $blnFoundCloseTag = True;
              $intEndPoint = $j + strlen($strTagEnd);
            } else {
              $blnFoundCloseTag = False;
              $j++;
            }
          }
          if ($blnFoundCloseTag) {
            $strOldCode = substr($strContent, $intStartPoint, ($intEndPoint - $intStartPoint));
            $strNewCode = $this->ConvertLinkToAnchor($strOldCode);
            $strContent = str_replace($strOldCode, $strNewCode, $strContent);
          }
        }
      }
      return $strContent;
    }
    function ConvertLinkToAnchor($strContent) {
      $arrContent = preg_split('\[link=|\[/link\]|\]', $strContent);
      $intItemCount = count($arrContent);
      $intUsedItemCount = 1;
      for ($i=0; $i<$intItemCount; $i++) {
        if ($arrContent[$i] != "") {
          switch ($intUsedItemCount) {
            case 1:
              $strURL = $arrContent[$i];
              $intUsedItemCount++;
              break;
            case 2:
              $strText = $arrContent[$i];
              $intUsedItemCount++;
              break;
          }
        }
      }
      $strAnchor = "<a href=\"$strURL\">$strText</a>";
      return $strAnchor;
    }
    ///////////////////////////////////////////////////////////////
    function ConvertImageCMSToHTMLSingle($strContent) {
      // Expected format: [img src=http://www.itcould.com/myimage.jpg]image description[/img]
      $arrContent = preg_split('\[img src=|]|\[/img\]|\]', $strContent);
      $intItemCount = count($arrContent);
      $intUsedItemCount = 1;
      for ($i=0; $i<$intItemCount; $i++) {
        if ($arrContent[$i] != "") {
          switch ($intUsedItemCount) {
            case 1:
              $strURL = $arrContent[$i];
              $intUsedItemCount++;
              break;
            case 2:
              $strText = $arrContent[$i];
              $intUsedItemCount++;
              break;
          }
        }
      }
      $strImage = "<img src=\"$strURL\" alt=\"$strText\" />";
      return $strImage;
    }
    function ConvertImageHTMLToCMS($strContent) {
      global $CMS;
      $strContent = $CMS->StripSlashesIFW($strContent);
      for ($i=0; $i<strlen($strContent); $i++) {
        $strTagStart = "<img src";
        if (substr($strContent, $i, strlen($strTagStart)) == $strTagStart) {
          $intStartPoint = $i;
          $blnFoundCloseTag = False;
          $j = $i;
          $strTagEnd = " />";
          while ($blnFoundCloseTag == False) {
            if (substr($strContent, $j, strlen($strTagEnd)) == $strTagEnd) {
              $blnFoundCloseTag = True;
              $intEndPoint = $j + strlen($strTagEnd);
            } else {
              $blnFoundCloseTag = False;
              $j++;
            }
          }
          if ($blnFoundCloseTag) {
            $strOldCode = substr($strContent, $intStartPoint, ($intEndPoint - $intStartPoint));
            $strNewCode = $this->ConvertImageHTMLToCMSSingle($strOldCode);
            $strContent = str_replace($strOldCode, $strNewCode, $strContent);
          }
        }
      }
      return $strContent;
    }
    ///////////////////////////////////////////////////////////////
    function ConvertImageCMSToHTML($strContent) {
      for ($i=0; $i<strlen($strContent); $i++) {
        $strTagStart = "[img src=";
        if (substr($strContent, $i, strlen($strTagStart)) == $strTagStart) {
          $intStartPoint = $i;
          $blnFoundCloseTag = False;
          $j = $i;
          $strTagEnd = "[/img]";
          while ($blnFoundCloseTag == False) {
            if (substr($strContent, $j, strlen($strTagEnd)) == $strTagEnd) {
              $blnFoundCloseTag = True;
              $intEndPoint = $j + strlen($strTagEnd);
            } else {
              $blnFoundCloseTag = False;
              $j++;
            }
          }
          if ($blnFoundCloseTag) {
            $strOldCode = substr($strContent, $intStartPoint, ($intEndPoint - $intStartPoint));
            $strNewCode = $this->ConvertImageCMSToHTMLSingle($strOldCode);
            $strContent = str_replace($strOldCode, $strNewCode, $strContent);
          }
        }
      }
      return $strContent;
    }
    function ConvertImageHTMLToCMSSingle($strContent) {
      // Expected format: <img src="image.jpg" alt="description" />
      $arrContent = preg_split('<img src=\"|\" alt=\"|\" />', $strContent);
      $intItemCount = count($arrContent);
      $intUsedItemCount = 1;
      for ($i=0; $i<$intItemCount; $i++) {
        if ($arrContent[$i] != "") {
          switch ($intUsedItemCount) {
            case 1:
              $strURL = $arrContent[$i];
              $intUsedItemCount++;
              break;
            case 2:
              $strText = $arrContent[$i];
              $intUsedItemCount++;
              break;
          }
        }
      }
      $strImage = "[img src=".$strURL."]".$strText."[/img]";
      return $strImage;
    }
  }

?>