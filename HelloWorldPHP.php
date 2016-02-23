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
        self::getLanguages();
        self::checkInputData();
    }

    /**
     *
     * select all languages code from tables
     */

    protected function getLanguages()
    {
//        self::$languages = '["af","sq","ar","hy","az","eu","be","bn","bs","bg","ca","ceb","ny","zh-CN","zh-TW","hr","cs","da","nl","en","eo","et","tl","fi","fr","gl","ka","de","el","gu","ht","ha","iw","hi","hmn","hu","is","ig","id","ga","it","ja","jw","kn","kk","km","ko","lo","la","lv","lt","mk","mg","ms","ml","mt","mi","mr","mn","my","ne","no","fa","pl","pt","ma","ro","ru","sr","st","si","sk","sl","so","es","su","sw","sv","tg","ta","te","th","tr","uk","ur","uz","vi","cy","yi","yo","zu"]';
        self::$languages = '["arabic","bulgarian","catalan","chinese-simplified","chinese-traditional","czech","danish","dutch","english",
                             "estonian","finnish","french","german","greek","haitian-creole","hebrew","hindi","hungarian","indonesian",
                             "italian","japanese","klingon","korean","latvian","lithuanian","malay","maltese","norwegian","persian",
                             "polish","portuguese","romanian","russian","slovak","slovenian","spanish","swedish","thai","turkish",
                             "ukrainian","urdu","vietnamese","welsh"]';

    }


    /**
     *
     * check input GET data
     */
    protected function checkInputData()
    {
        if (count($_GET) == 1) {
            if ($_GET['token'] == md5(self::TOKEN_INPUT)) {
                self::setSuccessResultContent();
            } else {
                self::setErrorResultContent('Wrong input token', 'tokenInvalid');
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
        $content = file_get_contents("http://translate.reference.com/english/" . self::getLanguageTranslate() . "/hello-world/tSGVsbG8gV29ybGQ%3D");
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
        $languageCodes = json_decode(self::$languages, true);
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
