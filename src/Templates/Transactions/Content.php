<?php $h=function($value):string{return htmlspecialchars((string)$value,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');};?>
<div class="admin-banking-content">
    <grid class="admin-banking-setup-grid fluid">
        <details class="panel admin-panel admin-banking-setup" style="--cw:6;--cw-sm:12"<?=empty($accounts)?' open':''?>>
            <summary><span><i class="fas fa-building-columns"></i> Bankrekeningen</span><small><?=count($accounts)?> ingesteld</small></summary>
            <div class="panel__body admin-panel__body">
                <?php if(!empty($accounts)){?><ul class="admin-banking-account-list"><?php foreach($accounts as$account){?><li><span><strong><?=$h($account->getName())?></strong><small><?=$h($account->getIban()!==''?$account->getIban():$account->getAccountReference())?></small></span><span class="fg-table__badge fg-table__badge--<?=$account->isActive()?'success':'neutral'?>"><?=$account->isActive()?'Actief':'Inactief'?></span></li><?php }?></ul><?php }?>
                <form class="admin-form admin-banking-account-form" ajax="true" action="<?=$h($createAccountAction??'')?>" method="post">
                    <label class="admin-field"><span>Naam *</span><input name="name" maxlength="128" required placeholder="Zakelijke rekening"></label>
                    <label class="admin-field"><span>Rekeningreferentie *</span><input name="account_reference" maxlength="64" required pattern="[A-Za-z0-9._-]+" placeholder="Bij ABN vaak het 9-cijferige rekeningnummer"></label>
                    <label class="admin-field"><span>IBAN</span><input name="iban" maxlength="34" autocomplete="off" placeholder="NL00BANK0000000000"></label>
                    <label class="admin-field"><span>Valuta *</span><input name="currency" value="EUR" maxlength="3" pattern="[A-Za-z]{3}" required></label>
                    <div class="admin-form-actions"><button class="button button-secondary" type="submit"><i class="fas fa-plus"></i> Rekening toevoegen</button></div>
                </form>
            </div>
        </details>
        <details class="panel admin-panel admin-banking-setup" style="--cw:6;--cw-sm:12"<?=!empty($accounts)&&$result->getTotal()===0?' open':''?>>
            <summary><span><i class="fas fa-file-import"></i> ABN-afschrift importeren</span><small>Bestand of geplakte regels</small></summary>
            <div class="panel__body admin-panel__body">
                <?php if(empty($activeAccounts)){?><div class="notification notification--warning">Voeg eerst een actieve bankrekening toe.</div><?php }?>
                <form class="admin-form admin-banking-import-form" ajax="true" action="<?=$h($importAction??'')?>" method="post" enctype="multipart/form-data">
                    <label class="admin-field"><span>Bankrekening *</span><select name="bank_account_public_id" required<?=empty($activeAccounts)?' disabled':''?>><option value="">Kies een rekening</option><?php foreach($activeAccounts as$account){?><option value="<?=$h($account->getPublicId())?>"><?=$h($account->getName().' · '.$account->getAccountReference())?></option><?php }?></select></label>
                    <label class="admin-field"><span>ABN-bestand, maximaal 10 MB</span><input type="file" name="statement_file" accept=".tab,.txt,.csv,text/plain,text/tab-separated-values"></label>
                    <div class="admin-banking-import-separator"><span>of plak de regels</span></div>
                    <label class="admin-field admin-field--wide"><span>Tabgescheiden afschriftregels</span><textarea name="statement_content" rows="6" placeholder="139592644&#9;EUR&#9;20241129&#9;..."></textarea></label>
                    <p class="admin-banking-privacy"><i class="fas fa-shield-halved"></i> Het bronbestand wordt niet bewaard. Alleen genormaliseerde transacties en een controlehash worden opgeslagen.</p>
                    <div class="admin-form-actions"><button class="button button-publish" type="submit"<?=empty($activeAccounts)?' disabled':''?>><i class="fas fa-file-import"></i> Afschrift importeren</button></div>
                </form>
            </div>
        </details>
    </grid>

    <form class="admin-form admin-data-results admin-banking-results" data-admin-banking-filters ajax="true" action="<?=$h($refreshAction??'')?>" method="post">
        <input type="hidden" name="sort" value="<?=$h($query->getSort())?>"><input type="hidden" name="direction" value="<?=$h($query->getDirection())?>"><input type="hidden" name="page" value="<?=$h($result->getPage())?>">
        <div class="admin-summary-grid"><?php foreach($summaryCards as$card){?><article class="admin-stat-card"><span class="admin-tone-icon is-<?=$h($card['tone'])?>"><i class="<?=$h($card['icon'])?>"></i></span><span class="admin-stat-card__content"><span class="admin-stat-card__label"><?=$h($card['label'])?></span><strong class="admin-stat-card__value"><?=$h($card['value'])?></strong><small class="admin-stat-card__meta"><?=$h($card['meta'])?></small></span></article><?php }?></div>
        <grid class="fluid"><section class="panel admin-panel" style="--cw:12"><div class="panel__body admin-panel__body admin-panel__body--flush">
            <div class="admin-data-toolbar admin-banking-toolbar">
                <label class="admin-data-search"><span class="admin-visually-hidden">Banktransacties zoeken</span><i class="fas fa-search"></i><input type="search" name="q" value="<?=$h($query->getSearch())?>" placeholder="Zoek op tegenpartij, omschrijving of referentie…" autocomplete="off"></label>
                <select name="account" aria-label="Filter op rekening"><?php foreach($accountOptions as$value=>$label){?><option value="<?=$h($value)?>"<?=$query->getAccountPublicId()===$value?' selected':''?>><?=$h($label)?></option><?php }?></select>
                <select name="status" aria-label="Filter op status"><option value="">Alle statussen</option><option value="unmatched"<?=$query->getStatus()==='unmatched'?' selected':''?>>Open</option><option value="matched"<?=$query->getStatus()==='matched'?' selected':''?>>Afgeletterd</option><option value="ignored"<?=$query->getStatus()==='ignored'?' selected':''?>>Genegeerd</option></select>
                <select name="flow" aria-label="Filter op richting"><option value="all">In en uit</option><option value="incoming"<?=$query->getFlow()==='incoming'?' selected':''?>>Ontvangen</option><option value="outgoing"<?=$query->getFlow()==='outgoing'?' selected':''?>>Uitgegeven</option></select>
                <select name="year" aria-label="Filter op jaar"><option value="">Alle jaren</option><?php foreach($years as$year){?><option value="<?=$h($year)?>"<?=$query->getYear()===$year?' selected':''?>><?=$h($year)?></option><?php }?></select>
                <button class="button button-secondary admin-data-reset" type="button" data-admin-banking-reset><i class="fas fa-times"></i> Wissen</button>
            </div>
            <?php echo(new \Flexgrid\Html\Table\TableRenderer($table))->render(); ?>
            <div class="admin-data-pagination"><label><span>Toon</span><select name="per_page" aria-label="Aantal resultaten per pagina"><?php foreach($pageSizes as$pageSize){?><option value="<?=$h($pageSize)?>"<?=$query->getPerPage()===$pageSize?' selected':''?>><?=$h($pageSize)?></option><?php }?></select><span>resultaten</span></label><span class="admin-data-pagination__count"><?=$h($result->getFirstPosition())?>–<?=$h($result->getLastPosition())?> van <?=$h($result->getTotal())?></span><nav class="admin-data-pagination__pages" aria-label="Banktransactiepagina's"><button type="button" data-admin-banking-page="<?=$h(max(1,$result->getPage()-1))?>"<?=$result->getPage()<=1?' disabled':''?>><i class="fas fa-chevron-left"></i></button><?php foreach($pages as$page){?><button type="button" data-admin-banking-page="<?=$h($page)?>"<?=$page===$result->getPage()?' class="is-active" aria-current="page"':''?>><?=$h($page)?></button><?php }?><button type="button" data-admin-banking-page="<?=$h(min($result->getTotalPages(),$result->getPage()+1))?>"<?=$result->getPage()>=$result->getTotalPages()?' disabled':''?>><i class="fas fa-chevron-right"></i></button></nav></div>
        </div></section></grid>
    </form>
</div>
