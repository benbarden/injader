ALTER TABLE {IFW_TBL_COMMENTS} ADD comment_status VARCHAR(20) NOT NULL;
ALTER TABLE {IFW_TBL_COMMENTS} ADD INDEX comment_status(comment_status);
UPDATE {IFW_TBL_COMMENTS} SET comment_status = 'Pending' WHERE approved = '';
UPDATE {IFW_TBL_COMMENTS} SET comment_status = 'Approved' WHERE approved = 'Y';
DELETE FROM {IFW_TBL_COMMENTS} WHERE approved = 'N';
ALTER TABLE {IFW_TBL_COMMENTS} DROP approved;
ALTER TABLE {IFW_TBL_CONTENT} DROP deleted;
ALTER TABLE {IFW_TBL_USER_SESSIONS} CHANGE user_agent user_agent TEXT NOT NULL;