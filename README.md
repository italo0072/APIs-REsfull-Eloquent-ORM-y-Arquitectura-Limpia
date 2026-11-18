# La Buena Mesa — API RESTful del Menú

API RESTful construida con **Laravel 12** y **Eloquent ORM** para centralizar la gestión del menú del restaurante *La Buena Mesa*. Permite consultar, crear, actualizar y eliminar platillos, organizados por categorías, con respuestas JSON consistentes y validaciones en el servidor.

## Contenido

- [Características](#características)
- [Tecnologías](#tecnologías)
- [Instalación](#instalación)
- [Estructura del proyecto](#estructura-del-proyecto)
- [Arquitectura](#arquitectura)
- [Endpoints](#endpoints)
- [Ejemplos de uso](#ejemplos-de-uso)
- [Pruebas automáticas](#pruebas-automáticas)
- [Pruebas manuales con Postman](#pruebas-manuales-con-postman)
- [Solución de problemas](#solución-de-problemas)
- [Escalabilidad](#escalabilidad)

## Características

- CRUD completo de elementos del menú: `GET`, `POST`, `PUT`, `PATCH` y `DELETE`.
- Categorías normalizadas en su propia tabla, con relación `Category 1 — N MenuItem`.
- Filtro por categoría aceptando **ID, slug o nombre visible**.
- Búsqueda por nombre o descripción, filtro por disponibilidad y paginación.
- Validaciones con *Form Requests* y mensajes personalizados en español para las reglas principales.
- Respuestas JSON consistentes mediante *API Resources*.
- Arquitectura por capas: **Controller → Service → Repository → Model**.
- Migraciones, seeders, factories y pruebas de feature.

## Tecnologías

| Componente | Uso |
|---|---|
| PHP | 8.2 o superior |
| Laravel | 12.x |
| ORM | Eloquent |
| Base de datos | MySQL / MariaDB (SQLite opcional) |
| Pruebas | PHPUnit 11 |

## Instalación

Requisitos: **PHP 8.2+**, **Composer** y **MySQL/MariaDB** (XAMPP sirve) o SQLite.

```bash
# 1. Instalar dependencias
composer install

# 2. Configurar el entorno
cp .env.example .env          # En Windows: copy .env.example .env
php artisan key:generate

# 3. Crear la base de datos `la_buena_mesa` en MySQL

# 4. Migrar y poblar datos de ejemplo
php artisan migrate --seed

# 5. Levantar el servidor
php artisan serve
```

La API queda disponible en `http://localhost:8000/api`.

Con una base recién sembrada se crean **5 categorías** y **10 platillos**, con IDs del 1 al 5 para categorías y del 1 al 10 para platillos.

> **SQLite:** en `.env` configura `DB_CONNECTION=sqlite`, elimina las demás variables `DB_*`, crea el archivo `database/database.sqlite` y ejecuta `php artisan migrate --seed`.

## Estructura del proyecto

```
la-buena-mesa-api/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/MenuItemController.php
│   │   ├── Requests/
│   │   │   ├── StoreMenuItemRequest.php
│   │   │   └── UpdateMenuItemRequest.php
│   │   └── Resources/
│   │       ├── CategoryResource.php
│   │       └── MenuItemResource.php
│   ├── Models/
│   │   ├── Category.php
│   │   └── MenuItem.php
│   ├── Providers/AppServiceProvider.php
│   ├── Repositories/
│   │   ├── Contracts/MenuItemRepositoryInterface.php
│   │   └── EloquentMenuItemRepository.php
│   └── Services/MenuItemService.php
├── bootstrap/app.php
├── config/
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── public/
├── routes/api.php
├── storage/
├── tests/Feature/MenuItemApiTest.php
├── artisan
├── composer.json
├── phpunit.xml
└── .env.example
```

## Arquitectura

| Capa | Responsabilidad |
|---|---|
| Controller | Recibe la petición, delega en el servicio y devuelve la respuesta HTTP |
| FormRequest | Valida la entrada antes de llegar al controlador |
| Service | Contiene la lógica de negocio |
| Repository | Ejecuta las consultas Eloquent |
| Resource | Define el formato JSON de salida |

El servicio depende de `MenuItemRepositoryInterface`, no de la implementación concreta. El binding se registra en `AppServiceProvider`.

## Endpoints

Base URL: `http://localhost:8000/api`

| Método | Endpoint | Descripción | Éxito |
|---|---|---|---|
| GET | `/api` | Índice de la API | 200 |
| GET | `/api/menu-items` | Listar platillos (paginado) | 200 |
| GET | `/api/menu-items/{id}` | Obtener un platillo | 200 |
| POST | `/api/menu-items` | Crear un platillo | 201 |
| PUT | `/api/menu-items/{id}` | Reemplazar un platillo | 200 |
| PATCH | `/api/menu-items/{id}` | Actualizar parcialmente un platillo | 200 |
| DELETE | `/api/menu-items/{id}` | Eliminar un platillo | 204 |
| GET | `/api/menu-items/category/{category}` | Filtrar por categoría | 200 |

`{category}` acepta el **ID**, el **slug** o el **nombre visible** de la categoría:

```
/api/menu-items/category/1
/api/menu-items/category/postres
/api/menu-items/category/Postres
```

`PUT` exige `category_id`, `name` y `price`. `PATCH` permite enviar solo los campos que se quieren cambiar.

### Parámetros de consulta

| Parámetro | Tipo | Descripción |
|---|---|---|
| `available` | boolean (`1`/`0`) | Filtra por disponibilidad |
| `search` | string | Busca en nombre y descripción |
| `per_page` | integer (1–100, default 15) | Cantidad de resultados por página |
| `page` | integer | Número de página |

### Campos y validaciones

| Campo | Tipo | Reglas al crear |
|---|---|---|
| `category_id` | integer | Requerido, debe existir |
| `name` | string | Requerido, máximo 120, único dentro de la categoría |
| `description` | string | Opcional, máximo 1000 |
| `price` | number | Requerido, entre 0 y 99999.99 |
| `is_available` | boolean | Opcional, por defecto `true` |
| `image_url` | string | Opcional, debe ser una URL válida |
| `preparation_time` | integer | Opcional, entre 1 y 240 minutos |

## Ejemplos de uso, usando Postman

> **Postman** 

### Listar platillos

```bash
curl -H "Accept: application/json" http://localhost:8000/api/menu-items
```

### Obtener un platillo

```bash
curl -H "Accept: application/json" http://localhost:8000/api/menu-items/1
```

### Filtrar por categoría

```bash
curl -H "Accept: application/json" http://localhost:8000/api/menu-items/category/postres
```

### Buscar y filtrar disponibles

```bash
curl -H "Accept: application/json" "http://localhost:8000/api/menu-items?available=1&search=atun&per_page=5"
```

### Crear un platillo

```bash
curl -X POST http://localhost:8000/api/menu-items \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"category_id":1,"name":"Ceviche nikkei","description":"Pescado del día con ají amarillo y soya","price":12.50,"preparation_time":10}'
```

Respuesta `201`:

```json
{
  "data": {
    "id": 11,
    "name": "Ceviche nikkei",
    "description": "Pescado del día con ají amarillo y soya",
    "price": 12.5,
    "is_available": true,
    "image_url": null,
    "preparation_time": 10,
    "category": {
      "id": 1,
      "name": "Entradas",
      "slug": "entradas"
    },
    "created_at": "2026-11-18T10:00:00+00:00",
    "updated_at": "2026-11-18T10:00:00+00:00"
  }
}
```

### Actualizar el precio

```bash
curl -X PATCH http://localhost:8000/api/menu-items/11 \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"price": 13.50}'
```

### Marcar como agotado

```bash
curl -X PATCH http://localhost:8000/api/menu-items/11 \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"is_available": false}'
```

### Reemplazar un platillo

```bash
curl -X PUT http://localhost:8000/api/menu-items/11 \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"category_id":1,"name":"Ceviche nikkei clásico","description":"Actualizado con PUT","price":13.50}'
```

### Eliminar un platillo

```bash
curl -X DELETE http://localhost:8000/api/menu-items/11 \
  -H "Accept: application/json"
```

### Error de validación (`422`)

```json
{
  "message": "La categoría seleccionada no existe.",
  "errors": {
    "category_id": [
      "La categoría seleccionada no existe."
    ]
  }
}
```

### Recurso no encontrado (`404`)

```json
{
  "message": "Recurso no encontrado."
}
```

## Pruebas automáticas

```bash
php artisan test
```

Las pruebas usan **SQLite en memoria** y no modifican la base de datos de desarrollo. Actualmente cubren CRUD, validación, respuestas `404`, filtros y consulta por categoría usando ID y nombre.

Resultado esperado:

```text
Tests: 14 passed (38 assertions)
```

## Pruebas manuales con Postman

Esta sección sirve como guía para probar la API y generar capturas de evidencia.

### Organización sugerida de la colección

```
La Buena Mesa API
├── 01 - Consultas (GET)
│   ├── Índice API
│   ├── Listar menú
│   ├── Obtener por ID
│   ├── Filtrar por categoría
│   └── Buscar y filtrar disponibles
├── 02 - CRUD
│   ├── Crear platillo
│   ├── Reemplazar (PUT)
│   ├── Actualizar parcial (PATCH)
│   └── Eliminar
└── 03 - Errores
    ├── Validación 422
    └── No encontrado 404
```

### Secuencia recomendada para las evidencias

Si no tienes datos propios, puedes partir de una base limpia. **Esto elimina los datos actuales:**

```bash
php artisan migrate:fresh --seed
```

Después ejecuta las peticiones en este orden:

| # | Captura | Método y URL | Qué debe mostrar |
|---|---|---|---|
| 1 | API activa | `GET /api` | `200` y el índice de endpoints |
| 2 | Listar menú | `GET /api/menu-items` | `200`, 10 platillos y `meta` de paginación |
| 3 | Detalle | `GET /api/menu-items/1` | `200` y el platillo con su categoría |
| 4 | Filtrar por categoría | `GET /api/menu-items/category/postres` | `200` y solo platillos de Postres |
| 5 | Filtros | `GET /api/menu-items?available=1&search=atun&per_page=5` | `200`, parámetros aplicados y `meta` |
| 6 | Crear | `POST /api/menu-items` | Body JSON y `201` con el `id` creado |
| 7 | Ver el creado | `GET /api/menu-items/11` | `200` y los datos del platillo creado |
| 8 | Reemplazar | `PUT /api/menu-items/11` | Body completo y `200` con los datos actualizados |
| 9 | Actualización parcial | `PATCH /api/menu-items/11` | Body parcial y `200` |
| 10 | Filtro de disponibilidad | `GET /api/menu-items?available=1` | El platillo marcado como agotado no aparece |
| 11 | Eliminar | `DELETE /api/menu-items/11` | `204 No Content` |
| 12 | No encontrado | `GET /api/menu-items/11` | `404` con `{"message":"Recurso no encontrado."}` |
| 13 | Validación | `POST /api/menu-items` con datos inválidos | `422` con errores por campo |

El ID `11` corresponde al platillo creado después de un `migrate:fresh --seed`. Si tu base ya tenía registros, usa el ID que devuelva el `POST`.

### Bodies para las capturas

**POST — crear:**

```json
{
  "category_id": 1,
  "name": "Ceviche nikkei de evidencia",
  "description": "Pescado del día con ají amarillo y soya",
  "price": 12.50,
  "is_available": true,
  "preparation_time": 10
}
```

**PUT — reemplazar:**

```json
{
  "category_id": 1,
  "name": "Ceviche nikkei clásico",
  "description": "Actualizado con PUT",
  "price": 13.50
}
```

**PATCH — actualización parcial:**

```json
{
  "is_available": false
}
```

**POST inválido — para el `422`:**

```json
{
  "category_id": 999,
  "name": "",
  "price": -5
}
```

### Tests en Postman

En la pestaña **Tests** del request puedes agregar aserciones. Para el `POST`:

```javascript
pm.test("Status 201", () => pm.response.to.have.status(201));

const json = pm.response.json();

pm.test("Devuelve el platillo creado", () => {
    pm.expect(json.data.name).to.eql("Ceviche nikkei de evidencia");
    pm.expect(json.data.id).to.be.a("number");
});

pm.collectionVariables.set("menuItemId", json.data.id);
```

Para el `422`:

```javascript
pm.test("Status 422", () => pm.response.to.have.status(422));

const json = pm.response.json();

pm.test("Reporta errores por campo", () => {
    pm.expect(json.errors).to.have.property("category_id");
    pm.expect(json.errors).to.have.property("name");
    pm.expect(json.errors).to.have.property("price");
});
```

Para `DELETE` usa `pm.response.to.have.status(204)` y para el `404` usa `pm.response.to.have.status(404)`.

### Consejos para las capturas

- Muestra el nombre del request, el método, la URL, el código de estado y el body de respuesta.
- En `POST`, `PUT` y `PATCH`, incluye también el body enviado. Puedes tomar dos capturas si no cabe todo.
- Usa **Save Response → Save as Example** para guardar la respuesta dentro de la colección.
- Exporta la colección desde el menú **... → Export** y úsala como anexo.
- Si Postman devuelve los tres campos como requeridos, el body llegó vacío o con JSON inválido. Revisa que sea **un solo objeto**, con comillas dobles, y que **Body** esté en **raw → JSON**. Dos objetos pegados (`{...}{...}`) producen exactamente ese error.

### Evidencias adicionales

- Captura de `php artisan test` con las pruebas en verde.
- Captura de `php artisan route:list --path=api` con las rutas registradas.
- Captura de phpMyAdmin con las tablas `categories` y `menu_items`, sus filas y la relación entre ambas.
- Captura del repositorio en GitHub y del README.

## Solución de problemas

| Síntoma | Causa probable | Solución |
|---|---|---|
| `422` con `category_id`, `name` y `price` requeridos | El body está vacío o el JSON es inválido | Usa `Body → raw → JSON`, un solo objeto y comillas dobles |
| `422` con `Ya existe un platillo...` | El nombre ya existe en esa categoría | Cambia el nombre o elimina el registro |
| `500` con `Access denied` | MySQL no está activo o las credenciales no coinciden | Verifica `DB_*` en `.env` y la conexión con `php artisan db:show` |
| `404` al consultar un ID | El ID no existe o la URL está mal | Consulta primero `GET /api/menu-items` y usa un ID real |
| `curl` no acepta `-X` o `-d` en PowerShell | `curl` es un alias de `Invoke-WebRequest` | Usa `curl.exe` |
| Los IDs no coinciden con los ejemplos | La base ya tenía registros o pruebas anteriores | Ejecuta `php artisan migrate:fresh --seed` |

## Escalabilidad

- **Multi-sucursal:** agregar una tabla `branches` y una relación con `menu_items` para manejar precios y disponibilidad por sucursal.
- **Autenticación:** proteger las operaciones de escritura con Laravel Sanctum.
- **Rendimiento:** cachear listados y versionar la API (`/api/v1`).
- **Tiempo real:** emitir eventos de broadcasting cuando cambien precios o disponibilidad.

---

Proyecto desarrollado con fines académicos.
