<?php

class openDesign
{
    private $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE2NzY0NTU5NjAsInYiOjAsImV4cCI6MzMyMDE4Mjg3NjAsImlzcyI6ImF2b2NvZGUtbWFuYWdlciIsImQiOnsiaWQiOjE0NTExMjgsImNvbnRleHQiOiJvZC1hcGkiLCJ0b2tlbl9pZCI6MjMwMTI2MywiZ3JhbnRfaWQiOjk3NjE2ODN9fQ.Nsk1tRzT5B3XQK0IHsp1qkzPyZrx6RtsHJiJsgYJF6Y';

    function __construct()
    {
    }

    protected function execCURL($curl)
    {
        $result = curl_exec($curl);
        if (!$result) {
            $error = curl_error($curl);
            $info = curl_getinfo($curl);
            die("<pre>cURL request failed, error = {$error}; info = " . print_r($info, true) . "</pre>");
        }
        if (curl_errno($curl)) {
            echo 'error:' . curl_error($curl);
            return curl_close($curl);
        }
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        return [$code, $result];
    }


    public function auth(): void
    {
        $url = "https://api.opendesign.dev/token";
        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$this->token}"
        ]);
        $response = $this->execCURL($curl);
        echo "<pre>" . $response[0] . $response[1] . "</pre>";
    }

    public function convert($design)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        $contenttype = 'content-type: application/json';
        $args['design_name'] = $design['name'];

        if($design['format']){
            $args['format'] = strtolower($design['format']);
        }
        switch ($design['format']) {
            case "figma":
//                // Access token: figd_lgfmCY2qNsFuFtkKJYutGOUp1nVri_h8wrI2lDu1
//                // https://www.figma.com/file/:key/:title
//                curl_setopt($curl, CURLOPT_URL,"https://api.opendesign.dev/designs/figma-link");
                $args['figma_filekey'] = $design['url'];
                break;
            case "link": # JSON ONLY
                curl_setopt($curl, CURLOPT_URL, "https://api.opendesign.dev/designs/link");
                $args['url'] = $design['file'];
                break;
            default:
                $contenttype = 'content-type: multipart/form-data';
                curl_setopt($curl, CURLOPT_URL, "https://api.opendesign.dev/designs/upload");
                $args['file'] = new CurlFile($design['file']);
        }
        curl_setopt($curl, CURLOPT_POSTFIELDS, $args);

        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$this->token}",$contenttype
        ]);
        $response = $this->execCURL($curl);

        if ($response[0] == 201) {
            $data = json_decode($response[1]);
            $art = lcms::artikel(6); //ID artikeltabel van projectmodule
            $art->setValue('artikel_id', (int)$design['artikel']);
            $art->setValue('design_id', $data->design->id);
            for($i = 0; $i < 69 && $data->design->completed_at == null; $i++) {
                $newStatus = $this->getInfo($data->design->id);
                if ($newStatus[0] == 200){
                    $newStatusDesign = json_decode($newStatus[1]);
                    $data->design->completed_at = $newStatusDesign->completed_at;
                    $data->design->format = $newStatusDesign->format;
                }
                else{
                    echo "Design could not be converted <br><pre>" . $newStatus[0] . $newStatus[1] . "</pre>";
                    return 0;
                }
            }
            switch($data->design->format){
                case "xd":
                    $art->setValue('design_type', 'XD');
                    break;
                default:
                    $art->setValue('design_type', ucfirst($data->design->format));
            }

            $art->save();
            echo "Design is processing or has been successfully uploaded!";
            return $data->design;
        }
        else{
            echo "<pre>" . $response[0] . $response[1] . "</pre>";
        }
    }

    public function getList($type, $design) : array
    {
        switch($type){
            case 'artboards':
                $url = "https://api.opendesign.dev/designs/" . $design . "/artboards";
                break;
            case 'summary':
                $url = "https://api.opendesign.dev/designs/" . $design . "/summary";
                break;
            default:
                $url = "https://api.opendesign.dev/designs";
        }
        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$this->token}"
        ]);
        return $this->execCURL($curl);
    }

    public function getArtboard($designId, $artboardId): array
    {
        $url = "https://api.opendesign.dev/designs/" . $designId . "/artboards/" . $artboardId . "/content";
//        if ('' /*Als je het octopus bestand wil downloaden*/) {
//            $url .= "/content";
//        }
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$this->token}"
        ]);
        return $this->execCURL($curl);
    }


    public function getInfo($designId): array
    {
        $url = "https://api.opendesign.dev/designs/"  . $designId;
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$this->token}"
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        return $this->execCURL($curl);
    }
}