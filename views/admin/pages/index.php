<?php /** @var array $pages */ ?>

<?php start_section('page_title'); ?>CMS Pages<?php end_section('page_title'); ?>

<!-- Page Header -->
<div class="section" style="margin-bottom: 2rem;">
    <a href="<?= url('admin') ?>" class="btn text back">← Back to Dashboard</a>
    <h1 class="section-title" style="margin-top: 0;">CMS Pages</h1>
    <p style="color: #C0C0D1;">Manage your content pages with multi-language support</p>
</div>

<!-- Pages Section -->
<div class="section">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2 class="section-subtitle" style="margin-bottom: 0;">Content Pages</h2>
        <div style="display: flex; gap: 12px;">
            <span style="color: rgba(255, 255, 255, 0.6); font-size: 0.9rem; align-self: center;">
                <?= count($pages) ?> page<?= count($pages) !== 1 ? 's' : '' ?>
            </span>
        </div>
    </div>

    <?php if (empty($pages)): ?>
        <div class="empty-state">
            <div class="empty-icon">📄</div>
            <h3>No pages found</h3>
            <p>Create YAML files in <code>content/pages/</code> to get started</p>
        </div>
    <?php else: ?>
        <!-- Pages Grid -->
        <div class="pages-grid">
            <?php foreach ($pages as $slug => $page): ?>
                <div class="page-card">
                    <div class="page-card-header">
                        <div class="page-icon">📄</div>
                        <div class="page-slug">
                            <code><?= htmlspecialchars($slug) ?></code>
                        </div>
                    </div>

                    <div class="page-card-content">
                        <div class="page-titles">
                            <div class="page-title">
                                <span class="flag">🇬🇧</span>
                                <span class="title"><?= htmlspecialchars($page['translations']['en']['title'] ?? 'Untitled') ?></span>
                            </div>
                            <div class="page-title">
                                <span class="flag">🇸🇮</span>
                                <span class="title"><?= htmlspecialchars($page['translations']['si']['title'] ?? 'Untitled') ?></span>
                            </div>
                        </div>

                        <div class="page-meta">
                            <?php if (!empty($page['parent'])): ?>
                                <div class="meta-item">
                                    <span class="meta-label">Parent:</span>
                                    <span class="meta-value"><?= htmlspecialchars($page['parent']) ?></span>
                                </div>
                            <?php endif; ?>
                            <div class="meta-item">
                                <span class="meta-label">Layout:</span>
                                <span class="meta-value"><?= htmlspecialchars($page['layout'] ?? 'default') ?></span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Blocks:</span>
                                <span class="meta-value"><?= count($page['translations']['en']['blocks'] ?? []) ?> EN / <?= count($page['translations']['si']['blocks'] ?? []) ?> SI</span>
                            </div>
                        </div>
                    </div>

                    <div class="page-card-footer">
                        <a href="<?= url($page['full_path']) ?>" target="_blank" class="btn-link">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6M15 3h6v6M10 14L21 3"/>
                            </svg>
                            Preview
                        </a>
                        <a href="<?= url('admin/pages/edit/' . $slug) ?>" class="btn blue">
                            Edit Page
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: rgba(0, 174, 239, 0.03);
    border: 2px dashed rgba(0, 174, 239, 0.2);
    border-radius: 16px;
}

.empty-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state h3 {
    margin: 0 0 0.5rem 0;
    color: rgba(255, 255, 255, 0.9);
    font-size: 1.5rem;
}

.empty-state p {
    color: rgba(255, 255, 255, 0.6);
    margin: 0;
}

.empty-state code {
    background: rgba(0, 174, 239, 0.15);
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-family: 'Courier New', monospace;
    color: #00AEEF;
}

.pages-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 1.5rem;
}

.page-card {
    background: linear-gradient(145deg, rgba(0, 0, 0, 0.4) 0%, rgba(26, 26, 26, 0.4) 100%);
    border: 1px solid rgba(0, 174, 239, 0.2);
    border-radius: 16px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.page-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0, 174, 239, 0.15);
    border-color: rgba(0, 174, 239, 0.4);
}

.page-card-header {
    padding: 1.25rem;
    background: rgba(0, 174, 239, 0.08);
    border-bottom: 1px solid rgba(0, 174, 239, 0.15);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.page-icon {
    font-size: 2rem;
    opacity: 0.8;
}

.page-slug {
    flex: 1;
}

.page-slug code {
    background: rgba(0, 0, 0, 0.3);
    padding: 0.4rem 0.8rem;
    border-radius: 8px;
    font-family: 'Courier New', monospace;
    font-size: 0.95rem;
    color: #00AEEF;
    font-weight: 600;
}

.page-card-content {
    padding: 1.25rem;
}

.page-titles {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    margin-bottom: 1.25rem;
}

.page-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.page-title .flag {
    font-size: 1.25rem;
}

.page-title .title {
    font-size: 1.1rem;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.95);
}

.page-meta {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
}

.meta-label {
    color: rgba(255, 255, 255, 0.6);
    font-weight: 500;
}

.meta-value {
    color: rgba(255, 255, 255, 0.9);
}

.page-card-footer {
    padding: 1rem 1.25rem;
    background: rgba(0, 0, 0, 0.2);
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    gap: 1rem;
    align-items: center;
}

.btn-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    font-size: 0.9rem;
    transition: color 0.2s;
}

.btn-link:hover {
    color: #00AEEF;
}

.btn-link svg {
    width: 14px;
    height: 14px;
}

.page-card-footer .btn {
    margin-left: auto;
}

@media (max-width: 768px) {
    .pages-grid {
        grid-template-columns: 1fr;
    }

    .page-card-footer {
        flex-direction: column;
        align-items: stretch;
    }

    .page-card-footer .btn {
        margin-left: 0;
        width: 100%;
        text-align: center;
    }
}
</style>
