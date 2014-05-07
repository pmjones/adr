<?php use Aura\Html\Functions; ?>

<h2><?= h($this->blog->title); ?></h2>
<p class="byline"><?= h($this->blog->author); ?>;

<div class="blog-body">
<?= $this->blog->body; ?>
</div>
