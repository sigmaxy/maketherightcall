use mtrc;
ALTER TABLE `mtrc_customer_import` 
ADD COLUMN `team` VARCHAR(45) NULL DEFAULT 'A' AFTER `cust_ref`;

