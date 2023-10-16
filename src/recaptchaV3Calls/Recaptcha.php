<?php

/**
 * Created by Nelson Castillo
 * 2023-10-13
 */

namespace RecaptchaV3Calls;

class Recaptcha
{
    public static function backendPHPVerificationCall($captcha = '', $wantedScoreValue = 0.8)
    {
        //if you dont have GOOGLE_RECAPTCHA_SECRET set on your .env file, please add it and this will start working
        $secret_key = getenv('GOOGLE_RECAPTCHA_SECRET');
        $siteVerification = json_encode([]);
        $wantedScore = (float)getenv('GOOGLE_RECAPTCHA_SCORE', 0); //if you set the score in the env the number will be included by default, values range from 0 to 1, not smaller than 0 nor larger than 1. Any sentence should be converted to 0 unless the string is just a number (float or not)
        if ($wantedScore === 0 or $wantedScore > 1 or $wantedScore < 0) {
            $wantedScore = $wantedScoreValue;
        }
        //if no captcha was passed (in case you do something in the backend before the verification and didnt unset itm then the post will grab it
        if ($captcha === '' and isset($_POST['g-recaptcha-response'])) {
            $captcha = $_POST['g-recaptcha-response'];
            unset($_POST['g-recaptcha-response']);
        } else if ($captcha === '' || $captcha === null) {
            $captcha = false;
        }
        //captcha doesnt exists or is to short deny validity of the captcha.
        if ($captcha === false) {
            return ['success' => false, 'error' => 'You did not submit a captcha or the g-recaptcha-response does not exist ' . json_encode($captcha)];
        } else if (strlen($captcha) < 20) {
            return ['success' => false, 'error' => 'The captcha input captured was a string to short to be valid'];
        } else {
            $siteVerification = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $secret_key . "&response=" . $captcha . "&remoteip=" . $_SERVER['REMOTE_ADDR']);
            $siteVerification = json_decode($siteVerification);
            if ($siteVerification->success === false) {
                return ['success' => false, 'error' => 'The recaptcha did not pass google verification, or the time between the recaptcha verification and the form submission was longer than two minutes.'];
            }
        }
        //the captcha pass the verification and return a score, lets check if their score is something you approved
        if ($siteVerification->success === true && $siteVerification->score <= $wantedScore) {
            return ['success' => false, 'error' => 'The challenge score was low'];
        }
        return ['success' => true, 'error' => ''];
    }

    public static function jsCallInsert($formId = null, $action = null, $key = null)
    {
        $the_key = $key;
        if ($key === null) {
            $the_key = getenv('GOOGLE_RECAPTCHA_KEY');
        }
        if ($action === null) {
            $action = "submit";
        }
        if ($formId === null) {
            $js = 'document.querySelector("form").submit();';
        } else {
            $js = 'document.getElementById("' . $formId . '").submit();';
        }
        return "<script src='https://www.google.com/recaptcha/api.js?render=" . $the_key . "'></script>"
            . "<script>"
            . "function clearError() { document.getElementById('g-recaptcha-error').innerHTML=''; }"
            . "function submitForm(e) {"
            . "   $js"
            . "}"
            . "</script>";
    }

    public static function htmlInsideTheFormCallInsert($buttonClasses = 'btn btn-light', $buttonText = "Submit", $key = null)
    {
        $the_key = $key;
        if ($key === null) {
            $the_key = getenv('GOOGLE_RECAPTCHA_KEY');
        }
        return "<div id='g-catptcha-error'></div>"
            . "<button id='form-submit-button' class='g-recaptcha " . $buttonClasses . "'"
            . " data-sitekey='" . $the_key . "'"
            . " data-callback='submitForm' data-action='submit' type='submit'>" . $buttonText . "</button>";
    }
}
