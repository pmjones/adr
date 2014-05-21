<?php
namespace Blog\Responder;

class BlogBrowseResponder extends AbstractBlogResponder
{
    public function __invoke()
    {
        $responded = $this->notFound('collection')
                  || $this->responseView('browse');

        if ($responded) {
            return $this->response;
        }
    }
}
