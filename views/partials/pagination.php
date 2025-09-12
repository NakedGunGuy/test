<?php
/**
 * Pagination component
 * @var array $pagination - pagination data containing current_page, total_pages, per_page, total_products, filters
 */

if ($pagination['total_pages'] <= 1) return; // Don't show pagination if only 1 page

$current_page = $pagination['current_page'];
$total_pages = $pagination['total_pages'];
$filters = $pagination['filters'] ?? [];

// Build query string for pagination links
function build_pagination_url($page, $per_page, $filters) {
    $params = array_merge($filters, ['page' => $page, 'per_page' => $per_page]);
    return '?' . http_build_query($params);
}

// Calculate page range to show
$range = 2; // Show 2 pages before and after current page
$start = max(1, $current_page - $range);
$end = min($total_pages, $current_page + $range);
?>

<div class="pagination-container">
    <div class="pagination-info">
        Showing <?= (($current_page - 1) * $pagination['per_page'] + 1) ?>-<?= min($current_page * $pagination['per_page'], $pagination['total_products']) ?> of <?= $pagination['total_products'] ?> products
    </div>
    
    <div class="pagination-controls">
        <?php if ($current_page > 1): ?>
            <a href="<?= build_pagination_url(1, $pagination['per_page'], $filters) ?>" class="pagination-btn">First</a>
            <a href="<?= build_pagination_url($current_page - 1, $pagination['per_page'], $filters) ?>" class="pagination-btn">‹ Prev</a>
        <?php endif; ?>
        
        <?php if ($start > 1): ?>
            <a href="<?= build_pagination_url(1, $pagination['per_page'], $filters) ?>" class="pagination-btn">1</a>
            <?php if ($start > 2): ?>
                <span class="pagination-ellipsis">...</span>
            <?php endif; ?>
        <?php endif; ?>
        
        <?php for ($i = $start; $i <= $end; $i++): ?>
            <?php if ($i == $current_page): ?>
                <span class="pagination-btn active"><?= $i ?></span>
            <?php else: ?>
                <a href="<?= build_pagination_url($i, $pagination['per_page'], $filters) ?>" class="pagination-btn"><?= $i ?></a>
            <?php endif; ?>
        <?php endfor; ?>
        
        <?php if ($end < $total_pages): ?>
            <?php if ($end < $total_pages - 1): ?>
                <span class="pagination-ellipsis">...</span>
            <?php endif; ?>
            <a href="<?= build_pagination_url($total_pages, $pagination['per_page'], $filters) ?>" class="pagination-btn"><?= $total_pages ?></a>
        <?php endif; ?>
        
        <?php if ($current_page < $total_pages): ?>
            <a href="<?= build_pagination_url($current_page + 1, $pagination['per_page'], $filters) ?>" class="pagination-btn">Next ›</a>
            <a href="<?= build_pagination_url($total_pages, $pagination['per_page'], $filters) ?>" class="pagination-btn">Last</a>
        <?php endif; ?>
    </div>
</div>

<style>
.pagination-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 20px 0;
    padding: 15px 0;
    border-top: 1px solid #C0C0D133;
}

.pagination-info {
    color: #999;
    font-size: 14px;
}

.pagination-controls {
    display: flex;
    gap: 8px;
    align-items: center;
}

.pagination-btn {
    padding: 8px 12px;
    text-decoration: none;
    background: #1E1E27;
    color: #fff;
    border: 1px solid #C0C0D133;
    border-radius: 6px;
    font-size: 14px;
    transition: all 0.2s ease;
    min-width: 40px;
    text-align: center;
}

.pagination-btn:hover {
    background: #01AFFC;
    border-color: #01AFFC;
    color: #fff;
}

.pagination-btn.active {
    background: #01AFFC;
    border-color: #01AFFC;
    color: #fff;
    font-weight: bold;
}

.pagination-ellipsis {
    color: #666;
    padding: 0 8px;
}

@media (max-width: 768px) {
    .pagination-container {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
    
    .pagination-controls {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .pagination-btn {
        padding: 6px 10px;
        font-size: 12px;
        min-width: 35px;
    }
}
</style>