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

DROP TABLE IF EXISTS {IFW_TBL_SPAM_RULES};
CREATE TABLE IF NOT EXISTS {IFW_TBL_SPAM_RULES} (
  id int(10) unsigned NOT NULL auto_increment, 
  block_rule VARCHAR( 255 ) NOT NULL ,
  block_type ENUM( 'Email', 'URL', 'Name', 'Comment', 'IP', 'Any' ) NOT NULL,
  PRIMARY KEY(id),
  INDEX (block_rule)
);