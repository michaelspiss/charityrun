BEGIN TRANSACTION;
CREATE TABLE IF NOT EXISTS `users` (
	`id`	INTEGER PRIMARY KEY AUTOINCREMENT,
	`username`	TEXT,
	`password`	TEXT
);
CREATE TABLE IF NOT EXISTS `user_packages` (
	`id`	INTEGER PRIMARY KEY AUTOINCREMENT,
	`user_id`	INTEGER,
	`package`	TEXT
);
INSERT INTO `user_packages` VALUES (1,1,'App\Auth\ElevatedAssistantPackage');
INSERT INTO `user_packages` VALUES (2,2,'App\Auth\AdminPackage');
CREATE TABLE IF NOT EXISTS `user_overrides` (
	`id`	INTEGER PRIMARY KEY AUTOINCREMENT,
	`user_id`	INTEGER,
	`permission`	TEXT,
	`value`	INTEGER
);
CREATE TABLE IF NOT EXISTS `stats` (
	`id`	TEXT,
	`value`	INTEGER,
	`active`	INTEGER DEFAULT 1,
	`item_order`	INTEGER UNIQUE
);
CREATE TABLE IF NOT EXISTS `settings` (
	`id`	TEXT,
	`value`	TEXT
);
CREATE TABLE IF NOT EXISTS `runners` (
	`id`	INTEGER PRIMARY KEY AUTOINCREMENT,
	`name`	TEXT,
	`total_rounds`	INTEGER DEFAULT 0,
	`class`	INTEGER
);
CREATE TABLE IF NOT EXISTS `logs` (
	`id`	INTEGER PRIMARY KEY AUTOINCREMENT,
	`class`	TEXT,
	`user`	INTEGER,
	`log_string`	TEXT,
	`rounds_changed`	INTEGER,
	`datetime`	TEXT,
	`active`	INTEGER DEFAULT 1
);
CREATE TABLE IF NOT EXISTS `lines` (
	`id`	INTEGER PRIMARY KEY AUTOINCREMENT,
	`from_lat`	NUMERIC,
	`to_lat`	NUMERIC,
	`from_long`	NUMERIC,
	`to_long`	NUMERIC,
	`distance`	NUMERIC
);
CREATE TABLE IF NOT EXISTS `groups` (
	`id`	INTEGER PRIMARY KEY AUTOINCREMENT,
	`name`	TEXT
);
CREATE TABLE IF NOT EXISTS `donors` (
	`id`	INTEGER PRIMARY KEY AUTOINCREMENT,
	`name`	TEXT,
	`donation`	NUMERIC,
	`amountIsFixed`	INTEGER,
	`wantsReceipt`	INTEGER,
	`runner_id`	INTEGER
);
COMMIT;
