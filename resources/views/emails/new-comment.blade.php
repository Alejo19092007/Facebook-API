<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
    <h2>Nuevo comentario en tu publicación</h2>
    <p><strong>{{ $comment->author }}</strong> comentó en tu publicación "{{ $comment->post->title }}":</p>
    <blockquote>{{ $comment->content }}</blockquote>
</body>
</html>
