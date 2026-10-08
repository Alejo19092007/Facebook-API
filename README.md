# Facebook API - Taller Backend

API REST simplificada inspirada en Facebook, construida con Laravel. Permite crear publicaciones con múltiples imágenes, comentarlas y reaccionar con "Me gusta".

Proyecto desarrollado para el curso **Arquitectura y Desarrollo Backend** - Universidad Autónoma de Bucaramanga.

## Requisitos

- PHP 8.1+
- Composer 2.x
- SQLite (por defecto) / MySQL / PostgreSQL

## Instalación

```bash
composer install
cp .env.example .env
php artisan key:generate

# Base de datos (SQLite por defecto)
touch database/database.sqlite

php artisan migrate
php artisan storage:link
php artisan serve
```

La API quedará disponible en `http://127.0.0.1:8000`.

## Endpoints

| Método | Ruta                      | Descripción                          |
|--------|---------------------------|---------------------------------------|
| POST   | `/api/posts`               | Crear publicación con imágenes       |
| GET    | `/api/posts`                | Listar publicaciones                 |
| GET    | `/api/posts/{id}`           | Ver un post individual               |
| DELETE | `/api/posts/{id}`           | Eliminar un post                     |
| POST   | `/api/posts/{id}/like`      | Incrementar "Me gusta"               |
| POST   | `/api/posts/{id}/comments`  | Agregar comentario                   |

## Modelo de datos

- **Post**: `title`, `content`, `likes_count`
- **PostImage**: pertenece a un Post (`post_id`, `image_path`)
- **Comment**: pertenece a un Post (`post_id`, `author`, `content`)

## Pruebas

La colección de Postman con todas las peticiones de prueba se encuentra en [`facebook-api-postman-collection.json`](facebook-api-postman-collection.json).

## Autor

**Docente:** Fabian Enrique Suarez Carvajal
