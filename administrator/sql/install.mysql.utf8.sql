/*
 *  @package Module UserWay for Joomla! 3.10.3
 *  @version install.mysql.utf8.sql: 191153 you radik
 *  @author UserWay Development Team
 *  @copyright (C) 2021 - UserWay Inc.
 *  @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

DROP TABLE IF EXISTS `#__userway`;

CREATE TABLE `#__userway` (
	`preference_id` INT(10) NOT NULL AUTO_INCREMENT,
	`account_id`    VARCHAR(255) NOT NULL,
	`state`         smallint(5) NOT NULL,
	`created_time`  TIMESTAMP NOT NULL,
	`updated_time`  TIMESTAMP NOT NULL,
	PRIMARY KEY (`preference_id`)
)
	ENGINE=InnoDB
	DEFAULT CHARSET=utf8mb4
  DEFAULT COLLATE=utf8mb4_unicode_ci;
