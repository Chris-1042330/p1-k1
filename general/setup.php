<?php
require_once 'modules/artikelen/functions.php';

class setupSocialMediaGeneral extends generate_module
{
    function install()
    {
        $type          = new artikel_type();
        $type          = $type->getByTable('art_social_media');
        $existingModId = (int)$type->get_module_id();

        $this->mod_naam         = 'Social media';
        $this->mod_geen_groepen = '1';
        $this->mod_imgX         = '0';
        $this->mod_imgY         = '0';

        $this->types['art_social_media'] = [
            [
                'naam' => 'titel',
                'veldtype' => '1',
                'extra' => '',
                'gewicht' => '10',
                'is_index' => '1',
                'is_verplicht' => '1',
                'taalgevoelig' => '0'
            ],
            [
                'naam' => 'icon',
                'veldtype' => '1',
                'extra' => 'Vul hier een Font Awesome icoon in.<br/>Bijvoorbeeld: fa-facebook-f',
                'gewicht' => '20',
                'is_index' => '1',
                'is_verplicht' => '1',
                'taalgevoelig' => '0'
            ],
            [
                'naam' => 'link_extern',
                'veldtype' => '1',
                'extra' => '',
                'gewicht' => '30',
                'is_index' => '1',
                'is_verplicht' => '0',
                'taalgevoelig' => '0'
            ]
        ];

        return $this->create_module($existingModId);
    }
}

$setup = new setupSocialMediaGeneral();
$setup->install();
