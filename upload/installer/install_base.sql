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

DROP TABLE IF EXISTS {IFW_TBL_CATEGORIES};
CREATE TABLE IF NOT EXISTS {IFW_TBL_CATEGORIES} (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  permalink VARCHAR(255) NOT NULL,
  description TEXT NULL,
  parent_id INT(10) UNSIGNED NULL,
  items_per_page INT(10) UNSIGNED NOT NULL,
  sort_rule VARCHAR(30) NOT NULL,
  PRIMARY KEY (id));
INSERT INTO {IFW_TBL_CATEGORIES} (id, name, permalink, items_per_page, sort_rule) VALUES(1, 'General', '/general/', 5, 'create_date|desc');

DROP TABLE IF EXISTS {IFW_TBL_CONTENT};
CREATE TABLE IF NOT EXISTS {IFW_TBL_CONTENT} (
  id int(10) unsigned NOT NULL auto_increment,
  title varchar(125) NOT NULL default '',
  permalink VARCHAR(255) NOT NULL,
  content mediumtext NOT NULL,
  author_id int(10) unsigned NOT NULL default 0,
  category_id int(10) unsigned NULL default 0,
  create_date datetime NOT NULL default '0000-00-00 00:00:00',
  edit_date datetime NOT NULL default '0000-00-00 00:00:00',
  last_updated datetime NOT NULL default '0000-00-00 00:00:00',
  read_userlist text NOT NULL,
  hits int(10) unsigned NOT NULL default '0',
  tags TEXT NOT NULL,
  seo_title VARCHAR(100) NOT NULL default '',
  link_url VARCHAR(150) NOT NULL default '',
  content_status VARCHAR(20) NOT NULL,
  user_groups TEXT NOT NULL,
  tags_deleted TEXT NOT NULL,
  article_order INT(10) UNSIGNED NOT NULL DEFAULT '0',
  article_excerpt TEXT NOT NULL,
  PRIMARY KEY(id),
  INDEX author_id(author_id), 
  INDEX category_id(category_id),
  INDEX content_status(content_status)
) ENGINE = MyISAM;
ALTER TABLE {IFW_TBL_CONTENT} ADD FULLTEXT title(title);
ALTER TABLE {IFW_TBL_CONTENT} ADD FULLTEXT content(content);
ALTER TABLE {IFW_TBL_CONTENT} ADD FULLTEXT title_content(title, content);
ALTER TABLE {IFW_TBL_CONTENT} ADD INDEX permalink (permalink ASC);

DROP TABLE IF EXISTS {IFW_TBL_PERMISSION_PROFILES};
CREATE TABLE {IFW_TBL_PERMISSION_PROFILES} (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  is_system CHAR(1) NOT NULL,
  create_article TEXT NOT NULL,
  publish_article TEXT NOT NULL,
  edit_article TEXT NOT NULL,
  delete_article TEXT NOT NULL,
  attach_file TEXT NOT NULL,
  PRIMARY KEY (id),
  INDEX is_system(is_system)
);
INSERT INTO {IFW_TBL_PERMISSION_PROFILES}(name, is_system, create_article, publish_article, edit_article, delete_article, attach_file) VALUES('System', 'Y', '2', '2', '2', '2', '2');

DROP TABLE IF EXISTS {IFW_TBL_SETTINGS};
CREATE TABLE IF NOT EXISTS {IFW_TBL_SETTINGS} (
  id int(10) unsigned NOT NULL auto_increment,
  preference varchar(45) NOT NULL default '',
  content TEXT NOT NULL,
  PRIMARY KEY(id),
  INDEX preference(preference)
);

INSERT INTO {IFW_TBL_SETTINGS}(preference, content) VALUES('{C_PREF_CMS_VERSION}', '{C_SYS_LATEST_VERSION}');
INSERT INTO {IFW_TBL_SETTINGS}(preference, content) VALUES('{C_PREF_SITE_TITLE}', 'Injader test site');
INSERT INTO {IFW_TBL_SETTINGS}(preference, content) VALUES('{C_PREF_SITE_DESCRIPTION}', '');
INSERT INTO {IFW_TBL_SETTINGS}(preference, content) VALUES('{C_PREF_SITE_KEYWORDS}', '');
INSERT INTO {IFW_TBL_SETTINGS}(preference, content) VALUES('{C_PREF_SITE_HEADER}', '');
INSERT INTO {IFW_TBL_SETTINGS}(preference, content) VALUES('{C_PREF_SITE_EMAIL}', 'you@yoursite.com');

INSERT INTO {IFW_TBL_SETTINGS}(preference, content) VALUES('{C_PREF_DEFAULT_THEME}', 'injader');

INSERT INTO {IFW_TBL_SETTINGS}(preference, content) VALUES('{C_PREF_DATE_FORMAT}', '1');
INSERT INTO {IFW_TBL_SETTINGS}(preference, content) VALUES('{C_PREF_TIME_FORMAT}', '0');
INSERT INTO {IFW_TBL_SETTINGS}(preference, content) VALUES('{C_PREF_SERVER_TIME_OFFSET}', '0');

INSERT INTO {IFW_TBL_SETTINGS}(preference, content) VALUES('{C_PREF_USER_REGISTRATION}', '0');
INSERT INTO {IFW_TBL_SETTINGS}(preference, content) VALUES('{C_PREF_COOKIE_DAYS}', '14');

INSERT INTO {IFW_TBL_SETTINGS}(preference, content) VALUES('{C_PREF_TAG_THRESHOLD}', '1');
INSERT INTO {IFW_TBL_SETTINGS}(preference, content) VALUES('{C_PREF_RSS_COUNT}', '10');
INSERT INTO {IFW_TBL_SETTINGS}(preference, content) VALUES('{C_PREF_DISQUS_ID}', '');

INSERT INTO {IFW_TBL_SETTINGS}(preference, content) VALUES('{C_PREF_ARTICLE_NOTIFY_ADMIN}', '1');
INSERT INTO {IFW_TBL_SETTINGS}(preference, content) VALUES('{C_PREF_ARTICLE_REVIEW_EMAIL}', '1');

INSERT INTO {IFW_TBL_SETTINGS}(preference, content) VALUES('{C_PREF_THUMB_SMALL}', '100');
INSERT INTO {IFW_TBL_SETTINGS}(preference, content) VALUES('{C_PREF_THUMB_MEDIUM}', '300');
INSERT INTO {IFW_TBL_SETTINGS}(preference, content) VALUES('{C_PREF_THUMB_LARGE}', '600');
INSERT INTO {IFW_TBL_SETTINGS}(preference, content) VALUES('{C_PREF_THUMB_KEEPASPECT}', 'Y');
INSERT INTO {IFW_TBL_SETTINGS}(preference, content) VALUES('{C_PREF_ATTACH_MAX_SIZE}', '1000000');
INSERT INTO {IFW_TBL_SETTINGS}(preference, content) VALUES('{C_PREF_AVATARS_PER_USER}', '1');
INSERT INTO {IFW_TBL_SETTINGS}(preference, content) VALUES('{C_PREF_AVATAR_SIZE}', '100');
INSERT INTO {IFW_TBL_SETTINGS}(preference, content) VALUES('{C_PREF_AVATAR_MAX_SIZE}', '100000');
INSERT INTO {IFW_TBL_SETTINGS}(preference, content) VALUES('{C_PREF_DIR_AVATARS}', 'data/avatars/');
INSERT INTO {IFW_TBL_SETTINGS}(preference, content) VALUES('{C_PREF_DIR_SITE_IMAGES}', 'data/site/');
INSERT INTO {IFW_TBL_SETTINGS}(preference, content) VALUES('{C_PREF_DIR_MISC}', 'data/attach/');

INSERT INTO {IFW_TBL_SETTINGS}(preference, content) VALUES('{C_PREF_LINK_STYLE}', '1');

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
  category_id INT( 10 ) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (relative_url)
);
ALTER TABLE {IFW_TBL_URL_MAPPING} ADD INDEX is_active(is_active);
ALTER TABLE {IFW_TBL_URL_MAPPING} ADD INDEX article_id(article_id);
ALTER TABLE {IFW_TBL_URL_MAPPING} ADD INDEX category_id(category_id);

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
