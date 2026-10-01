<?php
$h = function ($value): string { return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); };
$statusTone = $status === 'matched' ? 'success' : ($status === 'ignored' ? 'neutral' : 'warning');
$expenseTitle = trim($description) !== '' ? trim($description) : $counterparty;
$expenseTitle = function_exists('mb_substr') ? mb_substr($expenseTitle, 0, 255, 'UTF-8') : substr($expenseTitle, 0, 255);
$expenseReference = function_exists('mb_substr') ? mb_substr($reference, 0, 128, 'UTF-8') : substr($reference, 0, 128);
?>
<grid class="admin-banking-match-layout fluid">
    <section class="panel admin-panel" style="--cw:5;--cw-sm:12">
        <div class="panel__header admin-panel__header">
            <h3><i class="fas fa-money-bill-transfer"></i> Banktransactie</h3>
            <span class="fg-table__badge fg-table__badge--<?=$h($statusTone)?>"><?=$h($statusLabel)?></span>
        </div>
        <div class="panel__body admin-panel__body admin-banking-transaction-detail">
            <dl>
                <div><dt>Datum</dt><dd><?=$h($date)?></dd></div>
                <div><dt>Bedrag</dt><dd class="<?=$transaction->getAmount()->getMinorUnits() > 0 ? 'admin-banking-amount--incoming' : 'admin-banking-amount--outgoing'?>"><?=$h($amount)?></dd></div>
                <div><dt>Tegenpartij</dt><dd><?=$h($counterparty)?><small><?=$h($counterpartyIban)?></small></dd></div>
                <div><dt>Referentie</dt><dd><?=$h($reference ?: '—')?></dd></div>
                <div class="admin-banking-detail-wide"><dt>Omschrijving</dt><dd><?=$h($description ?: '—')?></dd></div>
            </dl>
            <?php if ($status === 'matched') { ?>
                <p class="admin-banking-decision is-success"><i class="fas fa-circle-check"></i> Afgeletterd aan <?=$h($transaction->getMatchedTargetType())?> <small><?=$h($transaction->getMatchedTargetPublicId())?></small></p>
                <?php if ($transaction->getMatchedTargetType() === 'expense') { ?><a class="button button-secondary" href="<?=$h(rtrim($expenseEditBaseUrl, '/') . '/' . rawurlencode($transaction->getMatchedTargetPublicId()))?>">Open uitgave</a><?php } ?>
            <?php } elseif ($status === 'ignored') { ?>
                <p class="admin-banking-decision"><i class="fas fa-ban"></i> Bewust buiten de aflettering gehouden.</p>
            <?php } elseif ($expenseCreationAvailable && $transaction->getAmount()->getMinorUnits() < 0) { ?>
                <p><button class="button button-publish" type="button" data-admin-banking-show-expense aria-controls="admin-banking-expense-panel" aria-expanded="false"><i class="fas fa-plus"></i> Maak uitgave</button></p>
            <?php } ?>
        </div>
    </section>

    <section class="panel admin-panel" style="--cw:7;--cw-sm:12">
        <div class="panel__header admin-panel__header">
            <h3><i class="fas fa-wand-magic-sparkles"></i> Matchvoorstellen</h3>
            <?php if ($status === 'unmatched') { ?>
                <div class="admin-banking-match-actions">
                    <button class="button button-secondary" type="button" ajax="true" action="<?=$h($ignoreAction ?? '')?>" public_id="<?=$h($publicId)?>" alert="Deze banktransactie bewust negeren?" use-waiting-icon><i class="fas fa-ban"></i> Negeren</button>
                    <form ajax="true" action="<?=$h($generateAction ?? '')?>" method="post">
                        <input type="hidden" name="public_id" value="<?=$h($publicId)?>">
                        <button class="button button-secondary" type="submit"><i class="fas fa-rotate"></i> Voorstellen berekenen</button>
                    </form>
                </div>
            <?php } ?>
        </div>
        <div class="panel__body admin-panel__body">
            <p class="admin-banking-match-notice"><i class="fas fa-circle-info"></i> Een voorstel wordt pas verwerkt nadat je het expliciet bevestigt.</p>
            <?php if (empty($proposals)) { ?>
                <div class="admin-empty-state admin-empty-state--compact"><i class="fas fa-link-slash"></i><p>Geen betrouwbare matchvoorstellen gevonden.</p></div>
            <?php } else { ?>
                <div class="admin-banking-proposals">
                    <?php foreach ($proposals as $proposal) { ?>
                        <article<?=$proposal['selected'] ? ' class="is-selected"' : ''?>>
                            <div class="admin-banking-proposal__heading">
                                <span class="fg-table__badge fg-table__badge--<?=$h($proposal['tone'])?>"><?=$h($proposal['confidence'])?></span>
                                <strong><?=$h($proposal['target_label'])?></strong>
                                <?php if ($proposal['selected']) { ?><span class="fg-table__badge fg-table__badge--success">Gekozen</span><?php } ?>
                            </div>
                            <p><?=$h($proposal['reason'])?></p>
                            <small>Publieke doel-id: <?=$h($proposal['target_public_id'])?></small>
                            <?php if ($status === 'unmatched') { ?>
                                <div class="admin-banking-proposal__actions"><button class="button button-publish" type="button" ajax="true" action="<?=$h($acceptAction ?? '')?>" public_id="<?=$h($publicId)?>" proposal_id="<?=$h($proposal['public_id'])?>" alert="Dit voorstel bevestigen en de banktransactie definitief verwerken?" use-waiting-icon><i class="fas fa-check"></i> Match bevestigen</button></div>
                            <?php } ?>
                        </article>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </section>

    <?php if ($status === 'unmatched' && $expenseCreationAvailable && $transaction->getAmount()->getMinorUnits() < 0) { ?>
        <section id="admin-banking-expense-panel" class="panel admin-panel" style="--cw:12;--cw-sm:12" data-admin-banking-expense-panel hidden>
            <div class="panel__header admin-panel__header"><h3><i class="fas fa-receipt"></i> Uitgave maken van banktransactie</h3></div>
            <div class="panel__body admin-panel__body">
                <?php if (empty($expenseCategories)) { ?>
                    <div class="notification notification--warning">Maak eerst bij Uitgaven een uitgavencategorie aan.</div>
                <?php } else { ?>
                    <form class="admin-form admin-banking-expense-form" ajax="true" action="<?=$h($createExpenseAction)?>" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="public_id" value="<?=$h($publicId)?>">
                        <label class="admin-field" style="--cw:6;--cw-sm:12"><span>Titel *</span><input name="title" value="<?=$h($expenseTitle)?>" maxlength="255" required></label>
                        <label class="admin-field" style="--cw:6;--cw-sm:12"><span>Categorie *</span><select name="category_public_id" required><option value="">Kies een categorie</option><?php foreach ($expenseCategories as $category) { ?><option value="<?=$h($category['public_id'])?>"><?=$h($category['name'] . ' · ' . $category['type_label'])?></option><?php } ?></select><small class="admin-field__help">De categorie bepaalt of dit bedrag als bedrijfskost meetelt.</small></label>
                        <label class="admin-field" style="--cw:6;--cw-sm:12"><span>Brutobedrag</span><input value="<?=$h($transaction->getAmount()->negate()->format() . ' ' . $transaction->getAmount()->getCurrency()->getCode())?>" readonly></label>
                        <label class="admin-field" style="--cw:6;--cw-sm:12"><span>Btw-percentage *</span><input name="vat_rate" type="number" value="21" min="0" max="100" step="0.01" inputmode="decimal" required><small class="admin-field__help">Standaard 21%; netto en btw worden uit het brutobedrag berekend.</small></label>
                        <label class="admin-field" style="--cw:6;--cw-sm:12"><span>Bestaande leverancier (optioneel)</span><select name="supplier_expense_public_id"><option value="">Geen bestaande leverancier</option><?php foreach ($existingSuppliers as $supplierOption) { ?><option value="<?=$h($supplierOption['expense_public_id'])?>"><?=$h($supplierOption['name'])?></option><?php } ?></select><small class="admin-field__help">Gebruikt de gegevens van een eerdere uitgave.</small></label>
                        <label class="admin-field" style="--cw:6;--cw-sm:12"><span>Nieuwe leverancier (optioneel)</span><input name="supplier_name" maxlength="255" placeholder="Naam van leverancier"><small class="admin-field__help">Een ingevulde nieuwe naam gaat voor op de selectie.</small></label>
                        <label class="admin-field" style="--cw:6;--cw-sm:12"><span>Referentie</span><input name="reference" value="<?=$h($expenseReference)?>" maxlength="128"></label>
                        <label class="admin-field" style="--cw:6;--cw-sm:12"><span>Factuur of bon (optioneel)</span><input type="file" name="attachment" accept="application/pdf,image/jpeg,image/png,image/webp"><small class="admin-field__help">PDF of afbeelding, maximaal 25 MB.</small></label>
                        <label class="admin-field" style="--cw:12"><span>Omschrijving</span><textarea name="description" rows="3" maxlength="10000"><?=$h($description)?></textarea></label>
                        <div class="admin-form-actions" style="--cw:12"><button class="button button-publish" type="submit"><i class="fas fa-save"></i> Uitgave opslaan</button></div>
                    </form>
                <?php } ?>
            </div>
        </section>
    <?php } ?>

    <?php if ($status === 'unmatched') { ?>
        <section class="panel admin-panel admin-banking-manual-match" style="--cw:12">
            <div class="panel__header admin-panel__header">
                <div><h3><i class="fas fa-magnifying-glass"></i> Handmatig afletteren</h3><small>Zoek veilig binnen deze administratie en valuta.</small></div>
            </div>
            <div class="panel__body admin-panel__body">
                <form class="admin-banking-manual-search" ajax="true" action="<?=$h($searchAction ?? '')?>" method="post" data-admin-banking-manual-search>
                    <input type="hidden" name="public_id" value="<?=$h($publicId)?>">
                    <label class="admin-data-search"><span class="admin-visually-hidden">Factuur of uitgave zoeken</span><i class="fas fa-search"></i><input type="search" name="q" placeholder="Zoek op nummer, klant, leverancier of referentie…" maxlength="120" autocomplete="off"></label>
                    <button class="button button-secondary" type="submit"><i class="fas fa-search"></i> Zoeken</button>
                </form>
                <div class="admin-banking-manual-results" data-admin-banking-manual-results aria-live="polite">
                    <div class="admin-empty-state admin-empty-state--compact"><i class="fas fa-keyboard"></i><p>Vul minimaal twee tekens in om beschikbare doelen te zoeken.</p></div>
                </div>
            </div>
        </section>
    <?php } ?>
</grid>
