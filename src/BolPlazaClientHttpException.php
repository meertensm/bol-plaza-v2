<?php
namespace MCS;

use Exception;

class BolPlazaClientHttpException extends Exception
{
    protected $errorCode;

    public function __construct($errorCode, $code = 0, Exception $previous = null) {

        $this->errorCode = $errorCode;

        $messages = [
            '41101' => 'The provided public key is unknown, blocked, blacklisted or for a different environment (test-app or live, which is based on the URL www.test-plazaapi.bol.com or www.plazaapi.bol.com).',
            '41102' => 'One of the mandatory request headers is missing or date header is not between +/- 15 minutes of the server time in GMT.',
            '41103' => 'Signature is not correctly calculated. (maybe the proxy, firewall, etc. changes one of the headers).',
            '41104' => 'The provided public key belongs to a non-active PAI seller.',
            '41201' => 'One of the mandatory tags is missing. E.g. for processing shipments or cancellations, at least a ‘Shipments’ or ‘Cancellations’ container tag must be present. It is also possible to provide Shipments and Cancellations in one call.',
            '41202' => 'The (numeric) value in element has a value x, which is too large. The maximum value is y.',
            '41301' => 'The URL path parameter ProcessOrderId is not an existing process order id.',
            '41401' => 'The URL path parameter YearMonth is not in the format yyyyMM.',
            '41501' => 'The Plaza partner exceeded the maximum number of requests within the period.',
            '41000' => 'Error occurred while processing your request. Please try again later.',
            '41100' => '<XSD validation error as thrown by the validator>',
            '41101' => 'Exceeded maximum length for offer id. max %s characters',
            '41200' => 'Access denied',
            '41201' => 'Request contains invalid authentication headers',
            '41202' => 'Client and server signature do not match',
            '41203' => 'You are not allowed to access this module',
            '41300' => 'Offer file ‘%s’ is still being processed',
            '41301' => 'Offer file ‘%s’ does not exist (anymore)',
            '41302' => 'Invalid filter used: %s. Valid filters are %s'
        ];

        $http_codes = [
            100 => 'Continue',
            101 => 'Switching Protocols',
            102 => 'Processing',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            207 => 'Multi-Status',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => 'Switch Proxy',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            418 => 'I\'m a teapot',
            422 => 'Unprocessable Entity',
            423 => 'Locked',
            424 => 'Failed Dependency',
            425 => 'Unordered Collection',
            426 => 'Upgrade Required',
            449 => 'Retry With',
            450 => 'Blocked by Windows Parental Controls',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            506 => 'Variant Also Negotiates',
            507 => 'Insufficient Storage',
            509 => 'Bandwidth Limit Exceeded',
            510 => 'Not Extended'
        ];

        $errorCode = (string) $errorCode;
        if (isset($messages[$errorCode])) {
            $errorCode = $messages[$errorCode];
        } elseif (isset($http_codes[$errorCode])) {
            $errorCode = $http_codes[$errorCode];
        }
        parent::__construct($errorCode, $code, $previous);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function getErrorCode()
    {
        return $this->errorCode;
    }
}
