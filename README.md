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

1. Clona el repositorio
2. Ejecuta `composer install`
3. Copia `.env.example` a `.env` y configura tu base de datos
4. Ejecuta `php artisan key:generate`
5. Ejecuta `php artisan migrate`
6. Instala dependencias de frontend con `npm install`
7. Inicia el servidor con `php artisan serve`

## Scripts Disponibles

- `composer setup`: Instalación completa del proyecto
- `composer dev`: Inicia servidor, cola, logs y vite en modo desarrollo
- `composer test`: Ejecuta los tests

## Tecnologías

- **Backend**: Laravel 12, PHP 8.2+
- **Frontend**: Vite, Bootstrap
- **Base de Datos**: MySQL/PostgreSQL/SQLite
- **Autenticación**: Laravel Sanctum
- **Testing**: Pest
