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

  class Thumb extends Helper {
    function Make($strSourceFile, $strThumbFile, $intSize) {
      global $CMS;
      // Create an image so we can do the resize
      if (preg_match('/JPG|JPEG/', strtoupper($strSourceFile))){
        $imgSrc = imagecreatefromjpeg($strSourceFile);
      } elseif (preg_match('/PNG/', strtoupper($strSourceFile))){
        $imgSrc = imagecreatefrompng($strSourceFile);
      } else {
        return false;
      }
      // Capture the original size of the uploaded image
      list($intSrcWidth, $intSrcHeight) = getimagesize($strSourceFile);
      $strKeepAspect = $CMS->SYS->GetSysPref(C_PREF_THUMB_KEEPASPECT);
      if ($strKeepAspect == "Y") {
        if ($intSrcWidth > $intSrcHeight) {
          $intDstWidth = $intSize;
          $intDstHeight = ($intSrcHeight/$intSrcWidth) * $intSize;
        } else {
          $intDstHeight = $intSize;
          $intDstWidth = ($intSrcWidth/$intSrcHeight) * $intSize;
        }
      } else {
        $intDstWidth  = $intSize;
        $intDstHeight = $intSize;
      }
      $imgDst = imagecreatetruecolor($intDstWidth, $intDstHeight);
      // Resize image
      imagecopyresampled($imgDst, $imgSrc, 0, 0, 0, 0, $intDstWidth, $intDstHeight, $intSrcWidth, $intSrcHeight);
      // Save resized image
      $strExtension = $this->GetExtensionFromPath($strSourceFile);
      if (preg_match('/PNG/', strtoupper($strExtension))) {
        imagepng($imgDst, $strThumbFile, 0);
      } else {
        imagejpeg($imgDst, $strThumbFile, 90); //, $intSize);
      }
      imagedestroy($imgDst);
      imagedestroy($imgSrc);
      return true;
    }
  }

?>