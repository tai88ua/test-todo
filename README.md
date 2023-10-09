
 - docer-compose up
 - composer install in docker php : ``docker exec test-todo_php_1  composer install`` 
 - bin/console  doctrine:database:create : ``docker exec test-todo_php_1 bin/console  doctrine:database:create``
 - bin/console  doctrine:migrations:migrate   - run migrations : ``docker exec test-todo_php_1 bin/console  doctrine:migrations:migrate``
 - open http://172.20.2.14/api/doc
