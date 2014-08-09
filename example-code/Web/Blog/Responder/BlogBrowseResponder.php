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
        if ($this->isFound('collection') && $this->isAcceptable()) {
            $this->renderView('browse');
        }

        return $this->response;
    }
}
