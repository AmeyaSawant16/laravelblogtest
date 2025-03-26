Steps to run the project

1. Build docker image
- docker build -t laravelblog:latest .

2. Run docker compose command
- docker-compose up -d

Wait for sometime to get all the services up and running.

3. Visit "http://localhost:8000" on brower

4. Register or login using below details.
- email: test@example.com

Make sure you register on "https://mailtrap.io/" to capture mails.

Caching Strategy use:  Write-through & Cache Batching

Technical Challenge faced:
1. Pushing 200k records to redis cache, Running out of memory.
2. Unable to build docker image of laravel, Trying to connect redis and database during image build process.


Database schema:

PostCollections table:
id number, title string, content text, excerpt text, image string, tags json, meta_title string, meta_description text, publish_type enum, publish_datetime timestamp, created_by number, updated_by number, created_at timestamp, updated_at timestamp, published boolean.
-------------
Comments table:
id number, user_id number, comment_date timestamp, user_comment string, post_id number, created_at timestamp, updated_at timestamp.
----------------
Users table:
id number, name string, email string, email_verified_at timestamp, password string, remember_token string, otp string, created_at timestamp, updated_at timestamp.
-----------------

If you are going to run "php artisan serve" then make sure you set all the ENV params properly in .env file

ELASTICSEARCH_HOST, 
ELASTICSEARCH_USER, 
ELASTICSEARCH_PASS, 
MAIL_MAILER, 
MAIL_HOST, 
MAIL_PORT, 
MAIL_USERNAME, 
MAIL_PASSWORD, 
REDIS_CLIENT, 
REDIS_HOST, 
REDIS_PASSWORD, 
REDIS_PORT, 
QUEUE_CONNECTION, 
CACHE_STORE, 
DB_CONNECTION, 
DB_HOST, 
DB_PORT, 
DB_DATABASE, 
DB_USERNAME, 
DB_PASSWORD
