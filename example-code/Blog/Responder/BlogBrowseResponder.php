<?php
namespace Blog\Responder;

class BlogBrowseResponder extends AbstractBlogResponder
{
    public function __invoke()
    {
        return $this->notFound('collection')
            || $this->responseView('browse');
    }
}
