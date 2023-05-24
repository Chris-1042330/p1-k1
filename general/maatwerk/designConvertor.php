<?php
// GEMAAKT DOOR CHRIS AARTMAN

class designConvertor
{
    private array $LinedPage = [];
    private int $i = 0;

    private int $segment = 0;

    private int $blockCount = 0;
    private int $blockId = 1;
    function __construct(){}

    public function getLined($artboard, $selectedLayers = null): string
    {
        try {
            $this->LinedPage[$this->i] = new stdClass();
            $this->LinedPage[$this->i]->type = "page";
            $this->LinedPage[$this->i]->senderId = 1;

            $this->LinedPage[$this->i]->data = new stdClass();
            $this->LinedPage[$this->i]->data->language = "NL";
            $this->LinedPage[$this->i]->data->canonical = null;
            $this->LinedPage[$this->i]->data->parentArticleId = 0;

            $this->LinedPage[$this->i]->data->page = new stdClass();
            $this->LinedPage[$this->i]->data->page->{"pagina_id"} = $this->LinedPage[$this->i]->senderId;
            $this->LinedPage[$this->i]->data->page->{"toon_inleiding"} = 1;
            $this->LinedPage[$this->i]->data->page->{"menu_kop"} =
            $this->LinedPage[$this->i]->data->page->{"pagina_titel"} = $artboard->name;
            $this->LinedPage[$this->i]->data->page->{"url_value"} = str_replace(" ", "-", strtolower($artboard->name));
            $this->LinedPage[$this->i]->data->page->parent =
                $this->LinedPage[$this->i]->data->page->parent =0;

            $this->LinedPage[$this->i]->data->images = [];
            $this->i++;

            // initieer koppeling tussen pagina met segmenten en blokken
            $this->LinedPage[$this->i] = new stdClass();
            $this->LinedPage[$this->i]->type = "segment";
            $this->LinedPage[$this->i]->senderId = 1;
            $this->LinedPage[$this->i]->data = new stdClass();

            $this->setNewSegment();
            $this->i++;

            $this->recursiveLayersLined($artboard->layers, $selectedLayers);

            return json_encode($this->LinedPage, JSON_PRETTY_PRINT);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    protected function recursiveLayersLined($layers, $selectedLayers): void {
        foreach ($layers as $layer) if ($layer->visible) {
            if ($selectedLayers !== null && !in_array($layer->id, $selectedLayers)) {
                continue;
            }
            else{
                $this->LinedPage[$this->i] = new stdClass();
                $this->LinedPage[$this->i]->type = "block";
                $this->LinedPage[$this->i]->senderId = 0;
                $this->LinedPage[$this->i]->data = new stdClass();
                if ($layer->type == "groupLayer") {
                    $this->LinedPage[$this->i]->type = "segment";
                    $this->setNewSegment();
                    $this->blockCount = 0;
                    $this->i++;
                    $this->recursiveLayersLined($layer->layers, $selectedLayers);
                } else {
                    $this->setNewBlock($layer);
                    $this->i++;
                }
            }
        }
    }
    protected function setNewSegment(){
        $this->LinedPage[$this->i]->senderId = $this->segment + 1;
        $this->LinedPage[$this->i]->data->parentPageId = strval($this->LinedPage[0]->senderId);

        $this->LinedPage[$this->i]->data->segment = new stdClass();
        $this->LinedPage[$this->i]->data->segment->id = $this->LinedPage[$this->i]->senderId;
            $this->LinedPage[$this->i]->data->segment->{"pagina_id"} = $this->LinedPage[$this->i]->data->parentPageId;
        $this->LinedPage[$this->i]->data->segment->title =
            $this->LinedPage[$this->i]->data->segment->name = "standaard";
        $this->LinedPage[$this->i]->data->segment->volgorde = $this->segment;
        $this->LinedPage[$this->i]->data->segment->class =
            $this->LinedPage[$this->i]->data->segment->label = "";
        $this->LinedPage[$this->i]->data->segment->detailType =
            $this->LinedPage[$this->i]->data->segment->{"parent_id"} = 0;
        $this->LinedPage[$this->i]->data->segment->voorwaarden =
            $this->LinedPage[$this->i]->data->segment->{"broadcast_position"} = null;

        $this->segment++;
        return $this->LinedPage[$this->i];
    }
    protected function setNewBlock($layer)
    {
        $this->LinedPage[$this->i]->senderId = $this->blockId;
        $this->LinedPage[$this->i]->data->parentSegmentId = $this->segment;
        $this->LinedPage[$this->i]->data->forHeader =
            $this->LinedPage[$this->i]->data->forFooter = false;
        $this->LinedPage[$this->i]->data->block = new stdClass();
        $type = match ($layer->type) {
            "shapeLayer" => "afbeelding",
            default => "html",
        };
        $this->LinedPage[$this->i]->data->block->{"block_id"} = $this->blockId;
        $this->LinedPage[$this->i]->data->block->{"pagina_id"} = strval($this->LinedPage[0]->senderId);
        $this->LinedPage[$this->i]->data->block->{"type_block"} = $type;
        $this->LinedPage[$this->i]->data->block->volgorde = $this->blockCount;
        $this->LinedPage[$this->i]->data->block->{"toon_inleiding"} = 1;
        $this->LinedPage[$this->i]->data->block->sectie = "sectie-1" /*. $this->segment*/;
        $this->LinedPage[$this->i]->data->block->{"sectie-segment"} = $this->LinedPage[$this->i]->data->parentSegmentId;
        $this->LinedPage[$this->i]->data->block->grid = "grid.php";
        $this->LinedPage[$this->i]->data->block->klasse = substr($layer->name,0,15);

        $this->LinedPage[$this->i]->data->block->koptekst =
            $this->LinedPage[$this->i]->data->block->blocknaam =
            $this->LinedPage[$this->i]->data->block->css_id =
            $this->LinedPage[$this->i]->data->block->template = "";

        $this->LinedPage[$this->i]->data->block->{"user_create"} =
            $this->LinedPage[$this->i]->data->block->{"user_author"} =
            $this->LinedPage[$this->i]->data->block->{"user_update"} =
            $this->LinedPage[$this->i]->data->block->{"sitemap_pagina_id"} =
            $this->LinedPage[$this->i]->data->block->{"opnemen_in_sitemap"} = 0;

        $this->LinedPage[$this->i]->data->block->{"create_time"} = date("Y-m-d H:i:s");;
        $this->LinedPage[$this->i]->data->block->{"template_config"} =
            $this->LinedPage[$this->i]->data->block->voorwaarden =
            $this->LinedPage[$this->i]->data->block->{"broadcast_position"} = null;
        $this->LinedPage[$this->i]->data->blockData = new stdClass();
        switch ($layer->type) {
            case "shapeLayer":
                if($layer->effects->fills[0]->pattern->filename) {
                    $this->LinedPage[$this->i]->data->block->{"type_block"} = "html";
                    $this->LinedPage[$this->i]->data->blockData->html =
                        "<img width=" . $layer->bounds->width / 1.5 . " height=" . $layer->bounds->height / 1.5 ." src='" . $layer->effects->fills[0]->pattern->filename . "'>";
                }
//                $this->LinedPage[$this->i]->data->images[] = new stdClass();
//                if($layer->effects->fills[0]->pattern->filename){
//                    $this->LinedPage[$this->i]->data->images[0]->bestand = $layer->effects->fills[0]->pattern->filename;
//                }
//                $this->LinedPage[$this->i]->data->block->template = "afbeelding";
//                $this->LinedPage[$this->i]->data->blockData->{"block_id"} = $this->LinedPage[$this->i]->data->block->{"block_id"};

                break;
            default:
                $this->LinedPage[$this->i]->data->blockData->html = $layer->text->value;
                $this->LinedPage[$this->i]->data->images = [];

        }
        $this->LinedPage[$this->i]->data->blockStyle = [];


        $this->blockCount++;
        $this->blockId++;
        return $this->LinedPage[$this->i];
    }
    
    public function getStyleguide($artboard){
        $css = $less = [];
        foreach ($artboard->layers as $layer) {
            $selector = "." . str_replace(" ", "-", substr($layer->name,0,15));

            if ($layer->type == "shapeLayer"){
                $css[$selector]['width'] = $layer->bounds->width . "px";
                $css[$selector]['height'] =  $layer->bounds->height . "px";
                $css[$selector]['color'] = sprintf("#%02x%02x%02x", $layer->effects->fills[0]->color->r,$layer->effects->fills[0]->color->g,$layer->effects->fills[0]->color->b);
                if(!in_array($css[$selector]['color'], $less)) $less[/*'fill-color'*/] .= $css[$selector]['color'];
                if($layer->effects->borders){
                    $css[$selector]['border-width'] = $layer->effects->borders[0]->width . "px";
                    $css[$selector]['border-style'] = $layer->effects->borders[0]->style;
                    $css[$selector]['border-color'] = sprintf("#%02x%02x%02x", $layer->effects->borders[0]->color->r,$layer->effects->borders[0]->color->g,$layer->effects->borders[0]->color->b);;
                    if(!in_array($css[$selector]['border-color'], $less)) $less[/*'border-color'*/] .= $css[$selector]['border-color'];
                }
            }
            else if ($layer->type == "textLayer"){
//                $css[$selector]['width'] = $layer->text->frame->width . "px";
//                $css[$selector]['height'] =  $layer->text->frame->height . "px";
                $css[$selector]['color'] = sprintf("#%02x%02x%02x", $layer->text->defaultStyle->color->r,$layer->text->defaultStyle->color->g,$layer->text->defaultStyle->color->b);
                if(!in_array($css[$selector]['color'], $less)) $less[/*'text-color'*/] .= $css[$selector]['color'];
                $css[$selector]['font-family'] = $layer->text->defaultStyle->font->name;
                if(!in_array($css[$selector]['font-family'], $less)) $less[/*'font-family'*/] .= $css[$selector]['font-family'];

                $css[$selector]['font-size'] = $layer->text->defaultStyle->font->size . "px";
                if($layer->text->defaultStyle->font->italic){
                    $css[$selector]['font-style'] = "italic";
                }
                if($layer->text->defaultStyle->font->bold){
                    $css[$selector]['font-weight'] = "bold";
                }
                $css[$selector]['letter-spacing'] = $layer->text->defaultStyle->font->letterSpacing. "px";
//                $css[$selector]['line-height'] = $layer->text->defaultStyle->font->paragraphSpacing;
                $css[$selector]['text-align'] = $layer->text->defaultStyle->font->align;
                $css[$selector]['text-decoration'] = $layer->text->styles[0]->font->underline ;
                $css[$selector]['text-decoration'] .= " " . $layer->text->styles[0]->font->linethrough;
            }

            if($layer->effects->fills[0]->opacity !== null) $css[$selector]['opacity'] = $layer->effects->fills[0]->opacity;
        }

        $cssString = "@home: 'https://dev81.lined.nl/websitenaam202X/ ';\n";
        for ($i = 0; $i < count($less); $i++){
            $cssString .= "@kleur$i: " . $less[$i]. ";\n";
        }
        $cssString .= "\n";
        return $this->css_array_to_string($css, $cssString);
    }
    protected function css_array_to_string($css, $cssString) : string{
        $prefix = '   ';
        $i = 0;
        foreach ($css as $key => $value) {
            if (is_array($value)) {
                $selector = $key;
                $properties = $value;
                $cssString .= "$selector {\n";
                $cssString .= $this->css_array_to_string($properties, null);
                $cssString .= "}\n";
            }
            else {
                $property = $key;
                // if value exists in variable array?
                /*if(in_array($value, $less)){
                    $cssString .= $prefix . "$property: @var$i;\n";
                    $i++;
                }
                else*/ $cssString .= $prefix . "$property: $value;\n";
            }
        }
        return $cssString;
    }

}