<?php

namespace App\Http\Controllers;

use App\Mail\NewCommentMail;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

/**
 * @group Comentarios
 *
 * Endpoints para listar y crear comentarios en una publicación.
 */
class CommentController extends Controller
{
    /**
     * Listar comentarios
     *
     * Devuelve los comentarios de un post, del más reciente al más antiguo.
     *
     * @urlParam postId integer required El id del post. Example: 1
     */
    public function index($postId)
    {
        $post = Post::findOrFail($postId);
        return response()->json($post->comments()->latest()->get(), 200);
    }

    /**
     * Agregar comentario
     *
     * Crea un comentario asociado al post y al usuario autenticado. Si el post
     * tiene un dueño, se le envía una notificación por correo. Requiere autenticación.
     *
     * @authenticated
     * @urlParam postId integer required El id del post. Example: 1
     * @bodyParam author string required Nombre del autor del comentario. Example: Carlos Gómez
     * @bodyParam content string required Contenido del comentario. Example: ¡Qué buenas fotos!
     */
    public function store(Request $request, $postId)
    {
        $request->validate([
            'author'  => 'required|string|max:100',
            'content' => 'required|string',
        ]);

        $post = Post::findOrFail($postId);

        $comment = $post->comments()->create([
            'user_id' => $request->user()->id,
            'author'  => $request->author,
            'content' => $request->content,
        ]);

        if ($post->user) {
            Mail::to($post->user->email)->send(new NewCommentMail($comment));
        }

        return response()->json($comment, 201);
    }
}
