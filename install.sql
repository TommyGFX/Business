DROP TABLE IF EXISTS wcf1_business_category;
CREATE TABLE wcf1_business_category (
	categoryID int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	parentID int(10) NOT NULL default '0',
	title varchar(100) NOT NULL,
	description mediumtext,
	allowDescriptionHtml tinyint(1) NOT NULL default '0',
	time int(10) NOT NULL default '0',
	image varchar(255) NOT NULL default '',
	links INT(10) NOT NULL DEFAULT 0,
	canViewCategoryGroupIDs varchar(20) NOT NULL,
	canAddLinkGroupIDs varchar(20) NOT NULL,
	position int(10) default NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wcf1_business_category_structure;
CREATE TABLE wcf1_business_category_structure (
	parentID int(10) NOT NULL default '0',
	categoryID int(10) NOT NULL default '0',
	position smallint(5) NOT NULL default '0',
	PRIMARY KEY  (`parentID`,`categoryID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wcf1_business_category_to_group;
CREATE TABLE wcf1_business_category_to_group (
	categoryID INT(10) NOT NULL DEFAULT 0,
	groupID INT(10) NOT NULL DEFAULT 0,
	canViewCategory TINYINT(1) NOT NULL DEFAULT -1,
	canEnterCategory TINYINT(1) NOT NULL DEFAULT -1,
	canViewLink TINYINT(1) NOT NULL DEFAULT -1,
	canEnterLink TINYINT(1) NOT NULL DEFAULT -1,
	canAddLink TINYINT(1) NOT NULL DEFAULT -1,
	canAddLinkWithoutModeration TINYINT(1) NOT NULL DEFAULT -1,
	canEditOwnLink TINYINT(1) NOT NULL DEFAULT -1,
	canDeleteOwnLink TINYINT(1) NOT NULL DEFAULT -1,
	canRateLink TINYINT(1) NOT NULL DEFAULT -1,
	canReportLink TINYINT(1) NOT NULL DEFAULT -1,
	canVisitLink TINYINT(1) NOT NULL DEFAULT -1,
	canSeeComments TINYINT(1) NOT NULL DEFAULT -1,
	canAddComment TINYINT(1) NOT NULL DEFAULT -1,
	canEditOwnComment TINYINT(1) NOT NULL DEFAULT -1,
	canUploadAttachment TINYINT(1) NOT NULL DEFAULT -1,
	canDownloadAttachment TINYINT(1) NOT NULL DEFAULT -1,
	canViewAttachmentPreview TINYINT(1) NOT NULL DEFAULT -1,
	PRIMARY KEY (groupID, categoryID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wcf1_business_link;
CREATE TABLE wcf1_business_link (
	linkID int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	categoryID int(10) NOT NULL default '0',
	subject VARCHAR(255) NOT NULL DEFAULT '',
	ort VARCHAR(255) NOT NULL default '',
	isDisabled tinyint(1) NOT NULL default '0',
	status tinyint(1) NOT NULL default '0',
	statusComment text NOT NULL,
	isClosed tinyint(1) NOT NULL default '0',
	isSticky tinyint(1) NOT NULL default '0',
	arranger VARCHAR(255) NOT NULL default '',
	userID INT(10) NOT NULL,
	message mediumtext NOT NULL,
	username varchar(255) NOT NULL,
	url varchar(255) NOT NULL,
	kind varchar(155) NOT NULL,
	lastChangeTime int(10) NOT NULL default '0',
	hits INT(10) NOT NULL DEFAULT 0,
	ratingDisabled tinyint(1) NOT NULL default '0',
	isReported int(1) NOT NULL default '0',
	ratings smallint(5) NOT NULL default '0',
	rating smallint(7) NOT NULL default '0',
	age int(2) NOT NULL,
	shortDescription text NOT NULL,
	languageID int(2) NOT NULL,
	time INT(10) NOT NULL DEFAULT 0,
	attachments SMALLINT(5) NOT NULL DEFAULT 0,
	enableSmilies TINYINT(1) NOT NULL DEFAULT 1,
	enableHtml TINYINT(1) NOT NULL DEFAULT 0,
	enableBBCodes TINYINT(1) NOT NULL DEFAULT 1,
	comments SMALLINT(5) NOT NULL DEFAULT 0,
	FULLTEXT KEY (subject, message),
	KEY `categoryID` (`categoryID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wcf1_business_link_rating;
CREATE TABLE wcf1_business_link_rating (
	linkID INT(10) NOT NULL DEFAULT 0,
	rating INT(10) NOT NULL DEFAULT 0,
	userID INT(10) NOT NULL DEFAULT 0,
	ipAddress VARCHAR(15) NOT NULL DEFAULT '',
	KEY (linkID, userID),
	KEY (linkID, ipAddress)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wcf1_business_comment;
CREATE TABLE wcf1_business_comment (
	commentID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	linkID INT(10) NOT NULL DEFAULT 0,
	userID INT(10) NOT NULL DEFAULT 0,
	username VARCHAR(255) NOT NULL DEFAULT '',
	message MEDIUMTEXT NULL,
	time INT(10) NOT NULL DEFAULT 0,
	enableSmilies TINYINT(1) NOT NULL DEFAULT 1,
	enableHtml TINYINT(1) NOT NULL DEFAULT 0,
	enableBBCodes TINYINT(1) NOT NULL DEFAULT 1,
	KEY (linkID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wcf1_business_link_report;
CREATE TABLE wcf1_business_link_report (
	reportID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	linkID INT(10) NOT NULL DEFAULT 0,
	userID INT(10) NOT NULL DEFAULT 0,
	username VARCHAR(255) NOT NULL DEFAULT '',
	report MEDIUMTEXT NOT NULL,
	reportTime INT(10) NOT NULL DEFAULT 0,
	UNIQUE KEY (linkID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wcf1_business_menu_item;
CREATE TABLE wcf1_business_menu_item (
	menuItemID int(10) unsigned NOT NULL auto_increment,
	packageID int(10) unsigned NOT NULL default '0',
	menuItem varchar(255) NOT NULL default '',
	parentMenuItem varchar(255) NOT NULL default '',
	menuItemLink varchar(255) NOT NULL default '',
	menuItemIcon varchar(255) NOT NULL default '',
	showOrder int(10) NOT NULL default '0',
	permissions text NULL,
	options TEXT NULL,
	PRIMARY KEY (menuItemID),
	UNIQUE KEY (menuItem,packageID)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;