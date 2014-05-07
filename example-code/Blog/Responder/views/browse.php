<?php
foreach ($this->collection as $blog) {
    $this->render('_intro', array('blog' => $blog));
}
