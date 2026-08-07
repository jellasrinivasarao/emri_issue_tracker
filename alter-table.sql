ALTER TABLE txn_issue_sla
ADD COLUMN response_status VARCHAR(30) NOT NULL DEFAULT 'PENDING',
ADD COLUMN resolution_status VARCHAR(30) NOT NULL DEFAULT 'PENDING',
ADD COLUMN overall_status VARCHAR(30) NOT NULL DEFAULT 'RUNNING',
ADD COLUMN response_warning_at DATETIME NULL,
ADD COLUMN resolution_warning_at DATETIME NULL,
ADD COLUMN response_breached_at DATETIME NULL,
ADD COLUMN resolution_breached_at DATETIME NULL;

ALTER TABLE cfg_sla_policy
ADD COLUMN response_warning_percent DECIMAL(5,2) NOT NULL DEFAULT 80,
ADD COLUMN resolution_warning_percent DECIMAL(5,2) NOT NULL DEFAULT 80;


SELECT *
FROM cfg_sla_policy
ORDER BY sla_policy_id;



INSERT INTO mst_working_calendar
(
    calendar_code,
    calendar_name,
    organisation_id,
    timezone,
    is_active,
    created_at
)
VALUES
(
    'IND_STD',
    'India Standard Business Calendar',
    1,
    'Asia/Kolkata',
    1,
    NOW()
);


INSERT INTO mst_working_schedule
(
    calendar_id,
    day_of_week,
    start_time,
    end_time,
    is_working_day,
    created_at
)
VALUES

(1, 1, '09:00:00', '18:00:00', 1, NOW()),
(1, 2, '09:00:00', '18:00:00', 1, NOW()),
(1, 3, '09:00:00', '18:00:00', 1, NOW()),
(1, 4, '09:00:00', '18:00:00', 1, NOW()),
(1, 5, '09:00:00', '18:00:00', 1, NOW()),
(1, 6, NULL, NULL, 0, NOW()),
(1, 7, NULL, NULL, 0, NOW());

SELECT
    organisation_id,
    organisation_code,
    organisation_name,
    is_active
FROM mst_organisation
WHERE is_active = 1;

SELECT
    state_id,
    state_code,
    state_name,
    organisation_id,
    is_active
FROM mst_state
WHERE is_active = 1;


SELECT
    project_id,
    project_code,
    project_name,
    state_id,
    is_active
FROM mst_project
WHERE is_active = 1;

SELECT
    application_id,
    application_code,
    application_name,
    is_active
FROM mst_application
WHERE is_active = 1;

SELECT *
FROM map_project_application
WHERE project_id = 1
AND is_active = 1;


INSERT INTO map_project_application
(
    project_id,
    application_id,
    is_active,
    created_at
)
VALUES
(
    1,
    1,
    1,
    NOW()
);


SELECT
    service_id,
    service_code,
    service_name,
    is_active
FROM mst_service
WHERE is_active = 1;


SELECT *
FROM map_project_service
WHERE project_id = 1
AND service_id = 1
AND is_active = 1;

INSERT INTO map_project_service
(
    project_id,
    service_id,
    is_active,
    created_at
)
VALUES
(
    1,
    1,
    1,
    NOW()
);


INSERT INTO mst_priority
(
    priority_code,
    priority_name,
    display_order,
    is_active,
    created_at
)
VALUES
('P1', 'Critical', 1, 1, NOW()),
('P2', 'High',     2, 1, NOW()),
('P3', 'Medium',   3, 1, NOW()),
('P4', 'Low',      4, 1, NOW());

SELECT *
FROM mst_priority
WHERE is_active = 1;


INSERT INTO mst_issue_category
(
    category_code,
    category_name,
    description,
    is_active,
    created_at
)
VALUES
(
    'INC',
    'Incident',
    'Unexpected interruption or degradation of service',
    1,
    NOW()
),
(
    'REQ',
    'Service Request',
    'Request for IT service or support',
    1,
    NOW()
);




INSERT INTO cfg_sla_policy
(
    sla_policy_code,
    sla_policy_name,
    project_id,
    service_id,
    priority_id,
    calendar_id,
    response_time_minutes,
    resolution_time_minutes,
    response_warning_percent,
    resolution_warning_percent,
    is_active,
    effective_from,
    effective_to,
    created_at
)
VALUES
(
    'SLA_P1_STD',
    'Critical Priority Standard SLA',
    1,
    1,
    1,
    1,
    30,
    240,
    80,
    80,
    1,
    '2026-01-01',
    NULL,
    NOW()
);



SELECT
    sla_policy_id,
    sla_policy_code,
    sla_policy_name,
    project_id,
    service_id,
    priority_id,
    calendar_id,
    response_time_minutes,
    resolution_time_minutes,
    is_active,
    effective_from,
    effective_to
FROM cfg_sla_policy
WHERE is_active = 1;