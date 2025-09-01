<?php /** @var array $page */ ?>
<h1><?= htmlspecialchars($page['title']) ?></h1>

<section class="hero">
    <h2><?= htmlspecialchars($page['content']['hero']['heading'] ?? '') ?></h2>
    <p><?= htmlspecialchars($page['content']['hero']['subheading'] ?? '') ?></p>
</section>

<article>
    <?php if (!empty($page['content']['text'])): ?>
        <?php foreach ($page['content']['text'] as $paragraph): ?>
            <p><?= htmlspecialchars($paragraph) ?></p>
        <?php endforeach; ?>
    <?php endif; ?>
</article>
