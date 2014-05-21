<?php
namespace Blog\Responder;

class BlogReadResponder extends AbstractBlogResponder
{
    public function __invoke()
    {
        $responded = $this->notFound('blog')
                  || $this->responseView('read');
                  
        if ($responded) {
            return $this->response;
        }
    }
}
