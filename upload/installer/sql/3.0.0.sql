DROP TABLE IF EXISTS maj_form_recipients;
DROP TABLE IF EXISTS maj_user_variables;
DROP TABLE IF EXISTS maj_widgets;
DROP TABLE IF EXISTS maj_ratings;

UPDATE maj_sys_preferences SET content = 'injader' WHERE preference = 'prefDefaultTheme';
