DELETE FROM {IFW_TBL_SYS_PREFERENCES} WHERE preference = 'prefWelcomeCode';
DELETE FROM {IFW_TBL_SYS_PREFERENCES} WHERE preference = 'prefNewArticleCount';
DELETE FROM {IFW_TBL_SYS_PREFERENCES} WHERE preference = 'prefNewCommentCount';
DELETE FROM {IFW_TBL_SYS_PREFERENCES} WHERE preference = 'prefPopularItemCount';
DELETE FROM {IFW_TBL_SYS_PREFERENCES} WHERE preference = 'prefSimilarItemCount';
DROP TABLE IF EXISTS maj_error_log;
INSERT INTO {IFW_TBL_SYS_PREFERENCES}(preference, content) VALUES('{C_PREF_LINK_STYLE}', '1');