# Credit Sales API

Un sistema de gestión de ventas a crédito desarrollado con Laravel 12.

## Características

- Gestión de clientes
- Administración de rutas de préstamos
- Sistema de usuarios con niveles y roles
- API RESTful
- Autenticación con Laravel Sanctum

## Estructura del Proyecto

Este proyecto está en desarrollo y actualmente incluye:

- **Usuarios**: Sistema de autenticación con niveles y teléfonos
- **Clientes**: Gestión básica de clientes  
- **Rutas de Préstamos**: Administración de rutas con comisiones y supervisores

## Instalación

Este proyecto usa **Laravel Sail** (Docker) para la base de datos MySQL. Asegúrate de tener Docker corriendo antes de ejecutar cualquier comando de base de datos.

1. Clona el repositorio
2. Ejecuta `composer install`
3. Copia `.env.example` a `.env` y configura tu base de datos
4. Ejecuta `php artisan key:generate`
5. Levanta los contenedores con `./vendor/bin/sail up -d`
6. Ejecuta las migraciones con `./vendor/bin/sail artisan migrate`
7. Instala dependencias de frontend con `npm install`

## Scripts Disponibles

- `./vendor/bin/sail up -d`: Levanta los contenedores en segundo plano
- `./vendor/bin/sail down`: Detiene los contenedores
- `./vendor/bin/sail artisan migrate`: Ejecuta las migraciones
- `composer dev`: Inicia servidor, cola, logs y vite en modo desarrollo
- `composer test`: Ejecuta los tests

## Tecnologías

- **Backend**: Laravel 12, PHP 8.2+
- **Frontend**: Vite, Bootstrap
- **Base de Datos**: MySQL/PostgreSQL/SQLite
- **Autenticación**: Laravel Sanctum
- **Testing**: Pest
