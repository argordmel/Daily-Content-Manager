<html><body>
<?
require_once ("recaptchalib.php");

// get a key at http://www.google.com/recaptcha/mailhide/apikey
$mailhide_pubkey = '01xW6TbrVIHRYwv6KGFXFB5g==';
$mailhide_privkey = 'a9cf79e22f9020aa2ea85de6144623e1';

?>

The Mailhide version of example@example.com is
<? echo recaptcha_mailhide_html ($mailhide_pubkey, $mailhide_privkey, "example@example.com"); ?>. <br>

The url for the email is:
<? echo recaptcha_mailhide_url ($mailhide_pubkey, $mailhide_privkey, "example@example.com"); ?> <br>

</body></html>
