<?php
foreach ($this->collection as $blog) {
?>
<div class="blog-intro">
    <h2><?= $blog->title; ?></h2>
    <p class="byline"><?= $blog->author; ?></p>
    <?= $blog->intro; ?>
    <p><?= $this->anchor("/blog/read/{$blog->id}", 'Read More ...'); ?></p>
</div>
<?php
}
