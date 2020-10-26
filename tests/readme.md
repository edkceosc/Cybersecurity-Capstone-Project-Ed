Tests
====

We use PHPUnit to run tests in this directory.

There are 2 categories of tests:

* Unit tests: in `./tests/unit/`
* Database tests: in `./tests/db/`

Unit tests
---

The unit tests don't do any I/O. They are easy to run:

```
composer install
./vendor/bin/phpunit --testdox --bootstrap ./vendor/autoload.php  --no-configuration ./tests/unit/
```

Database tests
---

The database test need a running postgres database. It is recommended to use a dedicated database
for the tests, since the tests may alter the data.

The database to use can be configured via the environment variable `DATABASE_URL`.
For example: `psql://dbuser:secret@localhost:5432/capstone_test_db`.

Before the first test run, the schema [db/schema.sql](../db/schema.sql) must be applied to the empty database.

Once this is done, the tests can run similar to the unit tests:

```
export DATABASE_URL=psql://dbuser:secret@localhost:5432/capstone_test_db
./vendor/bin/phpunit --testdox --bootstrap ./vendor/autoload.php  --no-configuration ./tests/db/
```
