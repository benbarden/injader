DROP TABLE IF EXISTS {IFW_TBL_ACCESS_LOG};
CREATE TABLE IF NOT EXISTS {IFW_TBL_ACCESS_LOG} (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INTEGER UNSIGNED NOT NULL,
  detail TEXT NOT NULL,
  tag VARCHAR(100) NOT NULL,
  log_date DATETIME NOT NULL,
  ip_address VARCHAR(20) NOT NULL,
  PRIMARY KEY(id),
  INDEX tag(tag),
  INDEX ip_address(ip_address)
);

DROP TABLE IF EXISTS {IFW_TBL_AREAS};
CREATE TABLE IF NOT EXISTS {IFW_TBL_AREAS} (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(125) NOT NULL DEFAULT '',
  area_level int(10) UNSIGNED NOT NULL DEFAULT 0,
  area_order int(10) UNSIGNED NOT NULL DEFAULT 0,
  hier_left int(10) UNSIGNED NOT NULL DEFAULT 0,
  hier_right int(10) UNSIGNED NOT NULL DEFAULT 0,
  parent_id int(10) UNSIGNED NOT NULL DEFAULT 0,
  permission_profile_id int(10) UNSIGNED NOT NULL DEFAULT 0,
  area_graphic_id int(10) UNSIGNED NOT NULL DEFAULT 0,
  content_per_page int(10) UNSIGNED NOT NULL DEFAULT 0,
  sort_rule VARCHAR(100) NOT NULL DEFAULT '',
  include_in_rss_feed CHAR(1) NOT NULL DEFAULT 'Y',
  max_file_size VARCHAR(20) NOT NULL DEFAULT '0',
  max_files_per_user int(10) UNSIGNED NOT NULL DEFAULT 0,
  area_url VARCHAR(200) NOT NULL DEFAULT '',
  smart_tags TEXT NOT NULL,
  seo_name VARCHAR(100) NOT NULL DEFAULT '',
  area_description TEXT NOT NULL,
  area_type VARCHAR(45) NOT NULL,
  theme_path TEXT NOT NULL,
  layout_style VARCHAR(50) NOT NULL DEFAULT '',
  subarea_content_on_index CHAR( 1 ) NOT NULL DEFAULT 'N',
  PRIMARY KEY(id),
  INDEX parent_id(parent_id),
  INDEX permission_profile_id(permission_profile_id),
  INDEX area_graphic_id(area_graphic_id),
  INDEX area_type(area_type)
);

INSERT INTO {IFW_TBL_AREAS} (id, name, area_level, area_order, hier_left, hier_right, parent_id, permission_profile_id, area_graphic_id, content_per_page, sort_rule, include_in_rss_feed, max_file_size, max_files_per_user, seo_name, smart_tags, area_description, area_type, theme_path, layout_style) VALUES (1, 'Home', 1, 1, 1, 2, 0, 0, 0, 10, 'create_date|desc', 'Y', '0', 0, 'home', '', '', '{C_AREA_CONTENT}', '', '');
INSERT INTO {IFW_TBL_AREAS} (id, name, area_level, area_order, hier_left, hier_right, parent_id, permission_profile_id, area_graphic_id, content_per_page, sort_rule, include_in_rss_feed, max_file_size, max_files_per_user, seo_name, smart_tags, area_description, area_type, theme_path, layout_style) VALUES (2, 'Test', 1, 1, 3, 4, 0, 0, 0, 25, 'last_updated|desc', 'N', '0', 0, 'test', '', '', '{C_AREA_CONTENT}', '', '');

DROP TABLE IF EXISTS {IFW_TBL_COMMENTS};
CREATE TABLE IF NOT EXISTS {IFW_TBL_COMMENTS} (
  id int(10) unsigned NOT NULL auto_increment,
  content text NOT NULL,
  create_date datetime NOT NULL default '0000-00-00 00:00:00',
  edit_date datetime NOT NULL default '0000-00-00 00:00:00',
  author_id int(10) unsigned NOT NULL default 0,
  story_id int(10) unsigned NOT NULL default 0,
  upload_id int(10) unsigned NOT NULL default 0,
  comment_count int(10) unsigned NOT NULL default 0,
  ip_address VARCHAR(20) NOT NULL default '',
  comment_status VARCHAR(20) NOT NULL,
  guest_name VARCHAR( 100 ) NOT NULL default '',
  guest_email VARCHAR( 100 ) NOT NULL default '',
  guest_url VARCHAR( 150 ) NOT NULL default '',
  PRIMARY KEY(id),
  INDEX story_id(story_id),
  INDEX author_id(author_id),
  INDEX upload_id(upload_id),
  INDEX comment_status(comment_status)
);

DROP TABLE IF EXISTS {IFW_TBL_CONTENT};
CREATE TABLE IF NOT EXISTS {IFW_TBL_CONTENT} (
  id int(10) unsigned NOT NULL auto_increment,
  title varchar(125) NOT NULL default '',
  content mediumtext NOT NULL,
  author_id int(10) unsigned NOT NULL default 0,
  content_area_id int(10) unsigned NOT NULL default 0,
  create_date datetime NOT NULL default '0000-00-00 00:00:00',
  edit_date datetime NOT NULL default '0000-00-00 00:00:00',
  last_updated datetime NOT NULL default '0000-00-00 00:00:00',
  locked char(1) NOT NULL default '',
  read_userlist text NOT NULL,
  hits int(10) unsigned NOT NULL default '0',
  tags TEXT NOT NULL,
  seo_title VARCHAR(100) NOT NULL default '',
  link_url VARCHAR(150) NOT NULL default '',
  content_status VARCHAR(20) NOT NULL,
  comment_count INTEGER UNSIGNED NOT NULL DEFAULT 0,
  user_groups TEXT NOT NULL,
  tags_deleted TEXT NOT NULL,
  article_order INT(10) UNSIGNED NOT NULL DEFAULT '0',
  article_excerpt TEXT NOT NULL,
  PRIMARY KEY(id),
  INDEX author_id(author_id), 
  INDEX content_area_id(content_area_id),
  INDEX content_status(content_status)
) ENGINE = MyISAM;
ALTER TABLE {IFW_TBL_CONTENT} ADD FULLTEXT title(title);
ALTER TABLE {IFW_TBL_CONTENT} ADD FULLTEXT content(content);
ALTER TABLE {IFW_TBL_CONTENT} ADD FULLTEXT title_content(title, content);

DROP TABLE IF EXISTS {IFW_TBL_PERMISSION_PROFILES};
CREATE TABLE {IFW_TBL_PERMISSION_PROFILES} (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  is_system CHAR(1) NOT NULL,
  view_area TEXT NOT NULL,
  create_article TEXT NOT NULL,
  publish_article TEXT NOT NULL,
  edit_article TEXT NOT NULL,
  delete_article TEXT NOT NULL,
  add_comment TEXT NOT NULL,
  edit_comment TEXT NOT NULL,
  delete_comment TEXT NOT NULL,
  lock_article TEXT NOT NULL,
  attach_file TEXT NOT NULL,
  PRIMARY KEY (id),
  INDEX is_system(is_system)
);
INSERT INTO {IFW_TBL_PERMISSION_PROFILES}(name, is_system, view_area, create_article, publish_article, edit_article, delete_article, add_comment, edit_comment, delete_comment, lock_article, attach_file) VALUES('System', 'Y', '0|1|2', '2', '2', '2', '2', '0|1|2', '2', '2', '2', '2');

DROP TABLE IF EXISTS {IFW_TBL_SPAM_RULES};
CREATE TABLE IF NOT EXISTS {IFW_TBL_SPAM_RULES} (
  id int(10) unsigned NOT NULL auto_increment, 
  block_rule VARCHAR( 255 ) NOT NULL ,
  block_type ENUM( 'Email', 'URL', 'Name', 'Comment', 'IP', 'Any' ) NOT NULL,
  PRIMARY KEY(id),
  INDEX (block_rule)
);

DROP TABLE IF EXISTS {IFW_TBL_SYS_PREFERENCES};
CREATE TABLE IF NOT EXISTS {IFW_TBL_SYS_PREFERENCES} (
  id int(10) unsigned NOT NULL auto_increment,
  preference varchar(45) NOT NULL default '',
  content TEXT NOT NULL,
  PRIMARY KEY(id),
  INDEX preference(preference)
);

INSERT INTO {IFW_TBL_SYS_PREFERENCES}(preference, content) VALUES('{C_PREF_CMS_VERSION}', '{C_SYS_LATEST_VERSION}');
INSERT INTO {IFW_TBL_SYS_PREFERENCES}(preference, content) VALUES('{C_PREF_SITE_TITLE}', 'Injader test site');
INSERT INTO {IFW_TBL_SYS_PREFERENCES}(preference, content) VALUES('{C_PREF_SITE_DESCRIPTION}', '');
INSERT INTO {IFW_TBL_SYS_PREFERENCES}(preference, content) VALUES('{C_PREF_SITE_KEYWORDS}', '');
INSERT INTO {IFW_TBL_SYS_PREFERENCES}(preference, content) VALUES('{C_PREF_SITE_HEADER}', '');
INSERT INTO {IFW_TBL_SYS_PREFERENCES}(preference, content) VALUES('{C_PREF_SITE_EMAIL}', 'you@yoursite.com');
INSERT INTO {IFW_TBL_SYS_PREFERENCES}(preference, content) VALUES('{C_PREF_SITE_FAVICON}', '');

INSERT INTO {IFW_TBL_SYS_PREFERENCES}(preference, content) VALUES('{C_PREF_RSS_ARTICLES_URL}', '');
INSERT INTO {IFW_TBL_SYS_PREFERENCES}(preference, content) VALUES('{C_PREF_DEFAULT_THEME}', 'injader');

INSERT INTO {IFW_TBL_SYS_PREFERENCES}(preference, content) VALUES('{C_PREF_SYSTEM_PAGE_COUNT}', '25');
INSERT INTO {IFW_TBL_SYS_PREFERENCES}(preference, content) VALUES('{C_PREF_MAX_LOG_ENTRIES}', '3000');
INSERT INTO {IFW_TBL_SYS_PREFERENCES}(preference, content) VALUES('{C_PREF_SYSTEM_LOCK}', 'N');

INSERT INTO {IFW_TBL_SYS_PREFERENCES}(preference, content) VALUES('{C_PREF_DATE_FORMAT}', '1');
INSERT INTO {IFW_TBL_SYS_PREFERENCES}(preference, content) VALUES('{C_PREF_TIME_FORMAT}', '0');
INSERT INTO {IFW_TBL_SYS_PREFERENCES}(preference, content) VALUES('{C_PREF_SERVER_TIME_OFFSET}', '0');

INSERT INTO {IFW_TBL_SYS_PREFERENCES}(preference, content) VALUES('{C_PREF_USER_REGISTRATION}', '0');
INSERT INTO {IFW_TBL_SYS_PREFERENCES}(preference, content) VALUES('{C_PREF_USER_CHANGE_PASS}', 'Y');
INSERT INTO {IFW_TBL_SYS_PREFERENCES}(preference, content) VALUES('{C_PREF_ALLOW_PASSWORD_RESETS}', 'Y');
INSERT INTO {IFW_TBL_SYS_PREFERENCES}(preference, content) VALUES('{C_PREF_COOKIE_DAYS}', '14');

INSERT INTO {IFW_TBL_SYS_PREFERENCES}(preference, content) VALUES('{C_PREF_TAG_THRESHOLD}', '1');
INSERT INTO {IFW_TBL_SYS_PREFERENCES}(preference, content) VALUES('{C_PREF_RSS_COUNT}', '10');

INSERT INTO {IFW_TBL_SYS_PREFERENCES}(preference, content) VALUES('{C_PREF_COMMENT_CAPTCHA}', '0');
INSERT INTO {IFW_TBL_SYS_PREFERENCES}(preference, content) VALUES('{C_PREF_COMMENT_USE_NOFOLLOW}', '1');
INSERT INTO {IFW_TBL_SYS_PREFERENCES}(preference, content) VALUES('{C_PREF_COMMENT_NOFOLLOW_LIMIT}', '3');

INSERT INTO {IFW_TBL_SYS_PREFERENCES}(preference, content) VALUES('{C_PREF_ARTICLE_NOTIFY_ADMIN}', '1');
INSERT INTO {IFW_TBL_SYS_PREFERENCES}(preference, content) VALUES('{C_PREF_ARTICLE_REVIEW_EMAIL}', '1');
INSERT INTO {IFW_TBL_SYS_PREFERENCES}(preference, content) VALUES('{C_PREF_COMMENT_REVIEW_EMAIL}', '1');
INSERT INTO {IFW_TBL_SYS_PREFERENCES}(preference, content) VALUES('{C_PREF_COMMENT_NOTIFICATION}', '1');
INSERT INTO {IFW_TBL_SYS_PREFERENCES}(preference, content) VALUES('{C_PREF_COMMENT_NOTIFY_AUTHOR}', '1');

INSERT INTO {IFW_TBL_SYS_PREFERENCES}(preference, content) VALUES('{C_PREF_THUMB_SMALL}', '100');
INSERT INTO {IFW_TBL_SYS_PREFERENCES}(preference, content) VALUES('{C_PREF_THUMB_MEDIUM}', '300');
INSERT INTO {IFW_TBL_SYS_PREFERENCES}(preference, content) VALUES('{C_PREF_THUMB_LARGE}', '600');
INSERT INTO {IFW_TBL_SYS_PREFERENCES}(preference, content) VALUES('{C_PREF_THUMB_KEEPASPECT}', 'Y');
INSERT INTO {IFW_TBL_SYS_PREFERENCES}(preference, content) VALUES('{C_PREF_ATTACH_MAX_SIZE}', '1000000');
INSERT INTO {IFW_TBL_SYS_PREFERENCES}(preference, content) VALUES('{C_PREF_AVATARS_PER_USER}', '1');
INSERT INTO {IFW_TBL_SYS_PREFERENCES}(preference, content) VALUES('{C_PREF_AVATAR_SIZE}', '100');
INSERT INTO {IFW_TBL_SYS_PREFERENCES}(preference, content) VALUES('{C_PREF_AVATAR_MAX_SIZE}', '100000');
INSERT INTO {IFW_TBL_SYS_PREFERENCES}(preference, content) VALUES('{C_PREF_DIR_AVATARS}', 'data/avatars/');
INSERT INTO {IFW_TBL_SYS_PREFERENCES}(preference, content) VALUES('{C_PREF_DIR_SITE_IMAGES}', 'data/site/');
INSERT INTO {IFW_TBL_SYS_PREFERENCES}(preference, content) VALUES('{C_PREF_DIR_MISC}', 'data/attach/');

INSERT INTO {IFW_TBL_SYS_PREFERENCES}(preference, content) VALUES('{C_PREF_LINK_STYLE}', '1');

DROP TABLE IF EXISTS {IFW_TBL_TAGS};
CREATE TABLE IF NOT EXISTS {IFW_TBL_TAGS} (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  tag VARCHAR(100) NOT NULL default '',
  tag_count INTEGER UNSIGNED NOT NULL default 0,
  article_list TEXT NOT NULL,
  PRIMARY KEY(id),
  INDEX tag(tag),
  INDEX tag_count(tag_count)
);

DROP TABLE IF EXISTS {IFW_TBL_UPLOADS};
CREATE TABLE IF NOT EXISTS {IFW_TBL_UPLOADS} (
  id int(10) unsigned NOT NULL auto_increment,
  title varchar(100) NOT NULL default '',
  location text NOT NULL,
  file_area_id int(10) unsigned NOT NULL default '0',
  author_id int(10) unsigned NOT NULL default '0',
  create_date datetime NOT NULL default '0000-00-00 00:00:00',
  edit_date datetime NOT NULL default '0000-00-00 00:00:00',
  hits int(10) unsigned NOT NULL default '0',
  is_avatar CHAR(1) NOT NULL default 'N',
  is_siteimage CHAR(1) NOT NULL DEFAULT 'N',
  delete_flag char(1) NOT NULL default '',
  thumb_small TEXT NOT NULL,
  thumb_medium TEXT NOT NULL,
  thumb_large TEXT NOT NULL,
  upload_size VARCHAR(50) NOT NULL DEFAULT '',
  seo_title VARCHAR(100) NOT NULL DEFAULT '',
  article_id INTEGER UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY(id),
  INDEX file_area_id(file_area_id),
  INDEX author_id(author_id),
  INDEX hits(hits),
  INDEX is_avatar(is_avatar),
  INDEX is_siteimage(is_siteimage),
  INDEX article_id(article_id)
);

DROP TABLE IF EXISTS {IFW_TBL_URL_MAPPING};
CREATE TABLE IF NOT EXISTS {IFW_TBL_URL_MAPPING} (
  relative_url VARCHAR( 255 ) NOT NULL ,
  is_active CHAR( 1 ) NOT NULL DEFAULT 'Y' ,
  article_id INT( 10 ) NOT NULL DEFAULT '0' ,
  area_id INT( 10 ) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (relative_url)
);
ALTER TABLE {IFW_TBL_URL_MAPPING} ADD INDEX is_active(is_active);
ALTER TABLE {IFW_TBL_URL_MAPPING} ADD INDEX article_id(article_id);
ALTER TABLE {IFW_TBL_URL_MAPPING} ADD INDEX area_id(area_id);

DROP TABLE IF EXISTS {IFW_TBL_USERS};
CREATE TABLE IF NOT EXISTS {IFW_TBL_USERS} (
  id int(10) unsigned NOT NULL auto_increment,
  username varchar(100) NOT NULL default '',
  userpass varchar(100) NOT NULL default '',
  forename varchar(45) NOT NULL default '',
  surname varchar(45) NOT NULL default '',
  email varchar(100) NOT NULL default '',
  location varchar(100) NOT NULL default '',
  occupation varchar(100) NOT NULL default '',
  interests text NOT NULL,
  homepage_link varchar(150) NOT NULL default '',
  homepage_text varchar(100) NOT NULL default '',
  avatar_id int(10) unsigned NOT NULL default '0',
  join_date datetime NOT NULL default '0000-00-00 00:00:00',
  ip_address varchar(20) NOT NULL default '',
  user_groups text NOT NULL,
  activation_key VARCHAR(64) NOT NULL DEFAULT '',
  seo_username VARCHAR(100) NOT NULL DEFAULT '',
  user_deleted CHAR(1) NOT NULL DEFAULT 'N',
  user_moderate CHAR(1) NOT NULL DEFAULT 'Y',
  PRIMARY KEY(id),
  INDEX username(username),
  INDEX email(email),
  INDEX avatar_id(avatar_id),
  INDEX user_deleted(user_deleted)
);

DROP TABLE IF EXISTS {IFW_TBL_USER_GROUPS};
CREATE TABLE IF NOT EXISTS {IFW_TBL_USER_GROUPS} (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL default '',
  is_admin CHAR(1) NOT NULL default 'N',
  is_default CHAR(1) NOT NULL default 'N',
  PRIMARY KEY(id),
  INDEX is_admin(is_admin),
  INDEX is_default(is_default)
);

INSERT INTO {IFW_TBL_USER_GROUPS}(id, name, is_admin, is_default) VALUES(1, 'New User', 'N', 'Y');
INSERT INTO {IFW_TBL_USER_GROUPS}(id, name, is_admin, is_default) VALUES(2, 'Administrator', 'Y', 'N');

DROP TABLE IF EXISTS {IFW_TBL_USER_SESSIONS};
CREATE TABLE IF NOT EXISTS {IFW_TBL_USER_SESSIONS} (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  session_id VARCHAR(64) NOT NULL default '',
  user_id INTEGER UNSIGNED NOT NULL default 0,
  ip_address VARCHAR(20) NOT NULL default '',
  user_agent TEXT NOT NULL,
  login_date DATETIME NOT NULL default '0000-00-00 00:00:00',
  expiry_date DATETIME NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY(id),
  INDEX user_id(user_id),
  INDEX session_id(session_id)
);

DROP TABLE IF EXISTS {IFW_TBL_USER_STATS};
CREATE TABLE IF NOT EXISTS {IFW_TBL_USER_STATS} (
  user_email VARCHAR( 100 ) NOT NULL ,
  comment_count INT NOT NULL DEFAULT '0',
  article_subscriptions TEXT NOT NULL,
  PRIMARY KEY (user_email)
);
