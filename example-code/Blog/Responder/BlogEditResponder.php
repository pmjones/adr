<?php
namespace Blog\Responder;

class BlogEditResponder extends AbstractBlogResponder
{
    public function __invoke()
    {
        if ($this->isFound('blog')) {
            $this->renderView('edit');
        }

        return $this->response;
    }
}
