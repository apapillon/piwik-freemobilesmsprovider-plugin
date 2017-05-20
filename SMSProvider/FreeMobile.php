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
use Piwik\Plugins\MobileMessaging\APIException;
use Piwik\Plugins\MobileMessaging\SMSProvider;
use Psr\Log\LoggerInterface;

/**
 * Add FreeMobile to SMS providers
 */
class FreeMobile extends \Piwik\Plugins\MobileMessaging\SMSProvider
{

    const API_URL = 'https://smsapi.free-mobile.fr/sendmsg';
    const SOCKET_TIMEOUT = 15;

    /**
     * @var LoggerInterface
     */
    private $logger;
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function getId()
    {
        return 'FreeMobile';
    }

    public function getDescription()
    {
        return 'Free Mobile is a French network operator that provide free SMS notifications option. You can use it to send SMS Reports from Piwik.<br/>
    <ul>
        <li>First, activate option on your account settings</li>
        <li>Enter your FreeMobile user on user key field</li>
        <li>Enter your password on password field.</li>
    </ul>
    <br/>
    <em>About Free Mobile</em>
    <ul>
        <li>Free Mobile send SMS only to the contract owner.</li>
        <li>Free Mobile is a French Network Operator.</li>
    </ul>';
    }

    public function getCredentialFields()
    {
    	return array(
    			array(
    					'type'  => 'text',
    					'name'  => 'username',
    					'title' => 'MobileMessaging_UserKey'
    			),
    			array(
    					'type'  => 'text',
    					'name'  => 'password',
    					'title' => 'General_Password'
    			),
    	);
    }

    public function verifyCredential($credential)
    {
        if (!isset($credential['username']) || !isset($credential['password'])) {
            throw new APIException(
                'API key must to contain the user and password separate by space.'
            );
        }
        /* Send SMS with test message */
        $this->sendSMS($credential, 'This is a test message from Piwik', null, null);

        return true;
    }

    public function sendSMS($credential, $smsText, $phoneNumber, $from)
    {
        $parameters = array(
            'user' => $credential['username'],
            'pass' => $credential['password'],
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