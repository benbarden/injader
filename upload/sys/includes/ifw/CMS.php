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

class CMS extends Helper {
    
    // Core system
    var $ERR; // Error
    var $Cache; // ICache
    var $CacheFile; // ICacheFile
    var $CacheBuild; // ICacheBuild
    var $IP; // IPacker
    var $IQ; // IQuery
    var $RES; // Restriction
    var $SYS; // System
    
    var $pages_Archives;
    
    // Class caching
    var $AC; // Autocode
    var $AL; // AccessLog
    var $AP; // AdminPage
    var $AR; // Area
    var $ARCO; // AreaContent
    var $ART; // Article
    var $AT; // AreaTraverse
    var $CHA; // Challenge
    var $CK; // Cookie
    var $COM; // Comment
    var $CON; // Connection
    var $DD; // DropDown
    var $FL; // File
    var $FMT; // Formatting
    var $FR; // FormRecipient
    var $LP; // LoginPage
    var $MV; // View
    var $MSG; // Messaging
    var $PL; // PageLink
    var $PN; // PageNumber
    var $PNN; // PageNumberNavigation
    var $PP; // PermissionProfile
    var $RAT; // Rating
    var $RC; // ReplaceConstants
    var $SR; // SortRule
    var $TG; // Tag
    var $TH; // Theme
    var $TS; // ThemeSetting
    var $UG; // UserGroup
    var $UM; // URLMapping
    var $US; // User
    var $USess; // UserSession
    var $UST; // UserStat
    var $UV; // UserVariable
    var $WGT; // Widget
    
    function InitClasses() {
        
        // Generic initialisation routine
        $this->ERR   = new Error;
        $this->Cache = new ICache;
        $this->CacheFile  = new ICacheFile;
        $this->CacheBuild = new ICacheBuild;
        $this->IP    = new IPacker;
        $this->IQ    = new IQuery;
        $this->RES   = new Restriction;
        $this->SYS   = new System;
        
        $this->pages_Archives = new pages_Archives;
        
        $this->AC    = new Autocode;
        $this->AL    = new AccessLog;
        $this->AP    = new AdminPage;
        $this->AR    = new Area;
        $this->ARCO  = new AreaContent;
        $this->ART   = new Article;
        $this->AT    = new AreaTraverse;
        $this->CHA   = new Challenge;
        $this->CK    = new Cookie;
        $this->COM   = new Comment;
        $this->CON   = new Connection;
        $this->DD    = new DropDown;
        $this->FL    = new File;
        $this->FMT   = new Formatting;
        $this->FR    = new FormRecipient;
        $this->LP    = new LoginPage;
        $this->MV    = new View;
        $this->MSG   = new Messaging;
        $this->PL    = new PageLink;
        $this->PN    = new PageNumber;
        $this->PNN   = new PageNumberNavigation;
        $this->PP    = new PermissionProfile;
        $this->RAT   = new Ratings;
        $this->RC    = new ReplaceConstants;
        $this->SR    = new SpamRule;
        $this->TG    = new Tags;
        $this->TH    = new Theme;
        $this->TS    = new ThemeSetting;
        $this->UG    = new UserGroup;
        $this->UM    = new URLMapping;
        $this->US    = new User;
        $this->USess = new UserSession;
        $this->UST   = new UserStats;
        $this->UV    = new UserVariable;
        $this->WGT   = new Widget;
        
    }
    
}
?>