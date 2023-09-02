<?php

namespace Util;

trait ServiceTrait {
    private static function validateParams($params, $isPost = false) {
        if($isPost) {
            $method = '_POST';
        } else {
            $method = '_GET';
        }

        $response = [];

        foreach($params as $param) {
            if(empty($$method[$param])) {
                throw new \Exception('Missing parameter: ' . $param);
            }

            $response[$param] = $$method[$param];
        }

        return $response;
    }
}