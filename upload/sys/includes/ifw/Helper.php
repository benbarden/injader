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

class Helper extends IFWCore {
    
    // ** Helper methods ** //
    function Query($strQuery, $strSource = "", $intLineNo = 0) {
        global $CMS;
        $intID = $CMS->IQ->DoSingleQuery($strQuery, $strSource, $intLineNo);
        return $intID;
    }
    
    function ResultQuery($strQuery, $strSource = "", $intLineNo = 0) {
        global $CMS;
        $arrResult = $CMS->IQ->DoResultQuery($strQuery, $strSource, $intLineNo);
        if ($arrResult) {
            return $arrResult;
        }
    }
    
    function MultiQuery($strQuery, $strSource = "", $intLineNo = 0) {
        global $CMS;
        $arrResult = $CMS->IQ->DoMultiQuery($strQuery, $strSource, $intLineNo);
        if ($arrResult) {
            return $arrResult;
        }
    }
    
    // ** Error handling ** //
    function Err_MCatch($strErrMsg, $strErrData) {
        global $CMS;
        return $CMS->ERR->MCatch($strErrMsg, $strErrData);
    }
    
    function Err_MWarn($strErrMsg, $strErrData) {
        global $CMS;
        return $CMS->ERR->MWarn($strErrMsg, $strErrData);
    }
    
    function Err_MFail($strErrMsg, $strErrData) {
        global $CMS;
        $CMS->ERR->MFail($strErrMsg, $strErrData);
    }
    
}
