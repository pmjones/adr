<?php
namespace Blog\Responder;

class BlogReadResponder extends AbstractBlogResponder
{
    public function __invoke()
    {
        return $this->notFound('collection')
            || $this->responseView('read');
    }
}
