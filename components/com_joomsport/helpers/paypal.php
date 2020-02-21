<?php
/*------------------------------------------------------------------------
# JoomSport Professional 
# ------------------------------------------------------------------------
# BearDev development company 
# Copyright (C) 2011 JoomSport.com. All Rights Reserved.
# @license - http://joomsport.com/news/license.html GNU/GPL
# Websites: http://www.JoomSport.com 
# Technical Support:  Forum - http://joomsport.com/helpdesk/
-------------------------------------------------------------------------*/
// no direct access
defined('_JEXEC') or die;

class JHtmlPaypal
{
    public static function getPaypalForm($options, $cap)
    {
        $options->paypalcur_on = '1';
        $options->paypalval_on = '1';
        $options->paypalval_enteramount = '';
        $options->paypalbuttontext = '';
        $options->paymenttype = '2';
        $options->paymentlocation = '';

        $html = '';
        $length = isset($_POST[ 'paypallength' ]) ? (int) $_POST[ 'paypallength' ] : '';
        $amount = isset($_POST[ 'paypalamount' ]) ? trim($_POST[ 'paypalamount' ]) : '';
        $amount = str_replace(',', '.', $amount);

        if ($amount < $options->paypalvalleast_val) {
            $amount = $options->paypalvalleast_val;
        }
        $currency_code = isset($_POST[ 'paypalcurrency_code' ]) ? trim($_POST[ 'paypalcurrency_code' ]) : 0;

        $header = '';

        if ($length >= 1 && $length <= 4) {
            if ($options->paymenttype == 2) { //sandbox.paypal - test
                $header = 'Location: https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business='.$options->paypal_email.'&item_name='.$options->paypal_org.'&amount='.$amount.'&no_shipping=0&no_note=1&tax=0&currency_code='.$currency_code.'&bn=PP%2dBuyNowBF&charset=UTF%2d8&return='.$options->paypalreturn.'&cancel_return='.$options->paypalcancel.'&rm=2&notify_url='.$options->notifyurl;
            }

            if ($options->paymentlocation != '') {
                $header = $header.'&lc='.$options->paymentlocation;
            }
            header($header);
        }

        $currencies = array('CAD' => '$', 'USD' => '$', 'GBP' => '£', 'AUD' => '$', 'JPY' => '&yen;', 'EUR' => '&euro;', 'CHF' => 'CHF', 'CZK' => 'CZK', 'DKK' => 'DKK', 'HKD' => '$', 'HUF' => 'HUF', 'NOK' => 'NOK', 'NZD' => '$', 'PLN' => 'PLN', 'SEK' => 'SEK', 'SGD' => '$');

        $html .= '<form action="'.$_SERVER['REQUEST_URI'].'" method="post">';
        if ($options->paypalval_on == 0) {
            $javaScript = <<< JAVASCRIPT
                <script type="text/javascript">
                  function donateChangeCurrency( )
                  {
                    var selectionObj = document.getElementById( 'donate_currency_code' );
                    var selection = selectionObj.value;
                    var currencyObj = document.getElementById( 'donate_symbol_currency' );
                    if( currencyObj )
                    {
                      var currencySymbols = { 'CAD': '$', 'USD': '$', 'GBP': '&pound;', 'AUD': '$', 'JPY': '&yen;', 'EUR': '&euro;', 'CHF': 'CHF', 'CZK' : 'CZK', 'DKK' : 'DKK', 'HKD' : '$', 'HUF' : 'HUF', 'NOK' : 'NOK', 'NZD' : '$', 'PLN' : 'PLN', 'SEK' : 'SEK', 'SGD' : '$' };
                      var currencySymbol = currencySymbols[ selection ];
                      currencyObj.innerHTML = currencySymbol;
                    }
                  }
                </script>
JAVASCRIPT;

            $symbol = $currencies[ $options->paypalcur_val ];
            $html .=  "$javaScript<p>".$options->paypalval_enteramount.'</p><p><span id="donate_symbol_currency">'.$symbol.'</span><input type="text" name="paypalamount" size="5" class="inputbox">';
        } elseif ($options->paypalval_on == 1) {
            $html .=  '<input type="hidden" value="'.$options->paypalval_val.'" name="paypalamount">';
        }
        if ($options->paypalcur_on == 0) {
            $html .= '<select name="paypalcurrency_code" id="donate_currency_code" class="inputbox" onchange="donateChangeCurrency();">';
            foreach ($currencies as $currency => $dummy) {
                $selected = ($currency == $options->paypalcur_val) ? ' selected="selected"' : '';
                $html .= "<option value=\"$currency\"$selected>$currency</option>\n";
            }
            $html .= "</select>\n";
        } elseif ($options->paypalcur_on == 1) {
            $html .= '<input type="hidden" name="paypalcurrency_code" value="'.$options->paypalcur_val.'">';
        }
        $html .= $cap;
        $html .= '  <input type="hidden" name="paypallength" value="1"  />
            <input type="hidden" name="rm" value="2">
            <input type="hidden" name="pay" value="1">
            <button class="send-button" type="submit"><span><b>'.JText::_('BL_JOINSEAS').'</b></span></button>
        </form>
        ';

        return $html;
    }
}
