<?php
require_once '../users/init.php';
require_once $abs_us_root.$us_url_root.'users/includes/header.php';
require_once $abs_us_root.$us_url_root.'users/includes/navigation.php';
if(isset($user) && $user->isLoggedIn()){
}
    ?>
<div id="page-wrapper">
<?php
$stmt = $pdo->prepare('SELECT * FROM `product` WHERE id=?;');
$stmt->bindParam(1, $_GET["id"], PDO::PARAM_STR);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_OBJ);
if ($stmt->rowCount() != 0) {
    echo "
<form class=\"paypal\" action=\"payments.php\" method=\"post\" id=\"paypal_form\">
    <input type=\"hidden\" name=\"cmd\" value=\"_xclick\" />
    <input type=\"hidden\" name=\"no_note\" value=\"1\" />
    <input type=\"hidden\" name=\"lc\" value=\"US\" />
    <input type=\"hidden\" name=\"bn\" value=\"PP-BuyNowBF:btn_buynow_LG.gif:NonHostedGuest\" />
    <input type=\"hidden\" name=\"first_name\" value=\"".$user->data()->fname." \">
    <input type=\"hidden\" name=\"last_name\" value=\"".$user->data()->lname." \">
    <input type=\"hidden\" name=\"payer_email\" value=\"".$user->data()->email." \">
    <input type=\"hidden\" name=\"item_number\" value=\"".$result->id." \" >
        <input type=\"hidden\" name=\"product_price\" value=\"".$result->product_price." \" >
            <input type=\"hidden\" name=\"product_name\" value=\"".$result->product_name." \" >
             <input type=\"hidden\" name=\"buyer_id\" value=\"".$user->data()->id." \" >
    <input type=\"submit\" name=\"submit\" value=\"Submit Payment\"/>
</form>
        ";
} else {
    echo "page not found!";
} ;
?>
</div>

    <!-- footers -->
<?php require_once $abs_us_root.$us_url_root.'users/includes/page_footer.php'; // the final html footer copyright row + the external js calls ?>

    <!-- Place any per-page javascript here -->


<?php require_once $abs_us_root.$us_url_root.'users/includes/html_footer.php'; // currently just the closing /body and /html ?>