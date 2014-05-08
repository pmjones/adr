<?php
namespace Blog\Responder;

class BlogBrowseResponder extends AbstractBlogResponder
{
    public function __invoke()
    {
        $view_registry = $this->view->getViewRegistry();
        $view_registry->set('browse', __DIR__ . '/views/browse.php');
        $view_registry->set('_intro', __DIR__ . '/views/_intro.php');
        /*
        $this->data->items = array(
            array(
                'name' => 'Something',
                'cost' => 23
            ),
            array(
                'name' => 'Many',
                'cost' => 23
            )
        );
        $view_registry->set('item_rows', function () {
            foreach ($this->items as $item) {
                echo $this->render('item_row', array('item' => $item));
            }
        });
        $view_registry->set('item_row', function () {
            echo $item['name']  . ' costs ' . $item['price'] . PHP_EOL;
        });
         */
        return $this->notFound('collection')
            || $this->responseView('browse');
    }
}
