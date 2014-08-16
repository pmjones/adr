<h3>Edit Blog Post</h3>

<?= $this->ul()->items($this->blog->getMessages())->get(); ?>

<?= $this->render(
    '_form',
    array(
        'method' => 'PATCH',
        'action' => '/blog/edit',
        'submit' => 'Update',
        'blog' => $this->blog
    )
); ?>
