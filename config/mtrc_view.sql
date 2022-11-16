use mtrc;
DROP VIEW IF EXISTS `view_mtrc_call_customer`;
CREATE VIEW
    view_mtrc_call_customer AS
SELECT
    mtrc_call.*,
    mtrc_customer_import.id as mtrc_customer_import_id,
    mtrc_customer_import.cust_ref as cust_ref,
    mtrc_customer_import.name as name,
    mtrc_customer_import.gender as gender,
    mtrc_customer_import.tel_mbl as tel_mbl,
    mtrc_customer_import.tel_hom as tel_hom,
    mtrc_customer_import.hkid as hkid,
    mtrc_customer_import.acc_no as acc_no,
    mtrc_customer_import.card_brand as card_brand,
    mtrc_customer_import.card_type as card_type,
    mtrc_customer_import.dob as dob,
    mtrc_customer_import.married_status as married_status,
    mtrc_customer_import.address as address,
    mtrc_customer_import.living_person as living_person,
    mtrc_customer_import.email as email,
    mtrc_customer_import.member_since as member_since,
    mtrc_customer_import.occupation as occupation,
    mtrc_customer_import.position as position,
    mtrc_customer_import.fid as fid
FROM mtrc_call
    LEFT JOIN mtrc_customer_import ON mtrc_call.import_customer_id = mtrc_customer_import.id;
    
DROP VIEW IF EXISTS `view_mtrc_customer_call`;
CREATE VIEW
    view_mtrc_customer_call AS
SELECT
    mtrc_customer_import.*,
	mtrc_call.status
FROM mtrc_customer_import
    LEFT JOIN mtrc_call ON mtrc_call.import_customer_id = mtrc_customer_import.id;