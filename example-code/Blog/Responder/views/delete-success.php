<?php use Aura\Html\Functions; ?>
<p>
    Successfully deleted blog post
    titled "<?= h($this->blog->title); ?>"
    by <?= h($this->blog->author); ?>.
</p>
