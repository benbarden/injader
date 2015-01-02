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

  // Articles
  define('AL_TAG_ARTICLE_VIEW',        "ArticleView");
  define('AL_TAG_ARTICLE_CREATE',      "ArticleCreate");
  define('AL_TAG_ARTICLE_EDIT',        "ArticleEdit");
  define('AL_TAG_ARTICLE_EDITTAGS',    "ArticleEditTags"); // Admin-only
  define('AL_TAG_ARTICLE_MARK',        "ArticleMarkForDeletion");
  define('AL_TAG_ARTICLE_UNMARK',      "ArticleUnmarkForDeletion");
  define('AL_TAG_ARTICLE_DELETE',      "ArticleDelete");
  define('AL_TAG_ARTICLE_RESTORE',     "ArticleRestore");
  define('AL_TAG_ARTICLE_SAVEDRAFT',   "ArticleSaveDraft");
  define('AL_TAG_ARTICLE_REVIEW',      "ArticleReview");
  define('AL_TAG_ARTICLE_PUBLISH',     "ArticlePublish");
  define('AL_TAG_ARTICLE_SCHEDULE',    "ArticleSchedule");
  
  // Articles - Bulk actions
  define('AL_TAG_ARTICLE_BULKMOVE',       "ArticleBulkMove");
  define('AL_TAG_ARTICLE_BULKEDITAUTHOR', "ArticleEditAuthor");
  define('AL_TAG_ARTICLE_BULKDELETE',     "ArticleBulkDelete");
  define('AL_TAG_ARTICLE_BULKRESTORE',    "ArticleBulkRestore");
  
  // Files
  define('AL_TAG_FILE_VIEW',         "FileView");
  define('AL_TAG_FILE_DOWNLOAD',     "FileDownload");
  define('AL_TAG_FILE_CREATE',       "FileCreate");
  define('AL_TAG_FILE_EDIT',         "FileEdit");
  define('AL_TAG_FILE_DELETE',       "FileDelete");
  define('AL_TAG_FILE_LOCK',         "FileLock");
  define('AL_TAG_FILE_UNLOCK',       "FileUnlock");
  
  // Areas
  define('AL_TAG_AREA_VIEW',    "AreaView");
  define('AL_TAG_AREA_CREATE',  "AreaCreate");
  define('AL_TAG_AREA_EDIT',    "AreaEdit");
  define('AL_TAG_AREA_DELETE',  "AreaDelete");
  define('AL_TAG_AREA_REORDER', "AreaReorder");

  // User Groups
  define('AL_TAG_USERGROUP_CREATE', "UserGroupCreate");
  define('AL_TAG_USERGROUP_EDIT',   "UserGroupEdit");
  define('AL_TAG_USERGROUP_DELETE', "UserGroupDelete");
  
  // Permission Profiles
  define('AL_TAG_PPCA_CREATE', "PerProfileCACreate");
  define('AL_TAG_PPCA_DELETE', "PerProfileCADelete");
  define('AL_TAG_PPCA_EDIT',   "PerProfileCAEdit");
  define('AL_TAG_PPSYS_EDIT',  "PerProfileSYSEdit");
  
  // User
  define('AL_TAG_AVATAR_DELETE',     "AvatarDelete");
  define('AL_TAG_AVATAR_SET',        "AvatarSet");
  define('AL_TAG_AVATAR_UNSET',      "AvatarUnset");
  define('AL_TAG_USER_EDIT',         "UserEdit");
  define('AL_TAG_USER_EDITPASSWORD', "UserEditPassword");
  define('AL_TAG_USER_EDITPROFILE',  "UserEditProfile");
  define('AL_TAG_USER_LOGIN',        "UserLogin");
  define('AL_TAG_USER_LOGIN_FAIL',   "UserLoginFail");
  define('AL_TAG_USER_LOGOUT',       "UserLogout");
  define('AL_TAG_USER_REGISTER',     "UserRegister");
  define('AL_TAG_USER_REINSTATE',    "UserReinstate");
  define('AL_TAG_USER_SUSPEND',      "UserSuspend");

  // User Sessions
  define('AL_TAG_USER_SESSION_DELETE',         "UserSessionDelete");
  define('AL_TAG_USER_SESSION_DELETE_EXPIRED', "UserSessionDeleteExpired");
