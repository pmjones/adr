<?php
use Aura\View\View;
use Aura\Web\Response;
use Aura\Web\Request\Accept;

abstract class AbstractResponder
{
    protected $data;

    protected $accept;

    protected $available = array();

    protected $response;

    protected $view;

    public function __construct(
        Response $response,
        View $view
    ) {
        $this->response = $response;
        $this->view = $view;
        $this->data = (object) array();
        $this->init();
    }

    protected function init()
    {
        // empty by default
    }

    public function __get($key)
    {
        return $this->data->$key;
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

    public function setAccept(Accept $accept)
    {
        $this->accept = $accept;
    }

    protected function isFound($key)
    {
        if (! $this->data->$key) {
            $this->response->status->set(404);
            return true;
        }

        return false;
    }

    protected function isAcceptable()
    {
        if (! $this->available || ! $this->accept) {
            return true;
        }

        $media = $this->accept->media->negotiate(array_keys($this->available));
        if (! $media) {
            $this->response->status->set(406);
            return false;
        }

        $this->response->content->setType($media->available->getValue());
        return true;
    }

    protected function renderView($view)
    {
        $content_type = $this->response->content->getType();
        if ($content_type) {
            $view .= $this->available[$content_type];
        }

        $this->view->setView($view);
        $this->view->setData($this->data);
        $this->response->content->set($this->view->__invoke());
    }
}
