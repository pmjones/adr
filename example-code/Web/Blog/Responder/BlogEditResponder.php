<?php
namespace Blog\Responder;

class BlogEditResponder extends AbstractBlogResponder
{
    protected $payload_method = array(
        'Domain\Payload\Found' => 'found',
        'Domain\Payload\NotFound' => 'notFound',
    );

    public function found()
    {
        return $this->renderView('edit');
    }
}
