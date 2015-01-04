DROP TABLE IF EXISTS maj_areas;
DROP TABLE IF EXISTS maj_comments;
DROP TABLE IF EXISTS maj_connections;
DROP TABLE IF EXISTS maj_form_recipients;
DROP TABLE IF EXISTS maj_ratings;
DROP TABLE IF EXISTS maj_spam_rules;
DROP TABLE IF EXISTS maj_user_stats;
DROP TABLE IF EXISTS maj_user_variables;
DROP TABLE IF EXISTS maj_widgets;

ALTER TABLE maj_areas DROP COLUMN nav_type, DROP INDEX nav_type;

DELETE FROM maj_sys_preferences WHERE preference = 'prefUserChangePass';
DELETE FROM maj_sys_preferences WHERE preference = 'prefAllowPasswordResets';
DELETE FROM maj_sys_preferences WHERE preference = 'prefSystemLock';
DELETE FROM maj_sys_preferences WHERE preference = 'prefRSSArticlesURL';
DELETE FROM maj_sys_preferences WHERE preference = 'prefSiteFavicon';
DELETE FROM maj_sys_preferences WHERE preference = 'prefMaxLogEntries';
DELETE FROM maj_sys_preferences WHERE preference = 'prefSystemPageCount';

DELETE FROM maj_sys_preferences WHERE preference = 'prefCommentCAPTCHA';
DELETE FROM maj_sys_preferences WHERE preference = 'prefCommentUseNoFollow';
DELETE FROM maj_sys_preferences WHERE preference = 'prefCommentNoFollowLimit';
DELETE FROM maj_sys_preferences WHERE preference = 'prefCommentReviewEmail';
DELETE FROM maj_sys_preferences WHERE preference = 'prefCommentNotification';
DELETE FROM maj_sys_preferences WHERE preference = 'prefCommentNotifyAuthor';

ALTER TABLE maj_access_log RENAME TO Cms_AccessLog;
ALTER TABLE maj_content RENAME TO Cms_Content;
ALTER TABLE maj_permission_profiles RENAME TO Cms_PermissionProfile;
ALTER TABLE maj_sys_preferences RENAME TO Cms_Settings;
ALTER TABLE maj_tags RENAME TO Cms_Tags;
ALTER TABLE maj_uploads RENAME TO Cms_Uploads;
ALTER TABLE maj_url_mapping RENAME TO Cms_UrlMapping;
ALTER TABLE maj_users RENAME TO Cms_Users;
ALTER TABLE maj_user_groups RENAME TO Cms_UserRoles;
ALTER TABLE maj_user_sessions RENAME TO Cms_UserSessions;

ALTER TABLE Cms_Content ADD COLUMN permalink VARCHAR(255) NOT NULL AFTER title;
ALTER TABLE Cms_Content ADD INDEX permalink (permalink ASC);
ALTER TABLE Cms_Content DROP COLUMN comment_count;
ALTER TABLE Cms_Content DROP COLUMN locked;

ALTER TABLE Cms_Permissions DROP COLUMN view_area;
ALTER TABLE Cms_Permissions DROP COLUMN add_comment;
ALTER TABLE Cms_Permissions DROP COLUMN edit_comment;
ALTER TABLE Cms_Permissions DROP COLUMN delete_comment;
ALTER TABLE Cms_Permissions DROP COLUMN lock_article;

UPDATE Cms_Settings SET content = 'injader' WHERE preference = 'prefDefaultTheme';
UPDATE Cms_Settings SET content = '3.0.0' WHERE preference = 'prefCMSVersion';
INSERT INTO Cms_Settings(preference, content) VALUES('prefDisqusId', '');

CREATE TABLE Cms_Categories (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  permalink VARCHAR(255) NOT NULL,
  description TEXT NULL,
  parent_id INT(10) UNSIGNED NULL,
  items_per_page INT(10) UNSIGNED NOT NULL,
  sort_rule VARCHAR(30) NOT NULL,
  PRIMARY KEY (id));
INSERT INTO Cms_Categories (id, name, permalink, items_per_page, sort_rule) VALUES(1, 'General', '/general/', 5, 'create_date|desc');

ALTER TABLE Cms_Content CHANGE COLUMN content_area_id category_id INT(10) UNSIGNED NULL DEFAULT '0';
ALTER TABLE Cms_Content DROP INDEX content_area_id , ADD INDEX category_id (category_id ASC);
UPDATE Cms_Content SET category_id = 1;

ALTER TABLE Cms_UrlMapping CHANGE COLUMN area_id category_id INT(10) NOT NULL DEFAULT '0' ;
