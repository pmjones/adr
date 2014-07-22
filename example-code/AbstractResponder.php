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
        Accept $accept,
        Response $response,
        View $view
    ) {
        $this->accept = $accept;
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

    protected function responseView($view)
    {
        $content_type = $this->response->content->getType();
        if ($content_type) {
            $view .= $this->available[$content_type];
        }

        $this->view->setView($view);
        $this->view->setData($this->data);
        $this->response->content->set($this->view->__invoke());
        return $this->response;
    }

    protected function notAcceptable()
    {
        // nothing available? ignore negotiation.
        if (! $this->available) {
            return false;
        }

        // negotiate a media type.
        $media = $this->accept->media->negotiate(
            $this->data->acceptable,
            array_keys($this->available)
        );

        // negotiation failure?
        if (! $media) {
            $this->response->status->set(406);
            return true;
        }

        // negotiation success.
        $this->response->content->setType($media->available->getValue());
        return false;
    }
}
