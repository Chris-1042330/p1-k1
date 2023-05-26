<?php
/** @var array $block */
/** @var templateConfig $tplConfig */

/* Required modules */
smart_include_css('general/style.less');

/* Set config */
$blockClass = ['projects'];
if ($block[0]['klasse'] != '') {
    $blockClass[] = $block[0]['klasse'];
}

/* Retrieve items */
$itemsRaw = new \templates\art_projects\ProjectModel();
$itemsRaw->setCurrentGroup(Rq::getGroupInt('group'));
$items = $itemsRaw->getAll();
/**
 * @param array $items
 * @return void
 */

// TODO: verwijderknop toevoegen aan elk beschikbaar artikel
//if (isset($_POST['delete'])) {
//    Artikel::delete($item->getArtikel_id());
//    header("Location: ./");
//}

if (!empty($items)) {
    ?>
    <div class="c-newsitems c-newsitems--homepage mb-xs-3">
        <div class="row">
            <div class="col-sm-4">
                <article class="text-center" itemscope itemtype="http://schema.org/NewsArticle">
                    <div class="c-newsitems__content">
                        <div>
                            <a href="<?=Link::c(Rq::getInt('page'))->page(5) ?>"
                                <i style="font-size: 40px" class="fa fa-plus" aria-hidden="true"></i>
                                <h2 class="h2-like">Maak project</h2>
                            </a>
                        </div>

                    </div>
                </article>
            </div>
            <?php
            foreach ($items as $item) {
//                $img = $item->getBestand() // getThumbnail() geeft 1920x1080 terug?;
                ?>
                <div class="col-sm-4">
                    <article class="text-center" itemscope itemtype="http://schema.org/NewsArticle">
                        <div class="c-newsitems__content">
                            <div>
                                <h2 class="h2-like">
                                    <a href="<?= Link::c(Rq::getInt('page'))->artikel_id($item->getArtikel_id()); ?>"><?= $item->getNaam(); ?></a>
                                </h2>
                            </div>
<!--                            <img class="img-responsive" src="--><?php //= lcms::resize($img, 348, 496, '348x496') ?><!--"-->
<!--                                 alt="--><?php //= $item->getNaam(); ?><!--"/>-->
                            <div class="c-newsitems__content">
                                <a class="o-button animation--ripple" data-ripple-color="#fff"
                                   href="<?= Link::c(Rq::getInt('page'))->artikel_id($item->getArtikel_id()); ?>"><button class="btn">Bekijken</button></a>
<!--                                <button type="submit" class="btn btn-danger" onclick="return confirm('Weet je het zeker?')" name="delete">Delete</button>-->

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
