<?php use Aura\Html\Functions; ?>

<h2><?= $this->blog->title; ?></h2>
<p class="byline"><?= $this->blog->author; ?></p>

<div class="blog-body">
<?= $this->blog->body; ?>
</div>
