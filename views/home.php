<?php /** @var array $seo_data */ ?>

<?php if (isset($seo_data)): ?>
    <?php start_section('seo_data'); echo serialize($seo_data); end_section('seo_data'); ?>
<?php endif; ?>

<?php start_section('page_title'); ?>Home<?php end_section('page_title'); ?>
