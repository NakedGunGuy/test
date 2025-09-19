<?php
$current_language = get_current_language();
$current_path = get_uri_without_language();
?>

<div class="language-toggle">
    <select name="language" class="form-input" onchange="switchLanguage(this.value)">
            <?php foreach (AVAILABLE_LANGUAGES as $lang): ?>
                <option value="<?= $lang ?>" <?= $current_language === $lang ? 'selected' : '' ?>>
                    <?= get_language_flag($lang) ?> <?= get_language_name($lang) ?>
                </option>
            <?php endforeach; ?>
        </select>
</div>

<script>
function switchLanguage(language) {
    const currentPath = '<?= htmlspecialchars($current_path) ?>';
    const newUrl = '/' + language + currentPath;
    window.location.href = newUrl;
}
</script>