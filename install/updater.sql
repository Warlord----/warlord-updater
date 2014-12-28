DROP TABLE IF EXISTS `updates`;
CREATE TABLE `updates` (
  `id` INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `created_at` DATETIME,
  `patch_file` VARCHAR(255) NOT NULL,
  UNIQUE(`patch_file`)
) CHARACTER SET 'utf8';
