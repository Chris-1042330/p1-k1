<?php
/** @var array $block */

/* Set config */
$blockClass = [''];
if ($block[0]['klasse'] != '') {
    $blockClass[] = $block[0]['klasse'];
}
include 'maatwerk/opendesign.php';
include 'maatwerk/designConvertor.php';

$itemRaw = new \templates\art_projects\ProjectModel;
$item = $itemRaw->getById(Rq::getInt('artikel_id'));

$design['artikel'] = $item->getArtikel_id();
$design['id'] = $item->getDesign_id();
$design['file'] = get_art_file_path($item->getBestand() ?: $item->getUrl());
$design['name'] = $item->getNaam();
$design['format'] = $item->getDesign_type();

$OpenAPI = new openDesign();
$convert = new designConvertor();

if (isset($_POST['convert'])) {
    $response = $OpenAPI->convert($design);
    $design['id'] = $response->id;
    $design['type'] = $response->format;
}
if(!empty($design['id'])){
    $artboards = json_decode($OpenAPI->getList('artboards', $design['id'])[1]);
}
if (isset($_GET['artboard'])) {
    $artboardContent = $OpenAPI->getArtboard($design['id'], $_GET['artboard'])[1];
    $currentArtboard = json_decode($artboardContent);
    if(!empty($_POST)){
        switch($_POST) {
            case isset($_POST['octopus']):
                header("Content-Disposition: attachment;filename=octopus_{$currentArtboard->name}.json");
                $content = json_encode($currentArtboard, JSON_PRETTY_PRINT);
                break;
            case isset($_POST['LCMS']):
                header("Content-Disposition: attachment;filename=lcms_{$currentArtboard->name}.json");
                $content = $convert->getLined($currentArtboard, $_POST['layers']);
                break;
            case isset($_POST['CSS']):
                header("Content-Disposition: attachment;filename=css_{$currentArtboard->name}.css");
                $content = $convert->getStyleguide($currentArtboard, $_POST['layers']);
                break;
        }
        if(!$content == 0){
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            header("Content-Type: application/force-download");
            header("Content-Type: application/octet-stream");
            header("Content-Type: application/download");
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($content));
            echo $content;
            die();
        }
    }
}
if (isset($_POST['delete'])) {
    Artikel::delete($item->getArtikel_id());
    header("Location: ./");
}
//$response = $OpenAPI->getInfo($design['id']);
//echo "<pre>" . $response[0] . $response[1] . "</pre>";
?>
<section class="row">
    <section class="col-md-6 col-sm-6 col-xs-12">
        <h1> <?= $design['name']; ?></h1>
    </section>
    <section class="col-md-6 col-sm-6 col-xs-12">
        <div class="right">

            <?php if (!empty($design['id'])) { ?>
                <form method="get">
                    <select name='artboard' class="btn" onchange="this.form.submit()">
                        <option value="">Select artboard</option>
                        <?php foreach ($artboards->artboards as $artboard) { ?>
                            <option value="<?= $artboard->id?>"><?= $artboard->name ?></option>
                        <?php } ?>
                    </select><br>
                </form>
            <?php } ?>
            <form method="post">
                <button type="submit" class="btn btn-danger" onclick="return confirm('you sure?')" name="delete">Delete</button>
            </form>
        </div>
    </section>
</section>

<section class="row">
    <?php if (empty($design['id'])) { ?>
        <p>Design hasn't been converted. Click the button to convert it</p>
        <form method="post">
            <input type="submit" name="convert" value="Convert design">
        </form>

    <?php } else { ?>
        <section class="col-md-4 col-sm-8 col-xs-12">
            <p>Design Type: <?= $design['format']; ?><br>
                <span style="font-size: 12px"> Design ID: <?= $design['id']; ?></span>
            </p>
            <?php if (!empty($currentArtboard)) { ?>
<!--            <form method="post">-->
<!--                <button type="submit" style="width: 100%" class="btn" value="true" name="download">Download Octopus design</button><br>-->
<!--                <button type="submit" style="width: 100%" class="btn" value="true" name="LCMS">ALL Download Lined JSON-schema</button><br>-->
<!--                <button type="submit" style="width: 100%" class="btn" value="true" name="CSS">Download CSS styleguide</button><br>-->
<!--            </form>-->
            <ul>
                <?php if (!empty($currentArtboard)) { ?>
                    <li><b>Artboard Name:</b> <?= $currentArtboard->name ?><br>
                        <span style="font-size: 12px">Artboard ID: <?= $currentArtboard->id ?></span>
                    </li>
                    <li><b>Datetime:</b> <?=date("Y-m-d H:i:s", $currentArtboard->timeStamp / 1000)?></li>
                    <li><b>Octopus:</b> <?=$currentArtboard->version->{"octopus-common"}?></li>
                    <li><b>Bounds:</b> <br>
                        <span style="font-size: 12px">Width: <?= $currentArtboard->bounds->width?></span><br>
                        <span style="font-size: 12px">Height: <?= $currentArtboard->bounds->height?></span><br>
    <!--                    <span style="font-size: 12px">Top: --><?php //= $currentArtboard->bounds->top?><!--</span><br>-->
    <!--                    <span style="font-size: 12px">Left: --><?php //= $currentArtboard->bounds->left?><!--</span><br>-->
<!--                        <span style="font-size: 12px">Right: --><?php //= $currentArtboard->bounds->right?><!--</span><br>-->
<!--                        <span style="font-size: 12px">Bottom: --><?php //= $currentArtboard->bounds->bottom?><!--</span>-->
                    </li>
                    <li><b>Frame:</b> X: <?= $currentArtboard->frame->x ?>; Y: <?= $currentArtboard->frame->y ?></li>
<!--                    <li><b>Layers:</b> --><?php //=count($currentArtboard->layers) ?><!--</li>-->
                <?php } ?>
            </ul>
<!--                <label>Choose the layers you want to download</label>-->
<!--                <select name="layers[]" size="30"  class='btn' style="width: 100%; height: 100%" multiple="multiple">-->
<!--                --><?php
//                function displayArrayRecursively($array, $indent='') {
//                    foreach($array as $layer) {
//                        if($layer->type == "groupLayer"){?>
<!--                            <optgroup style="text-align: left;" label="--><?php //= $layer->type ?><!-- - --><?php //=$layer->name?><!--">-->
<!--                                <option style="text-align: left" value="--><?php //=$layer->id?><!--">--><?php //=$layer->name?><!--</option>-->
<!--                                --><?php //displayArrayRecursively($layer->layers, $indent . '&nbsp;'); ?>
<!--                            </optgroup>-->
<!--                        --><?php //}else{?>
<!--                        <option style="text-align: left" value="--><?php //=$layer->id?><!--"> --><?php //= $indent ?><!-- --><?php //= $layer->type ?><!-- - --><?php //=$layer->name?><!--</option>-->
<!--                    --><?php //}
//                    }
//                }
//                displayArrayRecursively($currentArtboard->layers);
//                ?>
<!--                </select>-->
<!--                <fieldset >-->

<!--                </fieldset>-->

            <?php } ?>
        </section>
        <section class="col-md-8 col-sm-8 col-xs-12">


                <?php if (!empty($currentArtboard)) { ?>
            <form method="post">
                <button type="submit" style="width: 100%" class="btn" value="true" name="octopus">Download Octopus design</button><br>
                <button type="submit" style="width: 100%" class="btn" value="true" name="LCMS">ALL Download Lined JSON-schema</button><br>
                <button type="submit" style="width: 100%" class="btn" value="true" name="CSS">Download CSS-styleguide</button><br><br>
            </form>
            <form method="post">
                    <button type="submit" style="width: 100%" class="btn btn-dark" value="true" name="LCMS">Download geselecteerde lagen in Lined JSON-schema</button><br>
                    <button type="submit" style="width: 100%" class="btn btn-dark" value="true" name="CSS">Download geselecteerde lagen in CSS-styleguide</button><br>

                    <legend><b>Choose the layers you want to download</b></legend>
                <?php }
                function displayArrayRecursively($array, $indent='') {
                    foreach($array as $layer) {
                        if($layer->type == "groupLayer"){?>
                            <li>
                                <input type="checkbox" name="layers[]" value="<?=$layer->id?>" id="<?=$layer->id?>"/>
                                <label for="<?=$layer->id?>"><?=$layer->name?></label>
                            </li>
                            <ul>
                                <?php displayArrayRecursively($layer->layers, $indent . '&nbsp;'); ?>
                            </ul>
                        <?php }else{?>
                            <li><input type="checkbox" name="layers[]" value="<?=$layer->id?>"id="<?=$layer->id?>"/>
                                <?php /*=$indent*/?> <label for="<?=$layer->id?>"> <?= $layer->type ?> - <?=$layer->name?></label></li>
                        <?php }
                    }
                }
                displayArrayRecursively($currentArtboard->layers);
                ?>
            </form>
        </section>
    <?php } ?>
</section>

