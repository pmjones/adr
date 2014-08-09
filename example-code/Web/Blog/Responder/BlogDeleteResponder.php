<?php
namespace Blog\Responder;

use Domain\Status;

class BlogDeleteResponder extends AbstractBlogResponder
{
    public function __invoke()
    {
        if (! $this->isFound('status') || ! $this->deleted()) {
            $this->response->setStatus(500);
            $this->renderView('delete-failure');
        }

        return $this->response;
    }

    protected function deleted()
    {
        if ($this->data->status instanceof Status\Deleted) {
            $this->response->setStatus(200);
            $this->renderView('delete-success');
            return true;
        }

        return false;
    }
}
