DROP TABLE IF EXISTS {IFW_TBL_WIDGETS};
CREATE TABLE IF NOT EXISTS {IFW_TBL_WIDGETS} (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL DEFAULT '',
  version VARCHAR(20) NOT NULL DEFAULT '',
  conn_id INTEGER UNSIGNED NOT NULL DEFAULT 0,
  ucp_link CHAR(1) NOT NULL DEFAULT 'N',
  acp_link CHAR(1) NOT NULL DEFAULT 'N',
  query_string TEXT NOT NULL,
  item_limit INTEGER UNSIGNED NOT NULL DEFAULT 0,
  widget_variable VARCHAR(100) NOT NULL,
  widget_template TEXT NOT NULL,
  widget_type VARCHAR(30) NOT NULL DEFAULT '',
  PRIMARY KEY(id),
  INDEX conn_id(conn_id),
  INDEX widget_variable(widget_variable),
  INDEX widget_type(widget_type)
);
