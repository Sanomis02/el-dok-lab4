up: docker-up
down: docker-down
restart: docker-down docker-up
init: docker-down-clear animals-clear docker-pull docker-build docker-up animals-init

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

docker-pull:
	docker-compose pull

docker-build:
	docker-compose build

animals-init: animals-composer-install animals-ready

animals-clear:
	docker run --rm -v ${PWD}:/app --workdir=/app alpine rm -f .ready

animals-composer-install:
	docker-compose run --rm animals-php-cli composer install

animals-wait-db:
	until docker-compose exec -T animals-postgres pg_isready --timeout=0 --dbname=app ; do sleep 1 ; done

animals-ready:
	docker run --rm -v ${PWD}:/app --workdir=/app alpine touch .ready

docker-dumps:
#	docker exec arvidijaold_mysql_img_1 sh -c 'exec mysqldump "arvidija" -uroot -p"rootpassw"' > /var/www/projects/arvidija.old/docker/mysql/duomenys/arvidija.sql
	docker exec -t slim-animals_animals_mysql_1 sh -c 'echo "[client]\n user=root\n password=\"rootpassw\"" > my.cnf && exec mysqldump --defaults-file=my.cnf --databases animals' > /var/www/projects/slim-animals/table-start-data.sql
