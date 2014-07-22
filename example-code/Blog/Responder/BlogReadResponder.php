<?php
namespace Blog\Responder;

class BlogReadResponder extends AbstractBlogResponder
{
    protected $available = array(
        'text/html' => '',
        'application/json' => '.json'
    );

    public function __invoke()
    {
        $responded = $this->notFound('blog')
                  || $this->notAcceptable()
                  || $this->responseView('read');

        if ($responded) {
            return $this->response;
        }
    }
}
