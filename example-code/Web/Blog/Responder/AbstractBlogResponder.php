<?php
namespace Blog\Responder;

use AbstractResponder;

abstract class AbstractBlogResponder extends AbstractResponder
{
    protected function init()
    {
        parent::init();

        $view_names = array(
            'browse',
            'browse.json',
            'read',
            'read.json',
            'edit',
            'add',
            'delete-failure',
            'delete-success',
            '_form',
            '_intro',
        );

        $view_registry = $this->view->getViewRegistry();
        foreach ($view_names as $name) {
            $view_registry->set(
                $name,
                __DIR__ . "/views/{$name}.php"
            );
        }
    }
}
