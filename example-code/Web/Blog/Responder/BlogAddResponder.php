<?php
namespace Blog\Responder;

class BlogAddResponder extends AbstractBlogResponder
{
    protected $result_method = array(
        'Domain\Result\NewEntity' => 'display',
    );

    protected function display()
    {
        $this->renderView('add');
    }
}
