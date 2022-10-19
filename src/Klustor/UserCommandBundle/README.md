#Klustor UserCommandBundle

## Installation Instructions
### composer.json
- composer require symfony/validator doctrine/annotations
- composer require symfony/http-client
- Add the bundle to composer

```json
"autoload": {
        "psr-4": {
            "App\\": "src/",
            "Klustor\\UserCommandBundle\\": "src/Klustor/UserCommandBundle/"
        }
    },
```

- Exclude the bundle to be autowired for services
```yaml
  App\:
  resource: '../src/'
  exclude:
  - '../src/Klustor/'
```