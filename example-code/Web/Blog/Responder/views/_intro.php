<?php use Aura\Html\Escaper as e; ?>

<div class="blog-intro">
    <h2><?= e::h($this->_blog->title) ?></h2>
    <p class="byline"><?= e::h($this->_blog->author) ?></p>
    <?= e::h($this->_blog->intro) ?>
    <p><?= $this->a("/blog/read/{$this->_blog->id}", 'Read More ...'); ?></p>
</div>
