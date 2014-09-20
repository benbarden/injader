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

  class RSSParser extends Helper {
    var $insideitem = false;
    var $strTagName = "";
    var $intItemCount;
    var $intMaxItems;
    var $strRootNode = "";
    var $arrFeedItems = "";
    var $arrItemData = "";
    function startElement($parser, $tagName, $attrs) {
      if ($this->intItemCount < ($this->intMaxItems + 1)) {
        if ($this->insideitem) {
          $this->strTagName = $tagName;
        } elseif ($tagName == $this->strRootNode) {
          $this->insideitem = true;
        }
      }
    }
    function endElement($parser, $tagName) {
      if ($this->intItemCount < ($this->intMaxItems + 1)) {
        if ($tagName == $this->strRootNode) {
          foreach ($this->arrItemData[$this->intItemCount] as $key=>$value) {
            $this->arrFeedItems[$this->intItemCount][$key] = trim($value);
          }
          $this->intItemCount++;
          $this->insideitem = false;
        }
      }
    }
    function characterData($parser, $data) {
      if ($this->insideitem) {
        if (!empty($this->intItemCount) && !empty($this->strTagName)) {
          $this->arrItemData[$this->intItemCount][$this->strTagName] .= $data;
        }
      }
    }
    function ParserError($XMLParser) {
      unset($this->arrFeedItems);
      unset($this->arrItemData);
      $this->arrFeedItems['error_code']    = xml_get_error_code($XMLParser);
      $this->arrFeedItems['line_number']   = xml_get_current_line_number($XMLParser);
      $this->arrFeedItems['column_number'] = xml_get_current_column_number($XMLParser);
    }
    function IOError($strData) {
      unset($this->arrFeedItems);
      unset($this->arrItemData);
      $this->arrFeedItems['error_desc'] = M_ERR_IO_FAILURE;
    }
    function BuildArray($strRSSPath, $strNodeName, $intMaxItems) {
      unset($this->arrFeedItems);
      unset($this->arrItemData);
      $XMLParser = xml_parser_create();
      xml_set_object($XMLParser, $this);
      xml_set_element_handler($XMLParser, "startElement", "endElement");
      xml_set_character_data_handler($XMLParser, "characterData");
      $this->intItemCount = 1;
      $this->intMaxItems = $intMaxItems;
      $this->strRootNode = $strNodeName;
      $this->arrFeedItems = array();
      $this->arrItemData = array();
      for ($i=1; $i<$intMaxItems+1; $i++) {
        $this->arrItemData[$i] = array("DC:DATE" => "00:00:00", "TITLE" => "", "LINK" => "", "DESCRIPTION" => "");
      }
      @$fp = fopen($strRSSPath,"r");
      if ($fp) {
        while ($data = fread($fp, 4096)) {
          if (!xml_parse($XMLParser, $data, feof($fp))) {
            $this->ParserError($XMLParser);
            @fclose($fp);
            xml_parser_free($XMLParser);
            return $this->arrFeedItems;
          }
        }
        fclose($fp);
        xml_parser_free($XMLParser);
      } else {
        $this->IOError($strRSSPath);
      }
      return $this->arrFeedItems;
    }
  } 
?>