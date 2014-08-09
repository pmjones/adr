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
        if ($this->isFound('blog') && $this->isAcceptable()) {
            $this->renderView('read');
        }

        return $this->response;
    }
}
