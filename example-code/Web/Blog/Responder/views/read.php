<?php use Aura\Html\Escaper as e; ?>

<h2><?= e::h($this->blog->title) ?></h2>
<p class="byline"><?= e::h($this->blog->author) ?></p>

<div class="blog-body">
<?= $this->blog->body // raw ?>
</div>
<p><?= $this->a("/blog", 'Back'); ?> | <?= $this->a("/blog/edit/{$this->blog->id}", 'Edit'); ?> | <?= $this->a("/blog/delete/{$this->blog->id}", 'Delete'); ?></p>
