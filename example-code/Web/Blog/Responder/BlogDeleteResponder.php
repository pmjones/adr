<?php
namespace Blog\Responder;

class BlogDeleteResponder extends AbstractBlogResponder
{
    protected $payload_method = array(
        'Domain\Payload\NotFound' => 'notFound',
        'Domain\Payload\Deleted' => 'deleted',
        'Domain\Payload\NotDeleted' => 'notDeleted',
    );

    protected function deleted()
    {
        $this->renderView('delete-success');
    }

    protected function notDeleted()
    {
        $this->response->setStatus(500);
        $this->renderView('delete-failure');
    }
}
