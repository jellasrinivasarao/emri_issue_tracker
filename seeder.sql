
php artisan make:seeder IssueCategorySeeder
php artisan db:seed --class=IssueCategorySeeder


php artisan make:seeder PrioritySeeder
php artisan db:seed --class=PrioritySeeder



StateSeeder::class,
OrganisationSeeder::class,
HeadOfficeSeeder::class,
VendorSeeder::class,

ProjectSeeder::class,

ServiceSeeder::class,
ApplicationSeeder::class,
ApplicationModuleSeeder::class,

PrioritySeeder::class,
IssueCategorySeeder::class,
IssueStatusSeeder::class,

WorkingCalendarSeeder::class,
WorkingScheduleSeeder::class,
HolidaySeeder::class,

SupportGroupSeeder::class,

ProjectApplicationSeeder::class,
ProjectServiceSeeder::class,

SlaPolicySeeder::class,