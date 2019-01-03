#masunov/laravel-dto-yml-generator

## Generate DTO

Command for generate DTO

```
php artisan make:dto App/Domain/Dto/SimpleDto 
```

Command for generate DTO with yml

```
php artisan make:dto App/Domain/Dto/SimpleDto --yml
```

## Generate YML for DTO

Command for build YML for same class

```
php artisan build:yml --class=App/Domain/Dto/SimpleDto
```

Command for build YML for namespace

```
php artisan build:yml --namespace=App/Domain/Dto
```