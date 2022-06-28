init:
	cp .env.example .env
	docker-compose up -d
start:
	docker-compose start
stop:
	docker-compose stop
restart:
	docker-compose stop
	docker-compose start
destroy:
	docker-compose down --rmi all --volumes --remove-orphans
ps:
	docker-compose ps
remake:
	@make destroy
	@make init