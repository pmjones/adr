<h3>Edit Blog Post</h3>

<?= $this->render(
    '_form',
    array(
        'method' => 'POST',
        'action' => '/blog/update/' . $this->blog->id,
        'submit' => 'Update',
        'blog' => $this->blog
    )
); ?>
