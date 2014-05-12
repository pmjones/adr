<?php use Aura\Html\Escaper as e; ?>

<p>
    Successfully deleted blog post
    titled "<?= e::h($this->blog->title) ?>"
    by <?= e::h($this->blog->author) ?>.
</p>
