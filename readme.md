
## About Laravel Api

Hello everyone, I've created a boilerplate for those who wish to use Laravel to expose API's. This project has been setup to handle Authentication, Transactional Logs, Query Sorting and Filters and much more. I'm going to talk about all the features below.

## Libraries Used

1. JWT Auth: https://github.com/tymondesigns/jwt-auth
2. Laravel Eloquent Join: https://github.com/fico7489/laravel-eloquent-join
3. Make Observer Command: https://github.com/NickSynev/make-observer-command?files=1
4. Laravel Activity Log: https://github.com/spatie/laravel-activitylog

## Instructions

1. Clone this project and run composer update to download all the packages used in this project.
2. Run the following command to generate jwt auth config file. More Info regarding JWT Auth Library: 
> php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"

> php artisan jwt:secret

## Custom Commands

1. PsMigrationMake
2. PsSeederMake
3. PsTestMake
4. PsApi

### PsMigrationMake
This command is built on the default laravel "make:migration" command, I've extended this custom command to take columns as an option. 

Command:
> php artisan make:psmigration create_table_name_table --c column_names

Example:
> php artisan make:psmigration create_logins_table --c string.username:string.password:boolean.is_admin

For more information regarding on the usage of -c option please refer below to the "How to use -c option" Section.

### PsSeederMake
This command is built on top of the default laravel "make:seeder" command. I've extend this custom command to read the seeding data from a json file.

Command:

> php artisan make:psseeder model_nameSeeder -m model_name -d model_nameData -c column_names

The above command will make a seeder class for you in the database/seeder folder and also generate a data template for you in database/data folder so you can populate your data for the seeder. Make sure you include your seeder in databaseSeeder.php file.

Example:

> php artisan make:psseeder LoginSeeder -m Login -d LoginData -c string.username:string.password:boolean.is_admin

For more information regarding on the usage of -c option please refer below to the "How to use -c option" Section.

### PsTestMake
This command is built on top of the default laravel "make:test" command. I've extended this custom command to generate ready made dynamic functions for your test file. It will have the basic API test function (store, update, delete, view, duplicate etc..).

Command:

> php artisan make:pstest model_nameFeatureTest -m model_name -a api_name -c column_names

The above command will create a feature test class for you in tests/Feature folder with a ready made template to use.

Example:

> php artisan make:pstest LoginFeatureTest -m Login -a logins -c string.username:string.password:boolean.is_admin

For more information regarding on the usage of -c option please refer below to the "How to use -c option" Section.

### PsApi
This command is made to create all the API related files for you automatically. This will save on you from running the above commands manually.

To make life easier, I've created this command which will create ALL the necessary files at once.

Command

> php artisan make:psapi -m model_name -a api_name -c columns

The above command will create the following files for you:

1. Model
2. Migration
3. Observer
4. Seeder
5. Feature Test
6. Controller
7. Resource
8. Request

Example:
> php artisan make:psapi -m User -a users -c string.first_name:string.last_name:string.email


### How to use -c options:
To add a column you're going to use type.column_name. To add another column simply add ":" as a seperator and add your second column etc..

Example:
-c string.name:boolean.is_admin:integer.age:float.total:text.details:foreign.user_type.usertypes

Types:
1. string
2. integer
3. float
4. boolean
5. text
6. foreign

To use the "foreign" type, you'll have to write foreign.column_name.relational_table_name


## What to do after running the make:psapi Command:
1. Add Route
2. Seed Data
3. Feature Test
4. Model
5. Observer
6. Controller


