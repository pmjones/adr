<?php
namespace Blog\Responder;

class BlogEditResponder extends AbstractBlogResponder
{
    public function __invoke()
    {
        return $this->notFound('blog')
            || $this->responseView('edit');
    }
}
