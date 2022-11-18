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
    LEFT JOIN mtrc_customer_import ON mtrc_call.import_customer_id = mtrc_customer_import.id
    ORDER BY id;

DROP VIEW IF EXISTS `view_mtrc_customer_call`;
CREATE VIEW
    view_mtrc_customer_call AS
SELECT
    mtrc_customer_import.*,
	mtrc_call.status
FROM mtrc_customer_import
    LEFT JOIN mtrc_call ON mtrc_call.import_customer_id = mtrc_customer_import.id
    ORDER BY id;


DROP VIEW IF EXISTS `view_mtrc_customer_call_ajax`;
CREATE VIEW
    view_mtrc_customer_call_ajax AS
SELECT
    mtrc_customer_import.id AS id,
    mtrc_customer_import.cust_ref AS cust_ref,
    mtrc_customer_import.name AS name,
    mtrc_customer_import.gender AS gender,
    mtrc_customer_import.tel_mbl AS tel_mbl,
    mtrc_customer_import.tel_hom AS tel_hom,
    mtrc_customer_import.hkid AS hkid,
    mtrc_customer_import.acc_no AS acc_no,
    mtrc_customer_import.card_brand AS card_brand,
    mtrc_customer_import.card_type AS card_type,
    mtrc_customer_import.dob AS dob,
    mtrc_customer_import.married_status AS married_status,
    mtrc_customer_import.address AS address,
    mtrc_customer_import.living_person AS living_person,
    mtrc_customer_import.email AS email,
    mtrc_customer_import.member_since AS member_since,
    mtrc_customer_import.occupation AS occupation,
    mtrc_customer_import.position AS position,
    mtrc_customer_import.fid AS fid,
    FROM_UNIXTIME(mtrc_customer_import.created_at) as created_at,
    mtrc_customer_import.created_by AS created_by,
    FROM_UNIXTIME(mtrc_customer_import.updated_at) as updated_at,
    mtrc_customer_import.updated_by AS updated_by,
    mtrc_call.assignee_id,
    CASE
        when mtrc_call.status IS NULL THEN 'Not Assigned'
        when mtrc_call.status = 1 THEN 'Pending'
        when mtrc_call.status = 2 THEN 'Consider'
        when mtrc_call.status = 3 THEN 'DNQ'
        when mtrc_call.status = 4 THEN 'Reject'
        when mtrc_call.status = 5 THEN 'RTT'
        when mtrc_call.status = 6 THEN 'Busy'
        when mtrc_call.status = 7 THEN 'No Answer'
        when mtrc_call.status = 8 THEN 'Invalid Number'
        when mtrc_call.status = 9 THEN 'Success'
        when mtrc_call.status = 10 THEN 'Opt Out'
    END AS status,
    user__field_agentcode.field_agentcode_value AS assignee,
    user__field_agentname.field_agentname_value AS updated_by_name
FROM mtrc_customer_import
    LEFT JOIN mtrc_call ON mtrc_call.import_customer_id = mtrc_customer_import.id
    LEFT JOIN user__field_agentcode on mtrc_call.assignee_id = user__field_agentcode.entity_id
    LEFT JOIN user__field_agentname on mtrc_customer_import.updated_by = user__field_agentname.entity_id
    ORDER BY id;

DROP VIEW IF EXISTS `view_mtrc_call_customer_ajax`;
CREATE VIEW
    view_mtrc_call_customer_ajax AS
SELECT
    mtrc_call.id as id,
    mtrc_call.import_customer_id as import_customer_id,
    mtrc_call.assignee_id as assignee_id,
    CASE
        when mtrc_call.status IS NULL THEN 'Not Assigned'
        when mtrc_call.status = 1 THEN 'Pending'
        when mtrc_call.status = 2 THEN 'Consider'
        when mtrc_call.status = 3 THEN 'DNQ'
        when mtrc_call.status = 4 THEN 'Reject'
        when mtrc_call.status = 5 THEN 'RTT'
        when mtrc_call.status = 6 THEN 'Busy'
        when mtrc_call.status = 7 THEN 'No Answer'
        when mtrc_call.status = 8 THEN 'Invalid Number'
        when mtrc_call.status = 9 THEN 'Success'
        when mtrc_call.status = 10 THEN 'Opt Out'
    END AS status,
    CASE
        when mtrc_call.reject_reason = 0 THEN 'No Select'
        when mtrc_call.reject_reason = 1 THEN 'Premium too high (expensive)'
        when mtrc_call.reject_reason = 2 THEN 'Protetion period too long / short'
        when mtrc_call.reject_reason = 3 THEN 'Coverage not enough'
        when mtrc_call.reject_reason = 4 THEN 'Well covered'
        when mtrc_call.reject_reason = 5 THEN 'Coverage not interest'
        when mtrc_call.reject_reason = 6 THEN 'No need'
        when mtrc_call.reject_reason = 7 THEN 'Leaving HK'
        when mtrc_call.reject_reason = 8 THEN 'Unsatisfied with Partners'
        when mtrc_call.reject_reason = 9 THEN 'Unsatisfied with Chubb'
        when mtrc_call.reject_reason = 10 THEN 'Others'
    END AS reject_reason,
    mtrc_call.remark as remark,
    mtrc_call.count as count,
    FROM_UNIXTIME(mtrc_call.updated_at) as updated_at,
    mtrc_call.updated_by as updated_by,
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
    mtrc_customer_import.fid as fid,
    user__field_agentcode.field_agentcode_value AS assignee,
    user__field_agentname.field_agentname_value AS updated_by_name
FROM mtrc_call
    LEFT JOIN mtrc_customer_import ON mtrc_call.import_customer_id = mtrc_customer_import.id
    LEFT JOIN user__field_agentcode on mtrc_call.assignee_id = user__field_agentcode.entity_id
    LEFT JOIN user__field_agentname on mtrc_call.updated_by = user__field_agentname.entity_id
    ORDER BY id;
