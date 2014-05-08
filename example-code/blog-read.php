<?php
require __DIR__ . '/bootstrap.php';
$blog_service = new Blog\Domain\BlogService();
$blog_read_responder = new Blog\Responder\BlogReadResponder(
    $response,
    $view
);

$blog_read_action = new Blog\Action\BlogReadAction(
    $request,
    $blog_service,
    $blog_read_responder
);
$id = 1;
$blog_read_action($id);
echo $response->content->get();
