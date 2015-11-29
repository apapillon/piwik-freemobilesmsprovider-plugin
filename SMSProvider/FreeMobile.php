<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\FreeMobileMessaging\SMSProvider;

use Piwik\Http;
use Piwik\Plugins\MobileMessaging\SMSProvider;


/**
 * Add FreeMobile to SMS providers
 */
class FreeMobile extends \Piwik\Plugins\MobileMessaging\SMSProvider
{

    const API_URL = 'https://smsapi.free-mobile.fr/sendmsg';
    const SOCKET_TIMEOUT = 15;

    public function getId()
    {
        return 'FreeMobile';
    }

    public function getDescription()
    {
        return 'Free Mobile is a French network operator that provide free SMS notifications option. You can use it to send SMS Reports from Piwik.<br/>
    <ul>
        <li>First, activate option on your account settings</li>
        <li>Enter your FreeMobile user and password as API key separated by a whitespace.</li>
    </ul>
    <br/>
    <em>About Free Mobile</em>
    <ul>
        <li>Free Mobile send SMS only to the contract owner.</li>
        <li>Free Mobile is a French Network Operator.</li>
    </ul>';
    }

    public function verifyCredential($apiKey)
    {
        $account = explode(" ", $apiKey);
        if (2 != count($account)) {
            throw new APIException(
                'API key must to contain the user and password separate by space.'
            );
        }
        /* Send SMS with test message */
        $this->sendSMS($apiKey, 'This is a test message from Piwik', null, null);

        return true;
    }

    public function sendSMS($apiKey, $smsText, $phoneNumber, $from)
    {
        $account = explode(" ", $apiKey);
        $parameters = array(
            'user' => $account[0],
            'pass' => $account[1],
            'msg' => $smsText,
        );
        $url = self::API_URL . '?' . http_build_query($parameters, '', '&');
        $timeout = self::SOCKET_TIMEOUT;
        $result = Http::sendHttpRequestBy(
            Http::getTransportMethod(),
            $url,
            $timeout,
            $getExtendedInfo = true
        );
    }

    public function getCreditLeft($apiKey)
    {
        return '';
    }
}
