<?php use Aura\Html\Functions; ?>
<p>
    Failed to delete blog post
    titled "<?= h($this->blog->title); ?>"
    by <?= h($this->blog->author); ?>.
</p>
