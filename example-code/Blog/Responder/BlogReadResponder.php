<?php
namespace Blog\Responder;

class BlogReadResponder extends AbstractBlogResponder
{
    public function __invoke()
    {
        $view_registry = $this->view->getViewRegistry();
        $view_registry->set('read', __DIR__ . '/views/read.php');
        return $this->notFound('blog')
            || $this->responseView('read');
    }
}
