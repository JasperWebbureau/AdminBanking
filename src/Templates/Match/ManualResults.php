<?php $h = function ($value): string { return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }; ?>
<?php if (strlen(trim((string)$query)) < 2) { ?>
    <div class="admin-empty-state admin-empty-state--compact"><i class="fas fa-keyboard"></i><p>Vul minimaal twee tekens in om beschikbare doelen te zoeken.</p></div>
<?php } elseif (empty($targets)) { ?>
    <div class="admin-empty-state admin-empty-state--compact"><i class="fas fa-magnifying-glass"></i><p>Geen beschikbare facturen of uitgaven gevonden voor “<?=$h($query)?>”.</p></div>
<?php } else { ?>
    <div class="admin-banking-targets">
        <?php foreach ($targets as $target) { ?>
            <article class="admin-banking-target<?=$target['selectable'] ? '' : ' is-disabled'?>">
                <div class="admin-banking-target__heading"><span class="fg-table__badge fg-table__badge--info"><?=$h($target['target_label'])?></span><strong><?=$h($target['title'])?></strong></div>
                <dl>
                    <div><dt><?=$target['target_type'] === 'invoice' ? 'Klant' : 'Leverancier'?></dt><dd><?=$h($target['party'] ?: '—')?></dd></div>
                    <div><dt>Datum</dt><dd><?=$h($target['date'])?></dd></div>
                    <div><dt>Referentie</dt><dd><?=$h($target['reference'] ?: '—')?></dd></div>
                    <div><dt>Totaal</dt><dd><?=$h($target['total'])?></dd></div>
                    <?php if ($target['show_available']) { ?><div><dt><?=$h($target['available_label'])?></dt><dd><?=$h($target['available'])?></dd></div><?php } ?>
                </dl>
                <?php if ($target['warning'] !== '') { ?><p class="admin-banking-target__warning<?=$target['selectable'] ? ' is-info' : ''?>"><i class="fas fa-triangle-exclamation"></i> <?=$h($target['warning'])?></p><?php } ?>
                <div class="admin-banking-target__actions">
                    <?php if ($target['selectable']) { ?>
                        <button class="button button-publish" type="button" ajax="true" action="<?=$h($acceptManualAction ?? '')?>" public_id="<?=$h($publicId)?>" target_type="<?=$h($target['target_type'])?>" target_public_id="<?=$h($target['target_public_id'])?>" alert="Dit doel handmatig koppelen en de banktransactie definitief verwerken?" use-waiting-icon><i class="fas fa-link"></i> Handmatig koppelen</button>
                    <?php } else { ?>
                        <button class="button button-secondary" type="button" disabled><i class="fas fa-ban"></i> Niet koppelbaar</button>
                    <?php } ?>
                </div>
            </article>
        <?php } ?>
    </div>
<?php } ?>
