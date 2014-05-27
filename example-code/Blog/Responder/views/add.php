<h3>Add New Blog Post</h3>

<?= $this->render('_form', array(
    '_method' => 'POST',
    '_action' => '/blog/add',
    '_submit' => 'Create',
    '_blog' => $this->blog,
)); ?>
