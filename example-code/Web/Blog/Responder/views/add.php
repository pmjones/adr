<h3>Add New Blog Post</h3>

<?= $this->render(
    '_form',
    'POST',
    '/blog/add',
    'Create',
    $this->blog
); ?>
