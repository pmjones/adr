<h3>Edit Blog Post</h3>

<?= $this->ul()->items($this->blog->getMessages())->get(); ?>

<?= $this->render(
    '_form',
    'PATCH',
    '/blog/edit',
    'Update',
    $this->blog
); ?>
