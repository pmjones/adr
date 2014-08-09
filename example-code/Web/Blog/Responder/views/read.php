<?php use Aura\Html\Escaper as e; ?>

<h2><?= e::h($this->blog->title) ?></h2>
<p class="byline"><?= e::h($this->blog->author) ?></p>

<div class="blog-body">
<?= $this->blog->body // raw ?>
</div>
