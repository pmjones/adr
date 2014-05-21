<?php
namespace Blog\Responder;

class BlogEditResponder extends AbstractBlogResponder
{
    public function __invoke()
    {
        $responded = $this->notFound('blog')
                  || $this->responseView('edit');

        if ($responded) {
            return $this->response;
        }
    }
}
