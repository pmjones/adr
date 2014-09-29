<?php
namespace Blog\Responder;

class BlogUpdateResponder extends AbstractBlogResponder
{
    protected $payload_method = array(
        'Domain\Payload\NotFound' => 'notFound',
        'Domain\Payload\NotValid' => 'notValid',
        'Domain\Payload\Updated' => 'updated',
        'Domain\Payload\NotUpdated' => 'notUpdated',
    );

    protected function notValid()
    {
        $this->response->setStatus('422');
        return $this->renderView('edit');
    }

    protected function updated()
    {
        return $this->renderView('edit');
    }

    protected function notUpdated()
    {
        $this->response->setStatus('500');
        return $this->renderView('edit');
    }
}
