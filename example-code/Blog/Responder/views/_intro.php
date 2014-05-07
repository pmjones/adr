<?php use Aura\Html\Functions; ?>

<div class="blog-intro">
    <h2><?= h($blog->title); ?></h2>
    <p class="byline"><?= h($blog->author); ?></p>
    <?= $blog->intro; ?>
    <p><?= $this->anchor("/blog/read/{$blog->id}", 'Read More ...'); ?></p>
</div>
