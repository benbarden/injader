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

class Widget extends Helper {
    
    /**
     * Creates a new widget
     * @param $strName
     * @param $strVersion
     * @param $intConnID
     * @param $strUCPLink
     * @param $strACPLink
     * @param $strSQL
     * @param $intItemLimit
     * @param $strVariable
     * @param $strTemplate
     * @param $strType
     * @return integer
     */
    function Create($strName, $strVersion, $intConnID, $strUCPLink, $strACPLink, $strSQL, 
        $intItemLimit, $strVariable, $strTemplate, $strType) {
        
        global $CMS;
        $intID = $this->Query("
            INSERT INTO {IFW_TBL_WIDGETS}
            (name, version, conn_id, ucp_link, acp_link, query_string, item_limit, 
            widget_variable, widget_template, widget_type)
            VALUES('$strName', '$strVersion', $intConnID, '$strUCPLink', '$strACPLink', 
            '$strSQL', $intItemLimit, '$strVariable', '$strTemplate', '$strType')
        ", __CLASS__ . "::" . __FUNCTION__, __LINE__);
        $CMS->AL->Build(AL_TAG_PLUGIN_CREATE, $intID, $strName);
        return $intID;
        
    }
    
    /**
     * Edits an existing widget
     * @param $intID
     * @param $strName
     * @param $strVersion
     * @param $intConnID
     * @param $strUCPLink
     * @param $strACPLink
     * @param $strSQL
     * @param $intItemLimit
     * @param $strVariable
     * @param $strTemplate
     * @return void
     */
    function Edit($intID, $strName, $strVersion, $intConnID, $strUCPLink, $strACPLink, 
        $strSQL, $intItemLimit, $strVariable, $strTemplate) {
        
        global $CMS;
        $this->Query("
            UPDATE {IFW_TBL_WIDGETS}
            SET name = '$strName',
            version = '$strVersion',
            conn_id = $intConnID,
            ucp_link = '$strUCPLink', acp_link = '$strACPLink',
            query_string = '$strSQL', item_limit = $intItemLimit,
            widget_variable = '$strVariable', widget_template = '$strTemplate'
            WHERE id = $intID
        ", __CLASS__ . "::" . __FUNCTION__, __LINE__);
        $CMS->AL->Build(AL_TAG_PLUGIN_EDIT, $intID, $strName);
        
    }
    
    /**
     * Deletes a widget
     * @param $intID
     * @return void
     */
    function Delete($intID) {
        
        global $CMS;
        $arrWidget = $this->Get($intID);
        $strName = $arrWidget['name'];
        $this->Query("
            DELETE FROM {IFW_TBL_WIDGETS} WHERE id = $intID
        ", __CLASS__ . "::" . __FUNCTION__, __LINE__);
        $CMS->AL->Build(AL_TAG_PLUGIN_DELETE, $intID, $strName);
        
    }
    
    /**
     * Gets all widgets
     * @return array
     */
    function GetAll() {
        
        $arrWidgets = $this->ResultQuery("
            SELECT * FROM {IFW_TBL_WIDGETS} ORDER BY name ASC
        ", __CLASS__ . "::" . __FUNCTION__, __LINE__);
        return $arrWidgets;
        
    }
    
    /**
     * Gets all widgets ordered by variable, longest variable first
     * @return array
     */
    function GetAllVarDesc() {
        
        $arrWidgets = $this->ResultQuery("
            SELECT * FROM {IFW_TBL_WIDGETS}
            ORDER BY LENGTH(widget_variable) DESC
        ", __CLASS__ . "::" . __FUNCTION__, __LINE__);
        return $arrWidgets;
        
    }
    
    /**
     * Gets a widget
     * @param $intID
     * @return array
     */
    function Get($intID) {
        
        $arrWidget = $this->ResultQuery("
            SELECT * FROM {IFW_TBL_WIDGETS} WHERE id = $intID
        ", __CLASS__ . "::" . __FUNCTION__, __LINE__);
        return $arrWidget[0];
        
    }
    
    /**
     * Gets a widget from its variable name
     * @param $strVarName
     * @return array
     */
    function GetByVar($strVarName) {
        
        $arrWidget = $this->ResultQuery("
            SELECT * FROM {IFW_TBL_WIDGETS}
            WHERE UPPER(widget_variable) = UPPER('$strVarName')
        ", __CLASS__ . "::" . __FUNCTION__, __LINE__);
        return !empty($arrWidget[0]) ? $arrWidget[0] : "";
        
    }
    
    /**
     * Verifies whether a widget exists
     * @param $strVarName
     * @param $intID
     * @return unknown_type
     */
    function VarExists($strVarName, $intID) {
        
        if ($intID) {
            $strWhereClause = " AND id <> $intID";
        } else {
            $strWhereClause = "";
        }
        $arrWidget = $this->ResultQuery("
            SELECT id FROM {IFW_TBL_WIDGETS}
            WHERE UPPER(widget_variable) = UPPER('$strVarName')
            $strWhereClause
        ", __CLASS__ . "::" . __FUNCTION__, __LINE__);
        return !empty($arrWidget[0]) ? true : false;
        
    }
    
    /**
     * Gets the number of widgets on the site
     * @return integer
     */
    function GetCount() {
        
        $arrCount = $this->ResultQuery("
            SELECT count(*) AS count FROM {IFW_TBL_WIDGETS}
        ", __CLASS__ . "::" . __FUNCTION__, __LINE__);
        return $arrCount[0]['count'];
        
    }
    
}
?>