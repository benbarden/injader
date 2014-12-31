DROP TABLE IF EXISTS maj_connections;
DROP TABLE IF EXISTS maj_form_recipients;
DROP TABLE IF EXISTS maj_ratings;
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

UPDATE maj_sys_preferences SET content = 'injader' WHERE preference = 'prefDefaultTheme';
UPDATE maj_sys_preferences SET content = '3.0.0' WHERE preference = 'prefCMSVersion';

ALTER TABLE maj_access_log RENAME TO Cms_AccessLog;
ALTER TABLE maj_areas RENAME TO Cms_Areas;
ALTER TABLE maj_comments RENAME TO Cms_Comments;
ALTER TABLE maj_content RENAME TO Cms_Content;
ALTER TABLE maj_permission_profiles RENAME TO Cms_PermissionProfile;
ALTER TABLE maj_spam_rules RENAME TO Cms_SpamRules;
ALTER TABLE maj_sys_preferences RENAME TO Cms_Settings;
ALTER TABLE maj_tags RENAME TO Cms_Tags;
ALTER TABLE maj_uploads RENAME TO Cms_Uploads;
ALTER TABLE maj_url_mapping RENAME TO Cms_UrlMapping;
ALTER TABLE maj_users RENAME TO Cms_Users;
ALTER TABLE maj_user_groups RENAME TO Cms_UserRoles;
ALTER TABLE maj_user_sessions RENAME TO Cms_UserSessions;
ALTER TABLE maj_user_stats RENAME TO Cms_UserStats;
