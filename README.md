# Credit Sale API

API RESTful para la gestión de ventas a crédito. Permite administrar rutas de cobro, clientes y usuarios con un sistema de control de acceso basado en roles (RBAC).

## Stack

| Capa | Tecnología |
| --- | --- |
| Framework | Laravel 12 / PHP 8.2+ |
| Autenticación | tymon/jwt-auth v2.2 (tokens Bearer) |
| Base de datos | MySQL |
| Contenedores | Laravel Sail (Docker) |
| Testing | Pest v4 |

---

## Dominio

```text
User ──────────────────> LoanRoad (como vendedor)
User ──────────────────> LoanRoad (como supervisor)
LoanRoad ──────────────> Customer
```

- **User** — vendedores, supervisores y administradores del sistema.
- **LoanRoad** — ruta de cobro asignada a un vendedor y supervisada por un supervisor.
- **Customer** — cliente con cuota, interés y orden de cobro, asignado a una ruta.

Los tres modelos usan **soft deletes**.

---

## Instalación

### Requisitos

- PHP 8.2+
- Composer
- Docker (para Laravel Sail)

### Pasos

```bash
# 1. Clonar el repositorio
git clone <url> && cd credit-sale-APIRestful

# 2. Instalar dependencias PHP
composer install

# 3. Configurar el entorno
cp .env.example .env
php artisan key:generate

# 4. Generar la clave secreta para JWT
php artisan jwt:secret

# 5. Levantar la base de datos con Docker
./vendor/bin/sail up -d

# 6. Ejecutar migraciones y seeders
./vendor/bin/sail artisan migrate --seed
```

### Variables de entorno clave

```env
APP_KEY=          # generada con php artisan key:generate
JWT_SECRET=       # generada con php artisan jwt:secret
JWT_TTL=60        # tiempo de vida del token en minutos

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=credit_sale
DB_USERNAME=sail
DB_PASSWORD=password

AUTH_GUARD=api
```

---

## Usuarios de prueba (seeder)

| Email | Contraseña | Nivel |
| --- | --- | --- |
| `admin@admin.com` | `password` | administrador |
| `supervisor@supervisor.com` | `supervisorpass` | supervisor |
| `vendedor@vendedor.com` | `vendedorpass` | vendedor |

El seeder también crea 5 vendedores adicionales, cada uno con su ruta y 20 clientes.

---

## Autenticación

Todos los endpoints (excepto `login`) requieren el token JWT en el header:

```http
Authorization: Bearer <token>
```

### Obtener token

```http
POST /api/v1/auth/login
Content-Type: application/json

{
  "email": "admin@admin.com",
  "password": "password"
}
```

**Respuesta:**

```json
{
  "success": true,
  "message": "Autenticación exitosa",
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "token_type": "bearer",
    "expires_in": 3600
  }
}
```

---

## Endpoints

### Auth `/api/v1/auth`

| Método | Ruta | Auth | Descripción |
| --- | --- | --- | --- |
| `POST` | `/login` | No | Iniciar sesión |
| `POST` | `/register` | JWT + Admin | Registrar nuevo usuario |
| `GET` | `/me` | JWT | Perfil del usuario autenticado |
| `POST` | `/logout` | JWT | Cerrar sesión (invalida el token) |
| `POST` | `/refresh-token` | JWT | Renovar token |

### Usuarios `/api/v1/users`

Requiere nivel **administrador** en todas las acciones.

| Método | Ruta | Descripción |
| --- | --- | --- |
| `GET` | `/users` | Listar usuarios (`?level=vendedor`) |
| `POST` | `/users` | Crear usuario |
| `GET` | `/users/{id}` | Ver usuario |
| `PUT` | `/users/{id}` | Actualizar usuario |
| `DELETE` | `/users/{id}` | Eliminar usuario (soft delete) |

### Rutas de cobro `/api/v1/loan-roads`

| Método | Ruta | Nivel mínimo | Descripción |
| --- | --- | --- | --- |
| `GET` | `/loan-roads` | supervisor | Listar rutas (`?inactive=&user_id=&supervisor_id=`) |
| `POST` | `/loan-roads` | administrador | Crear ruta |
| `GET` | `/loan-roads/{id}` | cualquiera* | Ver ruta |
| `PUT` | `/loan-roads/{id}` | supervisor* | Actualizar ruta |
| `DELETE` | `/loan-roads/{id}` | administrador | Eliminar ruta (soft delete) |

> \*El vendedor solo puede ver su propia ruta. El supervisor solo puede editar las rutas que supervisa.

### Clientes `/api/v1/customers`

| Método | Ruta | Nivel mínimo | Descripción |
| --- | --- | --- | --- |
| `GET` | `/customers` | cualquiera* | Listar clientes (`?loan_road_id=&delinquent=`) |
| `POST` | `/customers` | cualquiera | Crear cliente |
| `GET` | `/customers/{id}` | cualquiera* | Ver cliente |
| `PUT` | `/customers/{id}` | cualquiera* | Actualizar cliente |
| `DELETE` | `/customers/{id}` | administrador | Eliminar cliente (soft delete) |

> \*El vendedor solo ve/edita clientes de su propia ruta. El supervisor solo edita clientes de rutas que supervisa.

---

## Matriz de permisos

### Usuarios

| Acción | Administrador | Supervisor | Vendedor |
| --- | :---: | :---: | :---: |
| Ver / Crear / Editar / Eliminar | ✅ | ❌ | ❌ |

### Rutas de cobro

| Acción | Administrador | Supervisor | Vendedor |
| --- | :---: | :---: | :---: |
| Ver listado | ✅ | ✅ | ❌ |
| Ver una | ✅ | ✅ | ✅ solo la suya |
| Crear | ✅ | ❌ | ❌ |
| Editar | ✅ | ✅ solo las suyas | ❌ |
| Eliminar | ✅ | ❌ | ❌ |

### Clientes

| Acción | Administrador | Supervisor | Vendedor |
| --- | :---: | :---: | :---: |
| Ver listado | ✅ todos | ✅ todos | ✅ solo su ruta |
| Ver uno | ✅ | ✅ | ✅ solo su ruta |
| Crear | ✅ | ✅ | ✅ |
| Editar | ✅ | ✅ solo su ruta | ✅ solo su ruta |
| Eliminar | ✅ | ❌ | ❌ |

---

## Formato de respuesta

Todas las respuestas siguen la misma estructura:

```json
{ "success": true, "message": "...", "data": {} }
```

```json
{
  "success": true,
  "message": "...",
  "data": [],
  "pagination": {
    "total": 50,
    "per_page": 10,
    "current_page": 1,
    "last_page": 5,
    "from": 1,
    "to": 10
  }
}
```

```json
{ "success": false, "message": "...", "errors": {} }
```

**Códigos HTTP usados:**

| Código | Situación |
| --- | --- |
| `200` | Éxito |
| `201` | Recurso creado |
| `204` | Eliminado (sin contenido) |
| `401` | Sin token o token inválido |
| `403` | Sin permisos para la acción |
| `404` | Recurso no encontrado |
| `409` | Conflicto (duplicado) |
| `422` | Error de validación |

---

## Comandos útiles

```bash
# Levantar entorno Docker
./vendor/bin/sail up -d

# Detener contenedores
./vendor/bin/sail down

# Ejecutar migraciones
./vendor/bin/sail artisan migrate

# Ejecutar migraciones + seeders
./vendor/bin/sail artisan migrate --seed

# Ejecutar todos los tests
php artisan test

# Ejecutar tests con detalle
php artisan test --verbose

# Limpiar caché de configuración
php artisan config:clear && php artisan cache:clear
```

---

## Tests

```bash
php artisan test
# Tests: 42 passed — Duration: ~3s
```

Cobertura por módulo:

| Archivo | Tests | Qué verifica |
| --- | :---: | --- |
| `AuthTest` | 8 | Login, logout, register con roles, invalidación de token |
| `UserTest` | 7 | CRUD de usuarios, restricciones por nivel |
| `CustomerTest` | 11 | Scoping por rol en index, Policies en show/update/destroy |
| `LoanRoadTest` | 13 | Restricciones por acción, ownership en show/update |

---

## Estructura del proyecto

```text
app/
├── Helpers/ApiResponse.php        # Respuestas JSON estandarizadas
├── Http/
│   ├── Controllers/Api/           # AuthController, UserController, CustomerController, LoanRoadController
│   ├── Middleware/CheckLevel.php  # Middleware RBAC (level:administrador,supervisor...)
│   ├── Requests/                  # Form Requests con validación
│   └── Resources/                 # UserResource, CustomerResource, LoanRoadResource
├── Models/                        # User, Customer, LoanRoad
└── Policies/                      # CustomerPolicy, LoanRoadPolicy
```
