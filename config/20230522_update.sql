ALTER TABLE `mtrc`.`mtrc_order` 
ADD COLUMN `credit_card` MEDIUMBLOB NULL AFTER `authorizationCode`,
ADD COLUMN `transaction_currency` VARCHAR(45) NULL AFTER `levy`,
ADD COLUMN `transacted_initial_premium` VARCHAR(45) NULL AFTER `transaction_currency`,
ADD COLUMN `application_sign_date` VARCHAR(45) NULL AFTER `transacted_initial_premium`;
