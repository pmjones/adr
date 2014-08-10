<?php
namespace Blog\Responder;

use Domain\Status;

class BlogDeleteResponder extends AbstractBlogResponder
{
    public function __invoke()
    {
        if ($this->isFound('status') && $this->isDeleted()) {
            $this->response->setStatus(200);
            $this->renderView('delete-success');
        }

        return $this->response;
    }

    protected function isDeleted()
    {
        if ($this->data->status instanceof Status\Deleted) {
            return true;
        }

        $this->response->setStatus(500);
        $this->renderView('delete-failure');
        return false;
    }
}
