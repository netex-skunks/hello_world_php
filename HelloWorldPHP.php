<?php
/**
 * Created by PhpStorm.
 * User: Netex
 * Date: 2/22/16
 * Time: 1:26 PM
 */

namespace Netex;


class HelloWorldPHP
{
    const TOKEN_INPUT = 'Netex';

    public static $resultContent;

    public static $languages;

    public static $toLanguage;


    public static function construct()
    {
        self::$resultContent = array ();
        self::$languages = __DIR__ . "/languages";
        self::checkInputData();
    }


    /**
     *
     * check input GET data
     */
    protected function checkInputData()
    {
        if (count($_GET) == 1) {
            if (array_key_exists("token", $_GET)) {
                if ($_GET['token'] == md5(self::TOKEN_INPUT)) {
                    self::setSuccessResultContent();
                } else {
                    self::setErrorResultContent('Wrong input token', 'tokenInvalid');
                }
            } else {
                self::setErrorResultContent('Bad request', 'usageLimits');
            }
        } else {
            self::setErrorResultContent('Bad request', 'usageLimits');
        }
        self::getResultContent();
    }


    /**
     * @param $message String;
     * @param $reason String;
     * set result content with error
     * */
    protected function setErrorResultContent($message, $reason)
    {
        http_response_code(400);
        self::$resultContent['error']['errors']['reason'] = $reason;
        self::$resultContent['error']['errors']['message'] = $message;
        self::$resultContent['error']['code'] = '400';
        self::$resultContent['error']['message'] = 'Bad request';

    }


    /**
     * set result content with success
     */
    protected function setSuccessResultContent()
    {
        $content = file_get_contents("http://translate.reference.com/english/" . self::getLanguageTranslate() . "/hello-world");
        preg_match('/placeholder="Translation".*<\//', $content, $result);
        preg_match('/>.*</', $result[0], $resultWord);
        $result = str_replace(">", "", $resultWord[0]);
        $word = str_replace("<", "", $result);
        http_response_code(200);
        self::$resultContent['success']['message'] = 'success request';
        self::$resultContent['success']['code'] = '200';
        self::$resultContent['message'] = $word;
    }

    /**
     * get language rand translate
     */
    protected function getLanguageTranslate()
    {
        $languageCodes = json_decode(file_get_contents(self::$languages), true);
        $languageTo = $languageCodes[array_rand($languageCodes)];
        return $languageTo;
    }

    /**
     * get result json content
     */
    protected function getResultContent()
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(self::$resultContent, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

}

\Netex\HelloWorldPHP::construct();
