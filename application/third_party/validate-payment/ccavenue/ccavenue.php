<?php
/*
 * ONLY translate line 8 through 10.
 *
 * If you have any questions at all about this file, please contact me through my Support Center:
 * http://jennperrin.com/support/
 */


$processPay			= "Processing Payment...";
$plsWaitMsg			= "Please wait, your CCAvenue payment is being processed and you will be redirected to the CCAvenue website.";
$redirectMsg		= "You will be automatically redirected to CCAvenue to complete your payment within a few seconds...";
/* END */

include(dirname(__FILE__)."/Crypto.php");

class ccavenuePaymnents {
    var $response;
    var $payment_data = array();
    var $fields = array();
    var $base_url;
    var $ccavenue_url;

    public function __construct()
    {

    }

    function addField($field, $value) {
        $this->fields["$field"] = $value;
    }

    function setPaymentUrl($url){
        $this->ccavenue_url=$url;
    }

    function setBaseUrl($url){
        $this->base_url=$url;
    }

    function submit_post() {
        $processPay			= "Redirecting to CCAvenue...";
        $plsWaitMsg			= "Please wait, your CCAvenue payment is being processed and you will be redirected to the CCAvenue website.";
        $redirectMsg		= "You will be automatically redirected to CCAvenue to complete your payment within a few seconds...";
        // Submit to PayPal
        echo "<body onLoad=\"document.forms['ccavenue_form'].submit();\">\n";
        //echo "<body>";
        echo "<div style='position: relative;text-align: center;width: 100%; height: 100%; display: table;'>";
        echo "<div class='loading-box' style='display: table-cell;vertical-align: middle;width: 100%;'>";
        echo "<img src='".$this->base_url."assets/global/img/loading.gif' style='width: 300px; height: 15px;'>";
        echo "<h3>".@$processPay."</h3>";
        echo "<p class=\"mt-0\">".@$redirectMsg."</p>";
        echo "</div>";
        echo "</div>";
        //echo "<div class=\"alertMsg info mb-20\"><div class=\"msgIcon pull-left\"><i class=\"fa fa-paypal\"></i></div>".@$plsWaitMsg."</div>";
        echo "<form method=\"post\" name=\"ccavenue_form\" ";
        echo "action=\"".$this->ccavenue_url."\">\n";
        foreach ($this->fields as $name => $value) {
            echo "<input type=\"hidden\" name=\"$name\" value=\"$value\"/>\n";
        }
        echo "</form>\n";
    }
}
?>
