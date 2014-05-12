<?php foreach ($this->collection as $blog) {
    echo $this->render('_intro', array('blog' => $blog));
} ?>
