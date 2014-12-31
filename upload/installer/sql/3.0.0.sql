DROP TABLE IF EXISTS maj_connections;
DROP TABLE IF EXISTS maj_form_recipients;
DROP TABLE IF EXISTS maj_ratings;
DROP TABLE IF EXISTS maj_user_variables;
DROP TABLE IF EXISTS maj_widgets;

ALTER TABLE maj_areas DROP COLUMN nav_type, DROP INDEX nav_type;

DELETE FROM maj_sys_preferences WHERE preference = 'prefUserChangePass';

UPDATE maj_sys_preferences SET content = 'injader' WHERE preference = 'prefDefaultTheme';
UPDATE maj_sys_preferences SET content = '3.0.0' WHERE preference = 'prefCMSVersion';

