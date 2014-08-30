<h3>Add New Blog Post</h3>

<?= $this->render('_form', array(
    'method' => 'POST',
    'action' => '/blog/add',
    'submit' => 'Create',
)); ?>
