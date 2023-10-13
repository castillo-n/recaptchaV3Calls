<?php

/**
* Created by Nelson Castillo
* 2019-06-25
*/

namespace recaptchaV3Calls;

class recaptchaV3
{
    public static function backendPHPVerificationCall($captcha = null, $score_val=0.8) {
        //if you dont have GOOGLE_RECAPTCHA_SECRET set on your .env file, please add it and this will start working
        $secret_key = getenv('GOOGLE_RECAPTCHA_SECRET');
        $score = (float) getenv('GOOGLE_RECAPTCHA_SCORE', 0); //if you set the score in the env the number will be included by default, values range from 0 to 1, not smaller than 0 nor larger than 1. Any sentence should be converted to 0 unless the string is just a number (float or not) 
        if($score === 0 or $score > 1 or $score < 0){
            $score = $score_val;
        }
        //if no captcha was passed (in case you do something in the backend before the verification and didnt unset itm then the post will grab it
        if ($captcha === null and isset($_POST['g-recaptcha-response'])) {
            $captcha = $_POST['g-recaptcha-response'];
        } else {
            $captcha = false;
        }
        //captcha doesnt exists or is to short deny validity of the captcha. 
        if (!$captcha){
            return ['success'=> false, 'error'=>'you didn\'t pass a captcha or the g-recaptcha-response exists'];
        }
        else if(strlen($captcha) < 20 ) {
            return ['success'=> false, 'error'=>'the captcha input captured was a string to short to be valid'];
        } else {
            $siteVerification = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $secret_key . "&response=" . $captcha . "&remoteip=" . $_SERVER['REMOTE_ADDR']);
            $siteVerification = json_decode($siteVerification);
            if ($siteVerification->success === false) {
                return ['success'=> false, 'error'=>'the recaptcha didn\'t pass google verification, or the time between the recaptcha verification and the submission of the form was longer than two minutes.'];
            }
        }
        
        //the captcha pass the verification and return a score, lets check if their score is something you approved
        if ($response->success==true && $response->score <= $score) {
            return ['success'=> false, 'error'=>'score is too low']
        }
        return ['success'=> true, 'error'=>''];
    }
    public static function jsCallInsert($key=null){
        $secret_key = getenv('GOOGLE_RECAPTCHA_KEY');
        if ($key === null) {
            $key = $secret_key
        }
        echo "<script src='https://www.google.com/recaptcha/api.js?render=".$key."'></script>".
            "<script>".
            "    grecaptcha.ready(function() {".
            "        grecaptcha.execute('".$key."', {action:'validate_captcha'})".
            "            .then(function(token) {".
            "                document.getElementById('g-recaptcha-response').value = token;".
            "            });".
            "    });".
            "</script>";
    }
    public static function htmlInsideTheFormCallInsert($key=null){
    }
}
