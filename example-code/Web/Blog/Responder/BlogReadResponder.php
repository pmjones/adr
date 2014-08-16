<?php
namespace Blog\Responder;

class BlogReadResponder extends AbstractBlogResponder
{
    protected $available = array(
        'text/html' => '',
        'application/json' => '.json'
    );

    protected $result_method = array(
        'Domain\Result\Found' => 'found',
        'Domain\Result\NotFound' => 'notFound',
    );

    protected function found()
    {
        if ($$this->negotiateMediaType()) {
            $this->renderView('read');
        }
    }
}
