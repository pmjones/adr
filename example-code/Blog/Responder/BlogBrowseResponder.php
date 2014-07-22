<?php
namespace Blog\Responder;

class BlogBrowseResponder extends AbstractBlogResponder
{
    protected $available = array(
        'text/html' => '',
        'application/json' => '.json'
    );

    public function __invoke()
    {
        $responded = $this->notFound('collection')
                  || $this->notAcceptable()
                  || $this->responseView('browse');

        if ($responded) {
            return $this->response;
        }
    }
}
