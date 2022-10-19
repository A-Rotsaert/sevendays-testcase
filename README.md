# Child Advisor

Testcase for [Sevendays](https://www.sevendays.be/en)
[Confluence page](https://confluence.external-share.com/content/00766d82-72d8-467d-92cd-e6dcc903061e)

## Installation & usage

```bash
composer install
npm install
docker-compose up -d --build
bin/console doctrine:migrations:migrate
symfony server:start
````

## Code quality

We're using PSR12 as coding standard and php-cs-fixer to fix code styling.

#### php-cs-fixer

```bash
bin/php-cs-fixer fix src/ --rules=@PSR12
```

## License

[MIT](https://choosealicense.com/licenses/mit/)