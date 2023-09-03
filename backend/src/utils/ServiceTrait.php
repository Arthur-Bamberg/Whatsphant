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
            if(empty($method[$param])) {
                throw new \Exception('Missing parameter: ' . $param, 400);
            }

            $response[$param] = $method[$param];
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
}