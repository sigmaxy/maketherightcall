use mtrc;
ALTER TABLE `mtrc_order` 
CHANGE COLUMN `product_name_english` `product_name_english` VARCHAR(100) NULL DEFAULT NULL ,
CHANGE COLUMN `product_name_chinese` `product_name_chinese` VARCHAR(100) NULL DEFAULT NULL ;
