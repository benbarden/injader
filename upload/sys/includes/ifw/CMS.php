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

class CMS extends Helper {
    
    // Core system
    var $ERR; // Error
    var $Cache; // ICache
    var $CacheFile; // ICacheFile
    var $CacheBuild; // ICacheBuild
    var $IQ; // IQuery
    var $RES; // Restriction
    var $SYS; // System

    // Top-level
    var $CK; // Cookie
    var $FMT; // Formatting
    var $MV; // View
    var $MSG; // Messaging
    var $PL; // PageLink
    var $PN; // PageNumber
    var $RC; // ReplaceConstants

    // DB
    var $AL; // AccessLog
    var $AR; // Area
    var $AT; // AreaTraverse
    var $ART; // Article
    var $CAT; // Category;
    var $FL; // File
    var $PP; // PermissionProfile
    var $TG; // Tag
    var $UM; // URLMapping
    var $US; // User
    var $UG; // UserGroup
    var $USess; // UserSession

    // HTML
    var $AP; // AdminPage
    var $ARCO; // AreaContent
    var $AC; // Autocode
    var $DD; // DropDown
    var $LP; // LoginPage
    var $PNN; // PageNumberNavigation
    var $TH; // Theme
    var $TS; // ThemeSetting

    function InitClasses() {
        
        // Generic initialisation routine
        $this->ERR   = new Error;
        $this->Cache = new ICache;
        $this->CacheFile  = new ICacheFile;
        $this->CacheBuild = new ICacheBuild;
        $this->IQ    = new IQuery;
        $this->RES   = new Restriction;
        $this->SYS   = new System;
        
        // Top level
        $this->CK    = new Cookie;
        $this->FMT   = new Formatting;
        $this->MV    = new View;
        $this->MSG   = new Messaging;
        $this->PL    = new PageLink;
        $this->PN    = new PageNumber;
        $this->RC    = new ReplaceConstants;

        // DB
        $this->AL    = new AccessLog;
        $this->AR    = new Area;
        $this->AT    = new AreaTraverse;
        $this->ART   = new Article;
        $this->CAT   = new Category;
        $this->FL    = new File;
        $this->PP    = new PermissionProfile;
        $this->TG    = new Tags;
        $this->UM    = new URLMapping;
        $this->US    = new User;
        $this->UG    = new UserGroup;
        $this->USess = new UserSession;

        // HTML
        $this->AP    = new AdminPage;
        $this->ARCO  = new AreaContent;
        $this->AC    = new Autocode;
        $this->DD    = new DropDown;
        $this->LP    = new LoginPage;
        $this->PNN   = new PageNumberNavigation;
        $this->TH    = new Theme;
        $this->TS    = new ThemeSetting;

    }
    
}