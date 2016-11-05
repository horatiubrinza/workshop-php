<?php

namespace ZWorkshop;


class EmotionAPI
{
    const ENDPOINT = 'https://api.projectoxford.ai/emotion/v1.0/recognize';

    const API_KEY = '383ce9382c0e437293003c8328319ccc';


    public function analyze($image, $isUrl = false, $faceRectangles = false)
    {
        $ch = curl_init();

        $apiUrl = self::ENDPOINT;
        if ($faceRectangles) {
            $apiUrl .=  '?faceRectangles=1';
        }

        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, $ch);

        $headers = array(
            'Ocp-Apim-Subscription-Key' => self::API_KEY,
        );

        $headers['Content-Type'] = $isUrl ? 'application/json' : 'application/octet-stream';

        curl_setopt($ch, CURLOPT_HEADER, $headers);

        curl_setopt($ch, CURLOPT_POST, true);

        if ($isUrl) {
            $data = [
                'url' => $image,
            ];
            $data = json_encode($data);
        } else {
            $data = [
                'file' => '@' . $image,
            ];
        }


        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $response = curl_exec($ch);

        return var_export($response,1);

    }
}
