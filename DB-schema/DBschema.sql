SHOW COLUMNS FROM mst_project;
SHOW COLUMNS FROM mst_organisation;
SHOW COLUMNS FROM mst_state;
SHOW COLUMNS FROM mst_head_office;
SHOW COLUMNS FROM mst_vendor;
SHOW COLUMNS FROM mst_support_group;
SHOW COLUMNS FROM mst_application;
SHOW COLUMNS FROM mst_application_module;
SHOW COLUMNS FROM mst_service;
SHOW COLUMNS FROM mst_issue_category;
SHOW COLUMNS FROM mst_issue_status;
SHOW COLUMNS FROM mst_priority;
SHOW COLUMNS FROM mst_working_calendar;
SHOW COLUMNS FROM mst_working_schedule;
SHOW COLUMNS FROM mst_calendar_holiday;


SELECT
    TABLE_NAME,
    CONSTRAINT_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'issue_tracker_db'
  AND REFERENCED_TABLE_NAME IS NOT NULL
ORDER BY
    TABLE_NAME,
    CONSTRAINT_NAME;
	
	
	SELECT
    TABLE_NAME,
    COLUMN_NAME,
    COLUMN_TYPE,
    IS_NULLABLE,
    COLUMN_KEY,
    COLUMN_DEFAULT,
    EXTRA
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = 'issue_tracker_db'
ORDER BY
    TABLE_NAME,
    ORDINAL_POSITION;
	
	
1. Organisation Type
2. Organisation
3. State
4. Head Office
5. Vendor
6. Support Group
7. Application              
8. Priority
9. Issue Category
10. Issue Status
11. Service

12. Application Module      ← depends on Application
13. Project                 ← depends on State
14. Project ↔ Application
15. Project ↔ Service

16. Working Calendar        ← depends on Organisation
17. Working Schedule        ← depends on Calendar
18. Calendar Holiday        ← depends on Calendar

19. Role
20. Privilege
21. Role ↔ Privilege
22. User
23. Support Group ↔ User
24. SLA Policy
25. Issue Routing