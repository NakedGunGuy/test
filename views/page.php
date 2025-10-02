<?php /** @var array $page */ ?>

<?php start_section('page_title'); ?><?= htmlspecialchars($page['title']) ?><?php end_section('page_title'); ?>

<div class="cms-page">
    <div class="cms-container">
        <?php if (!empty($page['blocks'])): ?>
            <?php foreach ($page['blocks'] as $block): ?>
                <?php
                switch ($block['type']) {
                    case 'hero':
                        ?>
                        <div class="cms-hero" <?= !empty($block['image']) ? 'style="background-image: url(' . htmlspecialchars($block['image']) . ');"' : '' ?>>
                            <div class="cms-hero-overlay">
                                <div class="cms-hero-content">
                                    <?php if (!empty($block['heading'])): ?>
                                        <h1 class="cms-hero-heading"><?= htmlspecialchars($block['heading']) ?></h1>
                                    <?php endif; ?>
                                    <?php if (!empty($block['subheading'])): ?>
                                        <p class="cms-hero-subheading"><?= htmlspecialchars($block['subheading']) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php
                        break;

                    case 'heading':
                        $level = $block['level'] ?? 2;
                        $tag = 'h' . $level;
                        ?>
                        <<?= $tag ?> class="cms-heading cms-heading-<?= $level ?>">
                            <?= htmlspecialchars($block['content'] ?? '') ?>
                        </<?= $tag ?>>
                        <?php
                        break;

                    case 'text':
                        ?>
                        <div class="cms-text">
                            <?= nl2br(htmlspecialchars($block['content'] ?? '')) ?>
                        </div>
                        <?php
                        break;

                    case 'image':
                        if (!empty($block['url'])):
                            ?>
                            <figure class="cms-image">
                                <img src="<?= htmlspecialchars($block['url']) ?>"
                                     alt="<?= htmlspecialchars($block['alt'] ?? '') ?>"
                                     loading="lazy">
                                <?php if (!empty($block['caption'])): ?>
                                    <figcaption><?= htmlspecialchars($block['caption']) ?></figcaption>
                                <?php endif; ?>
                            </figure>
                            <?php
                        endif;
                        break;

                    case 'columns':
                        $count = $block['count'] ?? 2;
                        $columns = is_array($block['content']) ? $block['content'] : [];
                        ?>
                        <div class="cms-columns cms-columns-<?= $count ?>">
                            <?php foreach ($columns as $column): ?>
                                <div class="cms-column">
                                    <?= nl2br(htmlspecialchars($column)) ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php
                        break;
                }
                ?>
            <?php endforeach; ?>
        <?php elseif (!empty($page['content'])): ?>
            <!-- Backward compatibility with old content structure -->
            <?php if (!empty($page['content']['hero'])): ?>
                <section class="hero">
                    <h2><?= htmlspecialchars($page['content']['hero']['heading'] ?? '') ?></h2>
                    <p><?= htmlspecialchars($page['content']['hero']['subheading'] ?? '') ?></p>
                </section>
            <?php endif; ?>

            <article>
                <?php if (!empty($page['content']['text'])): ?>
                    <?php foreach ($page['content']['text'] as $paragraph): ?>
                        <p><?= htmlspecialchars($paragraph) ?></p>
                    <?php endforeach; ?>
                <?php endif; ?>
            </article>
        <?php else: ?>
            <div class="cms-empty">
                <p>This page has no content yet.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.cms-page {
    min-height: 60vh;
}

.cms-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

/* Hero Block */
.cms-hero {
    position: relative;
    min-height: 400px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-size: cover;
    background-position: center;
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 3rem;
}

.cms-hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(0, 0, 0, 0.7), rgba(0, 174, 239, 0.3));
    display: flex;
    align-items: center;
    justify-content: center;
}

.cms-hero-content {
    position: relative;
    text-align: center;
    padding: 2rem;
    max-width: 800px;
}

.cms-hero-heading {
    font-size: 3rem;
    font-weight: 700;
    margin: 0 0 1rem 0;
    color: white;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
}

.cms-hero-subheading {
    font-size: 1.5rem;
    color: rgba(255, 255, 255, 0.9);
    margin: 0;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
}

/* Headings */
.cms-heading {
    margin: 2rem 0 1rem 0;
    font-weight: 600;
}

.cms-heading-1 {
    font-size: 2.5rem;
}

.cms-heading-2 {
    font-size: 2rem;
}

.cms-heading-3 {
    font-size: 1.5rem;
}

/* Text Block */
.cms-text {
    font-size: 1.1rem;
    line-height: 1.8;
    margin: 1.5rem 0;
    color: rgba(255, 255, 255, 0.9);
}

/* Image Block */
.cms-image {
    margin: 2rem 0;
    text-align: center;
}

.cms-image img {
    max-width: 100%;
    height: auto;
    border-radius: 12px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}

.cms-image figcaption {
    margin-top: 0.75rem;
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.6);
    font-style: italic;
}

/* Columns Block */
.cms-columns {
    display: grid;
    gap: 2rem;
    margin: 2rem 0;
}

.cms-columns-2 {
    grid-template-columns: repeat(2, 1fr);
}

.cms-columns-3 {
    grid-template-columns: repeat(3, 1fr);
}

.cms-columns-4 {
    grid-template-columns: repeat(4, 1fr);
}

.cms-column {
    background: rgba(0, 174, 239, 0.05);
    border: 1px solid rgba(0, 174, 239, 0.2);
    border-radius: 12px;
    padding: 1.5rem;
    font-size: 1rem;
    line-height: 1.6;
}

/* Empty State */
.cms-empty {
    text-align: center;
    padding: 4rem 2rem;
    color: rgba(255, 255, 255, 0.5);
}

/* Responsive */
@media (max-width: 768px) {
    .cms-hero {
        min-height: 300px;
    }

    .cms-hero-heading {
        font-size: 2rem;
    }

    .cms-hero-subheading {
        font-size: 1.2rem;
    }

    .cms-heading-1 {
        font-size: 2rem;
    }

    .cms-heading-2 {
        font-size: 1.5rem;
    }

    .cms-heading-3 {
        font-size: 1.25rem;
    }

    .cms-columns-2,
    .cms-columns-3,
    .cms-columns-4 {
        grid-template-columns: 1fr;
    }

    .cms-container {
        padding: 1rem;
    }
}
</style>
