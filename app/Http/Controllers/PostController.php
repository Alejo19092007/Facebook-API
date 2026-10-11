<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * @group Publicaciones
 *
 * Endpoints para crear, listar, buscar, comentar y reaccionar a publicaciones.
 */
class PostController extends Controller
{
    /**
     * Listar publicaciones
     *
     * Devuelve las publicaciones paginadas (10 por página), con sus imágenes y comentarios.
     * Acepta el parámetro opcional `search` para filtrar por título o contenido.
     *
     * @queryParam page integer Número de página a consultar. Example: 1
     * @queryParam search string Texto a buscar en título o contenido. Example: viaje
     */
    public function index(Request $request)
    {
        $posts = Post::with(['images', 'comments'])
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->through(function ($post) {
                $post->images->transform(function ($img) {
                    $img->image_url = asset('storage/' . $img->image_path);
                    return $img;
                });
                return $post;
            });

        return response()->json($posts, 200);
    }

    /**
     * Ver un post individual
     *
     * Devuelve un post específico con sus imágenes y comentarios.
     *
     * @urlParam id integer required El id del post. Example: 1
     */
    public function show($id)
    {
        $post = Post::with(['images', 'comments'])->findOrFail($id);
        $post->images->transform(function ($img) {
            $img->image_url = asset('storage/' . $img->image_path);
            return $img;
        });

        return response()->json($post, 200);
    }

    /**
     * Crear publicación
     *
     * Crea un post con título, contenido y una o más imágenes (opcional). Requiere autenticación.
     *
     * @authenticated
     * @bodyParam title string required Título de la publicación. Example: Mi primera publicación
     * @bodyParam content string required Contenido de la publicación. Example: Hola a todos
     * @bodyParam images[] file Imágenes a adjuntar (jpeg, png, jpg, gif, máx 2MB c/u).
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'    => 'required|string|max:255',
            'content'  => 'required|string',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $post = Post::create([
            'user_id' => $request->user()->id,
            'title'   => $request->title,
            'content' => $request->content,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('posts', 'public');
                PostImage::create([
                    'post_id'    => $post->id,
                    'image_path' => $path,
                ]);
            }
        }

        return response()->json($post->load('images'), 201);
    }

    /**
     * Dar "Me gusta"
     *
     * Registra un like del usuario autenticado sobre el post. Cada usuario solo puede
     * dar like una vez por publicación.
     *
     * @authenticated
     * @urlParam id integer required El id del post. Example: 1
     */
    public function like(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        $user = $request->user();

        if ($post->likedByUsers()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'message' => 'Ya diste like a esta publicacion',
            ], 409);
        }

        $post->likedByUsers()->attach($user->id);

        return response()->json([
            'message'     => 'Me gusta añadido correctamente',
            'likes_count' => $post->likedByUsers()->count(),
        ], 200);
    }

    /**
     * Eliminar publicación
     *
     * Elimina un post y sus imágenes asociadas (en disco y en base de datos). Requiere autenticación.
     *
     * @authenticated
     * @urlParam id integer required El id del post. Example: 1
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        foreach ($post->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $post->delete();
        return response()->json(null, 204);
    }
}
