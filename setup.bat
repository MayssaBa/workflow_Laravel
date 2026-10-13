@echo off
REM ---- Modeles + migrations + factories ----
for %%m in (Department Team Project Task Comment Issue TaskHistory RequestReply AiConversation AiMessage InternalRequest) do php artisan make:model %%m -mf
php artisan make:notifications-table
php artisan make:migration create_project_user_table

REM ---- Controleurs ----
for %%c in (Project Task Comment Issue Request) do php artisan make:controller %%cController --resource
for %%c in (Profile Dashboard Notification Assistant) do php artisan make:controller %%cController
for %%c in (Employee Department Team) do php artisan make:controller Admin/%%cController --resource

REM ---- Policies, middleware, requests, observer, notifications, seeder ----
php artisan make:policy ProjectPolicy --model=Project
php artisan make:policy TaskPolicy --model=Task
php artisan make:policy RequestPolicy --model=InternalRequest
php artisan make:policy UserPolicy --model=User
php artisan make:middleware RoleMiddleware
php artisan make:middleware EnsureUserIsActive
php artisan make:request StoreTaskRequest
php artisan make:request UpdateTaskStatusRequest
php artisan make:observer TaskObserver --model=Task
for %%n in (TaskAssigned TaskUpdated TaskBlocked RequestReplied) do php artisan make:notification %%n
php artisan make:seeder DemoDataSeeder

REM ---- Dossiers et fichiers que Artisan ne cree pas ----
mkdir app\Enums
mkdir app\Services\Assistant\Tools
mkdir resources\views\dashboard
mkdir resources\views\projects
mkdir resources\views\tasks
mkdir resources\views\requests
mkdir resources\views\notifications
mkdir resources\views\profile
mkdir resources\views\assistant
mkdir resources\views\components
mkdir resources\views\admin\employees
mkdir resources\views\admin\departments
mkdir resources\views\admin\teams
for %%f in (Role TaskStatus TaskPriority) do type nul > app\Enums\%%f.php
type nul > app\Services\DashboardService.php
type nul > app\Services\Assistant\AssistantService.php
for %%f in (GetMyTasksTool GetProjectSummaryTool GetTeamWorkloadTool GetOpenRequestsTool) do type nul > app\Services\Assistant\Tools\%%f.php
type nul > config\assistant.php
for %%f in (employee manager admin) do type nul > resources\views\dashboard\%%f.blade.php
type nul > resources\views\assistant\index.blade.php
type nul > resources\js\dashboard.js
type nul > resources\js\assistant.js
echo Termine.