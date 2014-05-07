<?php
namespace Blog\Responder;

class BlogDeleteResponder extends AbstractBlogResponder
{
    public function __invoke()
    {
        return $this->notFound('blog')
            || $this->deleteSuccess()
            || $this->deleteFailure();
    }

    protected function deleteSuccess()
    {
        if ($this->data->success) {
            $this->response->setStatus(200);
            return $this->responseView('delete-success');
        }
    }

    protected function deleteFailure()
    {
        $this->response->setStatus(500);
        return $this->responseView('delete-failure');
    }
}
