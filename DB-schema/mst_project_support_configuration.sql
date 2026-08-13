INSERT INTO mst_project_support_configuration
(
    project_id,
    config_code,
    config_name,
    default_support_level,
    default_team_type,
    sla_hours,
    description,
    is_active,
    created_by,
    updated_by,
    created_at,
    updated_at
)
VALUES
(
    2,
    'DEFAULT_SUPPORT_CFG_P2',
    'Project 2 Support Configuration',
    1,
    'HO_IT',
    4.00,
    'Default support configuration for project 2.',
    1,
    1,
    1,
    NOW(),
    NOW()
);


INSERT INTO mst_support_team
(
    team_code,
    team_name,
    team_type,
    support_level,
    email,
    phone,
    description,
    is_active,
    created_by,
    updated_by,
    created_at,
    updated_at
)
VALUES
(
    'HO_IT_LEVEL_1',
    'HO IT Level 1',
    'HO_IT',
    1,
    'hoit@example.com',
    '0000000000',
    'Default HO IT support team.',
    1,
    1,
    1,
    NOW(),
    NOW()
);



SELECT *
FROM mst_project_support_configuration
WHERE project_id = 2
  AND config_code = 'DEFAULT_SUPPORT_CFG_P2';

SELECT support_team_id
FROM mst_support_team
WHERE team_code = 'HO_IT_LEVEL_1';


INSERT INTO mst_issue_routing_rule
(
    rule_code,
    rule_name,
    project_id,
    support_configuration_id,
    issue_category_id,
    issue_type_id,
    priority_id,
    support_level,
    support_team_id,
    sla_hours,
    routing_priority,
    description,
    is_active,
    created_by,
    updated_by,
    created_at,
    updated_at
)
VALUES
(
    'P2_DEFAULT_HO_IT',
    'Project 2 Default HO IT Routing',
    2,
    1,
    NULL,
    NULL,
    NULL,
    1,
    1,
    4.00,
    1,
    'Default routing rule for Project 2.',
    1,
    1,
    1,
    NOW(),
    NOW()
);