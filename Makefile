.PHONY: dev

dev:
	cd docker && docker-compose up -d --remove-orphans

down:
	cd docker && docker-compose down -t0
