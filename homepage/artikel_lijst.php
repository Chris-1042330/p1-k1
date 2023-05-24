<?php
/** @var array $block */
/** @var templateConfig $tplConfig */

/* Required modules */
smart_include_css('homepage/style.less');
smart_include_js('homepage/main.js');
/* Set config */
$blockClass = ['projects'];
if ($block[0]['klasse'] != '') {
    $blockClass[] = $block[0]['klasse'];
}

/* Retrieve items */
$itemsRaw = new \templates\art_projects\ProjectModel();
$itemsRaw->setCurrentGroup(Rq::getGroupInt('group'));
$items = $itemsRaw->getAll();

if (!empty($items)) {
    ?>
    <div class="c-newsitems c-newsitems--homepage mb-xs-3">
        <div class="row">
            <?php
            foreach ($items as $item) {
                $img = $item->getBestand() // getThumbnail() geeft 1920x1080 terug?;
                ?>
                <div class="col-sm-4">
                    <article class="text-center" itemscope itemtype="http://schema.org/NewsArticle">
                        <div class="c-newsitems__content">
                            <div>
                                <h2 class="h2-like"><a href="<?= Link::c(Rq::getInt('page'))->artikel_id($item->getArtikel_id()); ?>"><?= $item->getNaam(); ?></a></h2>
                            </div>
                            <img class="img-responsive" src="<?= lcms::resize($img, 348, 496, '348x496') ?>"
                                 alt="<?= $item->getNaam(); ?>"/>
                            <div class="c-newsitems__content">
                                <a class="o-button animation--ripple" data-ripple-color="#fff"
                                   href="<?= Link::c(Rq::getInt('page'))->artikel_id($item->getArtikel_id()); ?>"><?= lcms::t('read_more', 'Lees meer'); ?></a>
                            </div>
                        </div>
                    </article>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
    <?php
}
?>
