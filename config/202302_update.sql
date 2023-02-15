DELETE FROM `mtrc`.`mtrc_attribute_relation` WHERE (`id` = '5');
DELETE FROM `mtrc`.`mtrc_attribute_relation` WHERE (`id` = '6');
DELETE FROM `mtrc`.`mtrc_attribute_relation` WHERE (`id` = '7');
DELETE FROM `mtrc`.`mtrc_attribute_relation` WHERE (`id` = '8');
UPDATE `mtrc`.`mtrc_attribute_relation` SET `id` = '5' WHERE (`id` = '9');
ALTER TABLE `mtrc`.`mtrc_order` 
ADD COLUMN `epolicy` VARCHAR(45) NULL AFTER `ecopy`;

ALTER TABLE `mtrc`.`mtrc_order` 
ADD COLUMN `json_generated` INT NULL AFTER `customer_id`;
