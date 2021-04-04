# Commands to run 

```
docker-compose up -d
docker exec -it commission_calculator_php sh
```
Inside docker:
```
composer install
cp .env.example .env
```
Fill `EXCHANGE_REST_API_KEY` with your api key.
Then run command
```
php src/calculate-commission.php src/input.csv 
```
Note: `input.csv` should be inside docker

Run tests:
```
./bin/phpunit
```
or to run only my tests
```
./bin/phpunit ./tests/Service/CalculationServiceTest
```
