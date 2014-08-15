<?php
namespace Blog\Responder;

use AbstractResponder;

abstract class AbstractBlogResponder extends AbstractResponder
{
    protected function init()
    {
        parent::init();

        $name_vars = array(
            'browse' => array(),
            'browse.json' => array(),
            'read' => array(),
            'read.json' => array(),
            'edit' => array(),
            'add' => array(),
            'delete-failure' => array(),
            'delete-success' => array(),
            '_form' => array('method', 'action', 'submit', 'blog'),
            '_intro' => array('blog'),
        );

        $view_registry = $this->view->getViewRegistry();
        foreach ($name_vars as $name => $vars) {
            $view_registry->set(
                $name,
                __DIR__ . "/views/{$name}.php",
                $vars
            );
        }
    }
}
