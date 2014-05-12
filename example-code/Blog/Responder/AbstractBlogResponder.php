<?php
namespace Blog\Responder;

use AbstractResponder;

abstract class AbstractBlogResponder extends AbstractResponder
{
    protected function init()
    {
        $view_names = array(
            'browse',
            'read',
            'edit',
            'add',
            'delete-failure',
            'delete-success',
            '_form',
            '_intro',
        );

        $view_registry = $this->view->getViewRegistry();
        foreach ($view_names as $view_name) {
            $view_registry->set(
                $view_name,
                __DIR__ . "/views/{$view_name}.php"
            );
        }
    }

    protected function notFound($key)
    {
        if (! $this->data->$key) {
            $this->response->status->set(404);
            return $this->response;
        }
    }
}
