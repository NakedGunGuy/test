<?php /** @var array $page */ ?>
<?php /** @var string $slug */ ?>

<?php start_section('page_title'); ?>Edit Page: <?= htmlspecialchars($slug) ?><?php end_section('page_title'); ?>

<div class="admin-header">
    <div>
        <a href="<?= url('admin/pages') ?>" class="back-link">← Back to Pages</a>
        <h1>Edit Page: <?= htmlspecialchars($slug) ?></h1>
        <p class="description">Build your page with blocks and multi-language support</p>
    </div>
</div>

<form id="page-form" hx-post="<?= url('admin/pages/update/' . $slug) ?>" hx-target="body" class="admin-form">

    <div class="section">
        <div class="section-header">
            <h2>Page Settings</h2>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Slug (Read-only)</label>
                <input type="text" value="<?= htmlspecialchars($slug) ?>" readonly disabled>
                <small>To change the slug, rename the YAML file</small>
            </div>

            <div class="form-group">
                <label for="parent">Parent Page</label>
                <input type="text" id="parent" name="parent" value="<?= htmlspecialchars($page['parent'] ?? '') ?>" placeholder="Leave empty for top-level">
                <small>Enter the slug of the parent page (e.g., "about")</small>
            </div>

            <div class="form-group">
                <label for="layout">Layout</label>
                <select id="layout" name="layout">
                    <option value="default" <?= ($page['layout'] ?? 'default') === 'default' ? 'selected' : '' ?>>Default Layout</option>
                    <option value="admin" <?= ($page['layout'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin Layout</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Full Path</label>
            <input type="text" value="<?= htmlspecialchars($page['full_path']) ?>" readonly disabled>
            <small>Preview: <a href="<?= url($page['full_path']) ?>" target="_blank"><?= url($page['full_path']) ?></a></small>
        </div>
    </div>

    <!-- Language Tabs -->
    <div class="section">
        <div class="language-tabs">
            <button type="button" class="tab-btn active" data-lang="en">🇬🇧 English</button>
            <button type="button" class="tab-btn" data-lang="si">🇸🇮 Slovenščina</button>
        </div>

        <!-- English Content -->
        <div class="language-content active" data-lang="en">
            <div class="form-group">
                <label for="title_en">Page Title</label>
                <input type="text" id="title_en" name="title_en"
                       value="<?= htmlspecialchars($page['translations']['en']['title'] ?? '') ?>"
                       required>
            </div>

            <div class="page-builder">
                <div class="builder-header">
                    <h3>Page Content Blocks</h3>
                    <div class="builder-actions">
                        <button type="button" class="btn small blue" onclick="addBlock('en', 'hero')">+ Hero</button>
                        <button type="button" class="btn small blue" onclick="addBlock('en', 'heading')">+ Heading</button>
                        <button type="button" class="btn small blue" onclick="addBlock('en', 'text')">+ Text</button>
                        <button type="button" class="btn small blue" onclick="addBlock('en', 'image')">+ Image</button>
                        <button type="button" class="btn small blue" onclick="addBlock('en', 'columns')">+ Columns</button>
                    </div>
                </div>

                <div id="blocks-en" class="blocks-container">
                    <!-- Blocks will be rendered here -->
                </div>
            </div>

            <input type="hidden" name="blocks_en" id="blocks_en_data">
        </div>

        <!-- Slovenian Content -->
        <div class="language-content" data-lang="si">
            <div class="form-group">
                <label for="title_si">Page Title</label>
                <input type="text" id="title_si" name="title_si"
                       value="<?= htmlspecialchars($page['translations']['si']['title'] ?? '') ?>"
                       required>
            </div>

            <div class="page-builder">
                <div class="builder-header">
                    <h3>Page Content Blocks</h3>
                    <div class="builder-actions">
                        <button type="button" class="btn small blue" onclick="addBlock('si', 'hero')">+ Hero</button>
                        <button type="button" class="btn small blue" onclick="addBlock('si', 'heading')">+ Heading</button>
                        <button type="button" class="btn small blue" onclick="addBlock('si', 'text')">+ Text</button>
                        <button type="button" class="btn small blue" onclick="addBlock('si', 'image')">+ Image</button>
                        <button type="button" class="btn small blue" onclick="addBlock('si', 'columns')">+ Columns</button>
                    </div>
                </div>

                <div id="blocks-si" class="blocks-container">
                    <!-- Blocks will be rendered here -->
                </div>
            </div>

            <input type="hidden" name="blocks_si" id="blocks_si_data">
        </div>
    </div>

    <div class="form-actions">
        <a href="<?= url('admin/pages') ?>" class="btn black">Cancel</a>
        <button type="submit" class="btn blue">Save Page</button>
    </div>
</form>

<script>
// Page blocks data
const pageBlocks = {
    en: <?= json_encode($page['translations']['en']['blocks'] ?? []) ?>,
    si: <?= json_encode($page['translations']['si']['blocks'] ?? []) ?>
};

// Language tab switching
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const lang = this.dataset.lang;

        // Update tab buttons
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');

        // Update content panels
        document.querySelectorAll('.language-content').forEach(c => c.classList.remove('active'));
        document.querySelector(`.language-content[data-lang="${lang}"]`).classList.add('active');
    });
});

// Render initial blocks
function renderBlocks(lang) {
    const container = document.getElementById(`blocks-${lang}`);
    container.innerHTML = '';

    pageBlocks[lang].forEach((block, index) => {
        container.appendChild(createBlockElement(lang, block, index));
    });

    updateHiddenInput(lang);
}

// Create block element
function createBlockElement(lang, block, index) {
    const div = document.createElement('div');
    div.className = 'block';
    div.dataset.index = index;

    let content = '';

    switch(block.type) {
        case 'hero':
            content = `
                <div class="block-header">
                    <div class="block-type">🎭 Hero Section</div>
                    <div class="block-actions">
                        <button type="button" onclick="moveBlock('${lang}', ${index}, -1)" class="btn-icon">↑</button>
                        <button type="button" onclick="moveBlock('${lang}', ${index}, 1)" class="btn-icon">↓</button>
                        <button type="button" onclick="removeBlock('${lang}', ${index})" class="btn-icon delete">×</button>
                    </div>
                </div>
                <div class="block-content">
                    <input type="text" placeholder="Heading" value="${block.heading || ''}"
                           onchange="updateBlock('${lang}', ${index}, 'heading', this.value)">
                    <input type="text" placeholder="Subheading" value="${block.subheading || ''}"
                           onchange="updateBlock('${lang}', ${index}, 'subheading', this.value)">
                    <div class="image-upload">
                        <input type="text" placeholder="Background Image URL" value="${block.image || ''}"
                               onchange="updateBlock('${lang}', ${index}, 'image', this.value)">
                        <button type="button" class="btn small" onclick="uploadImage('${lang}', ${index}, 'image')">Upload</button>
                    </div>
                </div>
            `;
            break;

        case 'heading':
            content = `
                <div class="block-header">
                    <div class="block-type">📝 Heading</div>
                    <div class="block-actions">
                        <button type="button" onclick="moveBlock('${lang}', ${index}, -1)" class="btn-icon">↑</button>
                        <button type="button" onclick="moveBlock('${lang}', ${index}, 1)" class="btn-icon">↓</button>
                        <button type="button" onclick="removeBlock('${lang}', ${index})" class="btn-icon delete">×</button>
                    </div>
                </div>
                <div class="block-content">
                    <select onchange="updateBlock('${lang}', ${index}, 'level', this.value)">
                        <option value="1" ${block.level == 1 ? 'selected' : ''}>H1</option>
                        <option value="2" ${block.level == 2 ? 'selected' : ''}>H2</option>
                        <option value="3" ${block.level == 3 ? 'selected' : ''}>H3</option>
                    </select>
                    <input type="text" placeholder="Heading text" value="${block.content || ''}"
                           onchange="updateBlock('${lang}', ${index}, 'content', this.value)">
                </div>
            `;
            break;

        case 'text':
            content = `
                <div class="block-header">
                    <div class="block-type"><?= icon('file') ?> Text</div>
                    <div class="block-actions">
                        <button type="button" onclick="moveBlock('${lang}', ${index}, -1)" class="btn-icon">↑</button>
                        <button type="button" onclick="moveBlock('${lang}', ${index}, 1)" class="btn-icon">↓</button>
                        <button type="button" onclick="removeBlock('${lang}', ${index})" class="btn-icon delete">×</button>
                    </div>
                </div>
                <div class="block-content">
                    <textarea rows="4" placeholder="Enter your text content..."
                              onchange="updateBlock('${lang}', ${index}, 'content', this.value)">${block.content || ''}</textarea>
                </div>
            `;
            break;

        case 'image':
            content = `
                <div class="block-header">
                    <div class="block-type"><?= icon('image') ?> Image</div>
                    <div class="block-actions">
                        <button type="button" onclick="moveBlock('${lang}', ${index}, -1)" class="btn-icon">↑</button>
                        <button type="button" onclick="moveBlock('${lang}', ${index}, 1)" class="btn-icon">↓</button>
                        <button type="button" onclick="removeBlock('${lang}', ${index})" class="btn-icon delete">×</button>
                    </div>
                </div>
                <div class="block-content">
                    <div class="image-upload">
                        <input type="text" placeholder="Image URL" value="${block.url || ''}"
                               onchange="updateBlock('${lang}', ${index}, 'url', this.value)">
                        <button type="button" class="btn small" onclick="uploadImage('${lang}', ${index}, 'url')">Upload</button>
                    </div>
                    ${block.url ? `<img src="${block.url}" style="max-width: 200px; margin-top: 0.5rem; border-radius: 8px;">` : ''}
                    <input type="text" placeholder="Alt text" value="${block.alt || ''}"
                           onchange="updateBlock('${lang}', ${index}, 'alt', this.value)">
                    <input type="text" placeholder="Caption (optional)" value="${block.caption || ''}"
                           onchange="updateBlock('${lang}', ${index}, 'caption', this.value)">
                </div>
            `;
            break;

        case 'columns':
            content = `
                <div class="block-header">
                    <div class="block-type"><?= icon('columns') ?> Columns</div>
                    <div class="block-actions">
                        <button type="button" onclick="moveBlock('${lang}', ${index}, -1)" class="btn-icon">↑</button>
                        <button type="button" onclick="moveBlock('${lang}', ${index}, 1)" class="btn-icon">↓</button>
                        <button type="button" onclick="removeBlock('${lang}', ${index})" class="btn-icon delete">×</button>
                    </div>
                </div>
                <div class="block-content">
                    <label>Number of columns:</label>
                    <select onchange="updateBlock('${lang}', ${index}, 'count', this.value)">
                        <option value="2" ${block.count == 2 ? 'selected' : ''}>2 Columns</option>
                        <option value="3" ${block.count == 3 ? 'selected' : ''}>3 Columns</option>
                        <option value="4" ${block.count == 4 ? 'selected' : ''}>4 Columns</option>
                    </select>
                    <textarea rows="4" placeholder="Column content (each line is a column)"
                              onchange="updateBlock('${lang}', ${index}, 'content', this.value)">${Array.isArray(block.content) ? block.content.join('\n') : block.content || ''}</textarea>
                </div>
            `;
            break;
    }

    div.innerHTML = content;
    return div;
}

// Add new block
function addBlock(lang, type) {
    const newBlock = { type };

    switch(type) {
        case 'hero':
            newBlock.heading = '';
            newBlock.subheading = '';
            newBlock.image = '';
            break;
        case 'heading':
            newBlock.level = 2;
            newBlock.content = '';
            break;
        case 'text':
            newBlock.content = '';
            break;
        case 'image':
            newBlock.url = '';
            newBlock.alt = '';
            newBlock.caption = '';
            break;
        case 'columns':
            newBlock.count = 2;
            newBlock.content = [];
            break;
    }

    pageBlocks[lang].push(newBlock);
    renderBlocks(lang);
}

// Update block field
function updateBlock(lang, index, field, value) {
    if (field === 'content' && pageBlocks[lang][index].type === 'columns') {
        // Split by newlines for columns
        pageBlocks[lang][index][field] = value.split('\n').filter(line => line.trim());
    } else {
        pageBlocks[lang][index][field] = value;
    }
    updateHiddenInput(lang);
}

// Remove block
function removeBlock(lang, index) {
    if (confirm('Are you sure you want to remove this block?')) {
        pageBlocks[lang].splice(index, 1);
        renderBlocks(lang);
    }
}

// Move block
function moveBlock(lang, index, direction) {
    const newIndex = index + direction;
    if (newIndex < 0 || newIndex >= pageBlocks[lang].length) return;

    const temp = pageBlocks[lang][index];
    pageBlocks[lang][index] = pageBlocks[lang][newIndex];
    pageBlocks[lang][newIndex] = temp;

    renderBlocks(lang);
}

// Upload image
function uploadImage(lang, index, field) {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/*';

    input.onchange = async (e) => {
        const file = e.target.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('image', file);

        try {
            const response = await fetch('<?= url('admin/pages/upload-image') ?>', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                pageBlocks[lang][index][field] = result.url;
                renderBlocks(lang);
            } else {
                alert('Upload failed: ' + (result.error || 'Unknown error'));
            }
        } catch (error) {
            alert('Upload failed: ' + error.message);
        }
    };

    input.click();
}

// Update hidden input with JSON data
function updateHiddenInput(lang) {
    document.getElementById(`blocks_${lang}_data`).value = JSON.stringify(pageBlocks[lang]);
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    renderBlocks('en');
    renderBlocks('si');
});
</script>

<style>
.back-link {
    display: inline-block;
    margin-bottom: 1rem;
    color: #00AEEF;
    text-decoration: none;
    font-size: 0.9rem;
}

.back-link:hover {
    text-decoration: underline;
}

.admin-form {
    max-width: 1400px;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.9);
}

.form-group input[type="text"],
.form-group select,
.form-group textarea {
    width: 100%;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    padding: 0.75rem;
    color: white;
    font-size: 0.95rem;
}

.form-group input:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.form-group textarea {
    resize: vertical;
    font-family: inherit;
}

.form-group small {
    display: block;
    margin-top: 0.25rem;
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.85rem;
}

.form-group small a {
    color: #00AEEF;
    text-decoration: none;
}

.form-group small a:hover {
    text-decoration: underline;
}

.language-tabs {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
    border-bottom: 2px solid rgba(255, 255, 255, 0.1);
}

.tab-btn {
    background: none;
    border: none;
    padding: 0.75rem 1.5rem;
    color: rgba(255, 255, 255, 0.6);
    cursor: pointer;
    font-size: 1rem;
    border-bottom: 2px solid transparent;
    margin-bottom: -2px;
    transition: all 0.2s;
}

.tab-btn:hover {
    color: rgba(255, 255, 255, 0.8);
}

.tab-btn.active {
    color: #00AEEF;
    border-bottom-color: #00AEEF;
}

.language-content {
    display: none;
}

.language-content.active {
    display: block;
}

.page-builder {
    margin-top: 2rem;
}

.builder-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.builder-header h3 {
    margin: 0;
    font-size: 1.1rem;
}

.builder-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.blocks-container {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.block {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 12px;
    overflow: hidden;
}

.block-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 1rem;
    background: rgba(0, 174, 239, 0.1);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.block-type {
    font-weight: 600;
    color: #00AEEF;
}

.block-actions {
    display: flex;
    gap: 0.5rem;
}

.btn-icon {
    background: rgba(255, 255, 255, 0.1);
    border: none;
    width: 28px;
    height: 28px;
    border-radius: 6px;
    color: white;
    cursor: pointer;
    font-size: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.btn-icon:hover {
    background: rgba(255, 255, 255, 0.2);
}

.btn-icon.delete {
    background: rgba(239, 68, 68, 0.2);
    font-size: 1.5rem;
    line-height: 1;
}

.btn-icon.delete:hover {
    background: rgba(239, 68, 68, 0.4);
}

.block-content {
    padding: 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.block-content input,
.block-content textarea,
.block-content select {
    background: rgba(0, 0, 0, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 6px;
    padding: 0.6rem;
    color: white;
    font-size: 0.9rem;
}

.image-upload {
    display: flex;
    gap: 0.5rem;
}

.image-upload input {
    flex: 1;
}

.form-actions {
    display: flex;
    gap: 1rem;
    padding: 1.5rem 0;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    margin-top: 2rem;
}

.section + .section {
    margin-top: 2rem;
}
</style>
