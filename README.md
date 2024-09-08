# About Tic Tac Toe

## How to use

- Clone the repository
- Run
```
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs
```
- `cp .env.example .env`
- `sail build --no-cache`
- `sail up`
- `sail artisan key:generate`
- `sail artisan migrate:fresh --seed` although we are not going to use the DB, but just to make sure everything is working fine
```
sail artisan migrate:fresh --seed

   INFO  Preparing database.

  Creating migration table ................................................................ 4.30ms DONE

   INFO  Running migrations.

  0001_01_01_000000_create_users_table .................................................... 5.66ms DONE
  2024_09_06_200118_create_personal_access_tokens_table ................................... 4.37ms DONE


   INFO  Seeding database.
```
- `sail artisan route:list --except-vendor` to check all routes
```
sail artisan route:list --except-vendor

GET|HEAD   / ........................................................................................
GET|HEAD   api ..................................................... Api\TicTacToeController@getState
DELETE     api .................................................... Api\TicTacToeController@resetGame
POST       api/restart .......................................... Api\TicTacToeController@restartGame
POST       api/{piece} ........................................... Api\TicTacToeController@placePiece

                                                                                     Showing [5] routes
```
- `sail test`
```
sail test

   PASS  Tests\Unit\ExampleTest
  ✓ that true is true

   PASS  Tests\Feature\ExampleTest
  ✓ the application returns a successful response                                                 0.08s

   PASS  Tests\Feature\TicTacToeTest
  ✓ get initial state                                                                             0.02s
  ✓ get session state                                                                             0.01s
  ✓ place piece                                                                                   0.01s
  ✓ restart game                                                                                  0.01s
  ✓ reset game                                                                                    0.01s

  Tests:    7 passed (17 assertions)
  Duration: 0.17s
```
