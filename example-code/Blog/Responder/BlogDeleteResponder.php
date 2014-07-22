<?php
namespace Blog\Responder;

class BlogDeleteResponder extends AbstractBlogResponder
{
    public function __invoke()
    {
        if (! $this->isFound('blog') || ! $this->deleted()) {
            $this->response->setStatus(500);
            $this->renderView('delete-failure');
        }

        return $this->response;
    }

    protected function deleted()
    {
        if ($this->data->success) {
            $this->response->setStatus(200);
            $this->renderView('delete-success');
            return true;
        }

        return false;
    }
}
