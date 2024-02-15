## Instale o sanctum no projeto: 
composer require laravel/sanctum

## Publique a configuração: 
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

## Execute as migrations 
php artisan migrate

## adicione as linhas abaixo no arquivo app/Http/Kernel.php
'api' => [
    \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
],

## registre as migrations sanctum
php artisan vendor:publish --tag=sanctum-migrations


## Crie os Guards e Providers no arquivo config/auth.php

## Altere o array guard no arquivo config/sanctum.php


## Crie os middlewares 

- IsAuthenticated
- SetSanctumGuard
- VerifyAdminGuard


