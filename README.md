# Facebook API - Taller Backend

API REST simplificada inspirada en Facebook, construida con Laravel. Permite crear publicaciones con múltiples imágenes, comentarlas y reaccionar con "Me gusta". Incluye autenticación con tokens, paginación, búsqueda, likes por usuario, notificaciones por correo y versionado de rutas.

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

La API quedará disponible en `http://127.0.0.1:8000`. Todas las rutas están bajo el prefijo `/api/v1`.

## Autenticación

La API usa **Laravel Sanctum** (tokens Bearer). Primero regístrate o inicia sesión para obtener un token, y envíalo en el header `Authorization: Bearer {token}` en las rutas protegidas.

| Método | Ruta                 | Descripción              | Protegida |
|--------|----------------------|---------------------------|-----------|
| POST   | `/api/v1/register`   | Registrar usuario nuevo   | No        |
| POST   | `/api/v1/login`      | Iniciar sesión            | No        |
| POST   | `/api/v1/logout`     | Cerrar sesión              | Sí        |

## Endpoints de publicaciones

| Método | Ruta                          | Descripción                              | Protegida |
|--------|-------------------------------|-------------------------------------------|-----------|
| POST   | `/api/v1/posts`               | Crear publicación con imágenes            | Sí        |
| GET    | `/api/v1/posts`                | Listar publicaciones (paginado, 10/pág.) | No        |
| GET    | `/api/v1/posts?search=texto`   | Buscar por título o contenido             | No        |
| GET    | `/api/v1/posts/{id}`           | Ver un post individual                    | No        |
| DELETE | `/api/v1/posts/{id}`           | Eliminar un post                          | Sí        |
| POST   | `/api/v1/posts/{id}/like`      | Dar "Me gusta" (una vez por usuario)      | Sí        |
| POST   | `/api/v1/posts/{id}/comments`  | Agregar comentario                        | Sí        |

Al comentar una publicación, si el post tiene un dueño autenticado, se envía una notificación por correo (driver `log`, se guarda en `storage/logs/laravel.log` para pruebas).

## Modelo de datos

- **User**: `name`, `email`, `password`
- **Post**: `user_id`, `title`, `content`, `likes_count` — pertenece a un User, tiene muchas PostImage y Comment
- **PostImage**: pertenece a un Post (`post_id`, `image_path`)
- **Comment**: pertenece a un Post y a un User (`post_id`, `user_id`, `author`, `content`)
- **post_user** (pivote): relación muchos-a-muchos entre Post y User para los likes, con restricción única por par post/usuario

## Documentación interactiva

Con el servidor corriendo, la documentación generada automáticamente con **Scribe** está disponible en:
```
http://127.0.0.1:8000/docs
```
Incluye todos los endpoints, parámetros, ejemplos de petición/respuesta y un botón "Try it out" para probarlos desde el navegador. Se regenera con `php artisan scribe:generate`.

## Pruebas

La colección de Postman con todas las peticiones de prueba se encuentra en [`facebook-api-postman-collection.json`](facebook-api-postman-collection.json).

## Autor

**Docente:** Fabian Enrique Suarez Carvajal
