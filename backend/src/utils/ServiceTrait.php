<?php

namespace Util;

trait ServiceTrait {
    private static function validateParams($params, $isPost = false) {
        if($isPost) {
            $method = $_POST;
        } else {
            $method = $_GET;
        }

        $response = [];

        foreach($params as $param) {
            if($param == 'img' && empty($method[$param])) {
                $response[$param] = 'https://digimedia.web.ua.pt/wp-content/uploads/2017/05/default-user-image.png';

            } else if($param == 'img') {
                $response[$param] = self::convertBase64ToFile($method[$param]);

            } else {
                if(empty($method[$param])) {
                    throw new \Exception('Missing parameter: ' . $param, 400);
                }

                $response[$param] = $method[$param];
            }
        }

        return $response;
    }

    private static function getUriParameter($getLastTwoParameters = false) {
        $uri = $_SERVER['REQUEST_URI'];

        $uri = explode('/', $uri);

        if($getLastTwoParameters) {
            $return = [];
            $return[] = $uri[count($uri) - 2];
            $return[] = $uri[count($uri) - 1];

            return $return;
        }

        $uri = explode('?', end($uri));

        return $uri[0];
    }

    private static function convertBase64ToFile($imgBase64) {
        $imgBase64 = explode(',', $imgBase64);

        $imgBase64 = end($imgBase64);

        $imgBase64 = str_replace(' ', '+', $imgBase64);

        $imgBase64 = base64_decode($imgBase64);

        $imgName = md5(uniqid(rand(), true)) . '.png';

        $imgPath = __DIR__ . '/../uploads/' . $imgName;

        file_put_contents($imgPath, $imgBase64);

        return $imgPath;
    }
}