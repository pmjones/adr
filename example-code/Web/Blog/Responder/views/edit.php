<h3>Edit Blog Post</h3>

<?= $this->ul()->items($this->blog->getMessages())->get(); ?>

<?= $this->render('_form', array(
    '_method' => 'PATCH',
    '_action' => '/blog/edit',
    '_submit' => 'Update',
    '_blog' => $this->blog,
)); ?>
