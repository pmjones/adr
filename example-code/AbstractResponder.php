<?php
use Aura\View\View;
use Aura\Web\Response;

abstract class AbstractResponder
{
    public $data;

    protected $response;

    protected $view;

    public function __construct(Response $response, View $view)
    {
        $this->response = $response;
        $this->view = $view;
        $this->data = (object) array();
    }

    public function __get($key)
    {
        return isset($this->data->$key) ? $this->data->{$key} : '';
    }

    public function __set($key, $val)
    {
        $this->data->$key = $val;
    }

    public function __isset($key)
    {
        return isset($this->data->$key);
    }

    public function __unset($key)
    {
        unset($this->data->$key);
    }

    abstract public function __invoke();

    protected function responseView($view)
    {
        $this->view->setView($view);
        $this->view->setData($this->data);
        $this->response->content->set($this->view->__invoke());
        return $this->response;
    }
}
