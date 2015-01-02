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

  // ** Core ** //
  define('M_ERR_PAGE_TITLE', "System Message");
  define('M_ERR_PAGE_INFO_DESC', "Supporting information");
  define('M_ERR_PAGE_INFO_NONE', "None provided");
  define('M_INFO_GO_BACK', "Go back to the previous page");
  
  // ** Assigned to HTTP error codes ** //
  // 401 Authorization Required
  define('M_ERR_NOT_LOGGED_IN', "You must be registered and logged in to view this page.");
  // 403 Forbidden
  define('M_ERR_UNAUTHORISED', "You are not authorised to view this page.");
  // 404 Not Found
  define('M_ERR_NO_ROWS_RETURNED', "The page cannot be found.");

  // ** General ** //
  define('M_ERR_FIELD_REQUIRED', "This field is required.");
  define('M_ERR_INVALID_CHARS', "You have entered invalid characters. You can only use the characters A-Z (uppercase or lowercase), the numbers 0-9, spaces, underscores and hyphens.");
  define('M_ERR_INVALID_EMAIL', "You have entered an invalid email address.");
  define('M_ERR_MISSING_SEARCH_PARAMS', "Please enter a search string, or enter one or more valid tags.");
  define('M_ERR_TAGS_NOT_FOUND', "The tag(s) you entered could not be found. Please enter a search string, or enter one or more valid tags.");
  define('M_ERR_MISSINGPARAMS_USER', "Some parameters were missing. Please complete all required fields and try again.");
  define('M_ERR_MISSINGPARAMS_SYSTEM', "Some parameters were missing.");
  define('M_ERR_INVALID_VIEW_PARAM', "Invalid parameter. Cannot view this item.");
  define('M_ERR_IO_FAILURE', "Cannot open file.");
  define('M_ERR_FILE_NOT_FOUND', "File not found.");
  
  // ** SEO ** //
  define('M_ERR_SYSTEM_SEO_ARTICLE_TITLE', "You cannot create an article with this title. Please choose another.");
  define('M_ERR_SYSTEM_SEO_AREA_NAME', "You cannot create an area with this name. Please choose another.");
  define('M_ERR_DUPLICATE_SEO_TITLE', "This title/name has already been used by another article or area. Please choose another.");
  
  // ** Site ** //
  define('M_ERR_SYSTEM_LOCKED', "The site has been locked by the site admin. If you are an admin, you can <a href=\"{FN_LOGIN}\">login here</a>.");
  define('M_ERR_VIEW_LINKED_AREA', "This area cannot be viewed directly.");
  define('M_ERR_CANNOT_VIEW_DIRECTLY', "This file cannot be viewed directly.");
  
  // ** Articles ** //
  define('M_ERR_UNPUBLISHED_CONTENT', "This article is currently unpublished.");
  define('M_ERR_ARTICLE_MARKED', "This article is already marked for deletion.");
  define('M_ERR_ARTICLE_UNMARKED', "This article is not marked for deletion.");

  // ** Uploads ** //
  define('M_ERR_UPLOAD_NOT_FOUND', "The file does not exist. Please choose another.");
  define('M_ERR_UPLOAD_LIMIT', "You cannot upload any more files in this area. Please delete some files if you wish to upload any more.");
  define('M_ERR_UPLOAD_MOVE_ERROR', "The file could not be moved. This may be resolved by changing the access permissions on the folder. Please report this to the site admin so the problem can be addressed.");
  define('M_ERR_UPLOAD_SECURITY', "The upload process could not complete due to a filename mismatch. This has been blocked for security reasons.");
  define('M_ERR_UPLOAD_NOT_IMAGE', "Only JPG and PNG files are allowed.");
  define('M_ERR_UPLOAD_NOT_DELETED_ACCESS', "Failed to delete file. Check your directory permissions.");
  define('M_ERR_UPLOAD_NOT_DELETED_MISSING', "Failed to delete file. The file does not exist.");
  define('M_ERR_UPLOAD_OR_URL', "Please select a file to upload or enter a direct URL.");
  define('M_ERR_UPLOAD_FILESIZE', "This file is too big. Please choose a smaller file and try again.");
  define('M_ERR_UPLOAD_PARTIAL', "The file was only partially uploaded, possibly due to a transfer error. If the problem persists, please report it to the site admin.");
  define('M_ERR_UPLOAD_NONE', "No file was uploaded. Please ensure that you selected a file, and try again.");
  define('M_ERR_UPLOAD_DUPLICATE', "A file with this name already exists on the server. Please rename your file or choose a different one and try again.");

  // ** Registration ** //
  define('M_ERR_EMAIL_IN_USE', "The email you have entered is already in use by an existing user. Please choose another.");
  define('M_ERR_USERNAME_IN_USE', "The username you have entered is already taken. Or, a different username has already been registered with this email address. Choose another name, or use the correct name for this email address.");
  define('M_ERR_USERNAME_TOO_SHORT', "Your username must be at least 3 characters long.");
  define('M_ERR_REGISTER_MULTIPLE', "Registering multiple usernames is not allowed.");
  define('M_ERR_REGISTRATION_DISABLED', "User registration has been disabled by the site admin.");
  define('M_ERR_REGISTER_WHILE_LOGGED_IN', "You cannot register a username while logged in.");
  
  // ** Login/Logout ** //
  define('M_ERR_ALREADY_LOGGED_IN', "You are already logged in.");
  define('M_ERR_ALREADY_LOGGED_OUT', "You are already logged out.");
  define('M_ERR_LOGIN_FAILED', "Invalid username or password. Login failed.");
  define('M_ERR_USER_SUSPENDED', "This account has been suspended by the site admin.");
  
  // ** Forgot Password ** //
  define('M_ERR_USERNAME_NOT_FOUND', "Username or email not found.");
  define('M_ERR_INVALID_ACTIVATION_KEY', "Invalid activation key.");

  // ** Users ** //
  define('M_ERR_ENTER_PW_TWICE', "You must enter your old password, and enter your new password twice.");
  define('M_ERR_DIFF_PASSWORDS', "The passwords do not match.");
  define('M_ERR_OLD_PW_WRONG', "The old password is incorrect.");
  define('M_ERR_AVATAR_LIMIT', "You cannot upload any more avatars. To upload a new avatar, first remove an existing one.");
  define('M_ERR_AVATAR_DIMENSIONS', "This image is either too wide or too tall to be an avatar. Please resize the file and try again.");
  define('M_ERR_AVATAR_NOT_YOURS', "This avatar belongs to another user. You are not allowed to use it.");
  define('M_ERR_NOT_AN_AVATAR', "This file is not an avatar!");
  define('M_ERR_NO_EMAIL_SET', "You haven't set an email address. Please go to the <a href=\"{FN_ADM_INDEX}\">Control Panel</a> and edit your profile.");

  // ** Admin ** //
  define('M_ERR_NO_FORM_RECIPIENTS', "There are no form recipients. A site admin must add at least one recipient for this form to appear.");
  define('M_ERR_AREA_PARENT_SELF', "Cannot parent a site area under itself!");
  define('M_ERR_NO_AREAS', "There are no areas on your site. You must create a top-level area.");
  define('M_ERR_SQL_SECURITY', "For security reasons, this query is not allowed.");
  define('M_ERR_GROUP_NOT_USED', "This template group is not currently used by any areas.");
  define('M_ERR_BREACHED_RSS_COUNT', "Please enter an RSS count that is between 5 and 30 inclusive.");
  define('M_ERR_NO_SMART_TAGS', "Please choose at least one smart tag.");
  define('M_ERR_BULK_NO_ITEMS', "No items selected. Please select at least one item.");
  define('M_ERR_SEARCH_USER_NOT_FOUND', "Username not found.");
  
  // ** Themes ** //
  define('M_ERR_THEME_FILE_MISSING', "A theme file could not be found. Please refer to Supporting Information (below) for details of which file is missing.");
  
  // ** Access log ** //
  define('M_AL_AREA_CREATE', "Created area");
  define('M_AL_AREA_DELETE', "Deleted area");
  define('M_AL_AREA_EDIT', "Edited area");
  define('M_AL_ARTICLE_CREATE', "Created article");
  define('M_AL_ARTICLE_EDIT', "Edited article");
  define('M_AL_ARTICLE_DELETE', "Deleted article");
  define('M_AL_ARTICLE_MARK', "Marked article for deletion");
  define('M_AL_ARTICLE_RESTORE', "Restored article");
  define('M_AL_ARTICLE_SAVEDRAFT', "Saved article draft");
  define('M_AL_ARTICLE_REVIEW', "Submitted article for review");
  define('M_AL_ARTICLE_PUBLISH', "Published article");
  define('M_AL_ARTICLE_SCHEDULE', "Scheduled article");
  
