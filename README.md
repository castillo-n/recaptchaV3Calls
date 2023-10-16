# recaptchaV3Calls
Making an easy out of the box calls to using googles recaptcha v3 everywhere. 

There are 3 static calls. 

backendPHPVerificationCall
Pretty explicit. You just use it to verify the recaptcha string.
You can send a captcha if you dont want to use the $_POST or leave it as an empty string, which then will trigger the function to search for the value of the recaptcha inside of the $_POST
As a default the score value to pass is .6, I think googles is .5 you can change it by passing a second parameter with a float value between 0 to 1, the lower you go the easier it gets for a bot to pass it 

jsCallInsert
Pretty explicit. You just use it to submit the form to the back end and create the captcha

htmlInsideTheFormCallInsert
Pretty explicit :). You just use it create the button that google needs to generate the recaptcha when the submit button is pressed, this is important as you have two minutes from the creation to verification, which includes in between the submit step as well. If 2 minutes have passed between the origination of the captcha and the verification of that captcha then you are out of luck and you will have to resubmit the form with a new captcha. This makes it less frustruating to those that do not know about such issue to not have to wonder about why is their form not submitting correctly as it add the captcha creation to the submit button. 

The important values to add to your .env file
GOOGLE_RECAPTCHA_SECRET
GOOGLE_RECAPTCHA_KEY
GOOGLE_RECAPTCHA_SCORE
names are pretty explicit. 
secret is the secret key
key is the public key
and score, is the score you want the user to be over to submit the form correctly 
If you don't want to use this variables on your env file, then just passed them as a value on the function calls. 
The following are the function calls and their parameters.
htmlInsideTheFormCallInsert($buttonClasses = 'btn btn-light', $buttonText = "Submit", $key = null)
jsCallInsert($formId = null, $action = null, $key = null) 
backendPHPVerificationCall($captcha = '', $wantedScoreValue = 0.7, $secret_key = null)

To implement it you need to use

use RecaptchaV3Calls\Recaptcha;

Then call any of the functions, as they are static functions, so you will have to call them in someway like: 
In the HTML, Blade, Twig file, etc (tags may change based on the platform you are using) but on something like the following (this cant be use on .html or .htm files)

<?php echo Recaptcha::htmlInsideTheFormCallInsert('btn btn-light form-control'); ?>
<?php echo Recaptcha::jsCallInsert("my_awesome_form"); ?>

and then in your back end

$didTheCaptchaPassed_thisIsABoolean = Recaptcha::backendPHPVerificationCall($captcha); //then do whatever you need to do with the verification. the return has two parameters ['success' => true||false, 'error' => ''||'the error description']

