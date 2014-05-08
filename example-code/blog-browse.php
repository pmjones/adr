<?php
require __DIR__ . "/bootstrap.php";

$blog_service = new Blog\Domain\BlogService();
$blog_browse_responder = new Blog\Responder\BlogBrowseResponder(
    $response,
    $view
);

$blog_browse_action = new Blog\Action\BlogBrowseAction(
    $request,
    $blog_service,
    $blog_browse_responder
);
$id = 1;
$blog_browse_action();
echo $response->content->get();
