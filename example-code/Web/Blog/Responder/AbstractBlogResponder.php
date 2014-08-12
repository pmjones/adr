<?php
namespace Blog\Responder;

use AbstractResponder;

abstract class AbstractBlogResponder extends AbstractResponder
{
    protected function init()
    {
        $view_names = array(
            'browse' => array(),
            'browse.json' => array(),
            'read' => array(),
            'read.json' => array(),
            'edit' => array(),
            'add' => array(),
            'delete-failure' => array(),
            'delete-success' => array(),
            '_form' => 'blog',
            '_intro',
        );

        $view_registry = $this->view->getViewRegistry();
        foreach ($view_names as $view_name => $view_vars) {
            $view_registry->set(
                $view_name,
                __DIR__ . "/views/{$view_name}.php",
                $view_vars
            );
        }
    }

    protected function notFound($result)
    {
        $this->response->status->set(404);
    }

}
