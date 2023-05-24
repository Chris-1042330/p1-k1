<?php
/** @var array $block */
/** @var templateConfig $tplConfig */

/* Required modules */

/* Set config */
$blockClass = ['projects'];
if ($block[0]['klasse'] != '') {
    $blockClass[] = $block[0]['klasse'];
}

/* Retrieve items */
$itemsRaw = new \templates\art_projects\ProjectModel();
$itemsRaw->setCurrentGroup(Rq::getGroupInt('group'));
$items = $itemsRaw->getAll();
include_once 'maatwerk/opendesign.php';
if (!empty($items)) {
    ?>
    <div class="<?= implode(' ', $blockClass) ?>">
        <?php foreach ($items as $item) {
            $link = Link::c(Rq::getInt('page'))->artikel_id($item->getArtikel_id());
            ?>

            <a href="<?= $link ?>"><?= $item->getNaam(); ?></a><br>
        <?php } ?>
    </div>
    <?php
}