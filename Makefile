init:
	cp .env.example .env
	cp src/.env.example src/.env
	docker-compose up -d
	docker-compose exec app composer install
	docker-compose exec app php artisan key:generate
	docker-compose exec app php artisan storage:link
	docker-compose exec app chmod -R 777 storage bootstrap/cache
	@make fresh
	docker-compose exec app yarn install
	@make watch
init-mock:
	cp .env.example .env
	cp src/.env.example src/.env
	docker-compose up -d
	docker-compose exec app composer install
	docker-compose exec app php artisan key:generate
	docker-compose exec app php artisan storage:link
	docker-compose exec app chmod -R 777 storage bootstrap/cache
	docker-compose exec app yarn install
	docker-compose exec app yarn dev
init-update:
	docker-compose up -d
	docker-compose exec app composer install
	docker-compose exec app php artisan key:generate
	docker-compose exec app php artisan storage:link
	docker-compose exec app chmod -R 777 storage bootstrap/cache
	@make fresh
	docker-compose exec app yarn install
	docker-compose exec app yarn dev
start:
	docker-compose start
stop:
	docker-compose stop
remake:
	@make destroy
	@make init
re-update:
	@make destroy
	@make init-update
watch:
	cd src && echo "start up now!🎉🎉🎉🎉🎉🎉🎉🎉🎉🎉🎉" && yarn watch
dev:
	cd src && echo "build now!🎉🎉🎉🎉🎉🎉🎉🎉🎉🎉🎉" && yarn dev
queue:
	docker-compose exec app php artisan queue:work
destroy:
	docker-compose down --rmi all --volumes --remove-orphans
ps:
	docker-compose ps
app:
	docker-compose exec app ash
web:
	docker-compose exec web ash
db:
	docker exec -it laravel-cognito-oauth_db bash
migrate:
	docker-compose exec app php artisan migrate
migrate-fresh:
	docker-compose exec app php artisan migrate:fresh
fresh:
	docker-compose exec app php artisan migrate:fresh --seed
seed:
	docker-compose exec app php artisan db:seed
test:
	docker-compose exec app php artisan test
cache:
	docker-compose exec app php artisan cache:clear
	docker-compose exec app php artisan config:clear
	docker-compose exec app php artisan route:clear
	docker-compose exec app php artisan view:clear
install:
	docker-compose exec app composer install
	docker-compose exec app yarn install