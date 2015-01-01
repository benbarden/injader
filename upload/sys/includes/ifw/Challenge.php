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

  class Challenge extends Helper {
    var $strMessage;
    function SetMessage($strMessage) {
      session_start();
      $this->strMessage = $strMessage;
      $_SESSION['txtAnswer'] = password_hash($strMessage, PASSWORD_BCRYPT);
    }
    function GetMessage() {
      return $this->strMessage;
    }
    function BuildMessage() {
      $imgOutput = imagecreatefrompng(ABS_ROOT."sys/includes/ifw/injader.png");
      $intImageX = (imagesx($imgOutput) - strlen($this->strMessage)) / 5; // 20
      $intImageY = imagesy($imgOutput) - (imagesy($imgOutput) / 3); // 65
      $imgTextColour = imagecolorallocate($imgOutput, 0, 0, 0);
      ImageTTFText($imgOutput, 25, -5, $intImageX, $intImageY, $imgTextColour, ABS_ROOT."sys/includes/ifw/FoundationRegular.ttf", $this->strMessage);
      header("Content-Type: image/png");
      header("Pragma: public"); // required
      header("Expires: 0");
      header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
      header("Cache-Control: private", false); // required for certain browsers
      header("Content-Transfer-Encoding: binary");
      header("Content-Disposition: attachment; filename=disp.png");
      imagepng($imgOutput);
      readfile($imgOutput);
      imagedestroy($imgOutput);
      exit;
    }
  }

