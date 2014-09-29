<?php
namespace Blog\Responder;

class BlogAddResponder extends AbstractBlogResponder
{
    protected $payload_method = array(
        'Domain\Payload\NewEntity' => 'display',
    );

    protected function display()
    {
        $this->renderView('add');
    }
}
