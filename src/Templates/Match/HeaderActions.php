<?php $h = static function ($value): string { return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }; ?>
<div class="admin-page-header__actions">
    <a class="button button-secondary" href="<?=$h($overviewUrl ?? '')?>"><i class="fas fa-arrow-left"></i> Terug naar overzicht</a>
</div>
