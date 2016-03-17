<?php
/**
 * Created by PhpStorm.
 * User: Netex
 * Date: 2/22/16
 * Time: 1:26 PM
 */

namespace Netex;

final class HelloWorldPHP
{
    const TOKEN_INPUT = 'Netex';

    /**
     * @var string [] $resultContent
     */
    private $resultContent = array();

    /**
     * @var string
     */
    private static $languagesFileLocation = __DIR__ . "/languages";

    /**
     * HelloWorldPHP constructor.
     */
    function __construct()
    {
        $this->checkInputData();
    }

    /**
     *
     * check input GET data
     */
    private function checkInputData()
    {
        if (count($_GET) == 1) {
            if (array_key_exists("token", $_GET)) {
                if ($_GET['token'] == md5(self::TOKEN_INPUT)) {
                    $this->setSuccessResultContent();
                } else {
                    $this->setErrorResultContent('Wrong input token', 'tokenInvalid');
                }
            } else {
                $this->setErrorResultContent('Bad request', 'usageLimits');
            }
        } else {
            $this->setErrorResultContent('Bad request', 'usageLimits');
        }
        $this->getResultContent();
    }


    /**
     * @param $message String;
     * @param $reason String;
     * set result content with error
     * */
    private function setErrorResultContent($message, $reason)
    {
        http_response_code(400);
        $this->resultContent['error']['errors']['reason'] = $reason;
        $this->resultContent['error']['errors']['message'] = $message;
        $this->resultContent['error']['code'] = '400';
        $this->resultContent['error']['message'] = 'Bad request';

    }


    /**
     * set result content with success
     */
    private function setSuccessResultContent()
    {
        $translation = $this->getRandomTranslation("hello world");

        http_response_code(200);
        $this->resultContent['success']['message'] = 'success request';
        $this->resultContent['success']['code'] = '200';
        $this->resultContent['message'] = $translation;
        
    }

    /**
     * @param $text
     * @return mixed
     */
    private function getRandomTranslation($text)
    {
        $text = preg_replace('/\s/','-',$text);

        $language = self::getLanguageTranslate();
        $pageContent = file_get_contents("http://translate.reference.com/english/" . $language . "/".$text);

        preg_match('/placeholder="Translation".*<\//', $pageContent, $result);
        preg_match('/>.*</', $result[0], $result);

        $result = str_replace(">", "", $result[0]);

        $translation = str_replace("<", "", $result);

        return $translation;
    }

    /**
     * get language rand translate
     */
    private function getLanguageTranslate()
    {
        $languageCodes = json_decode(file_get_contents(self::$languagesFileLocation), true);
        $languageTo = $languageCodes[array_rand($languageCodes)];
        return $languageTo;
    }

    /**
     * get result json content
     */
    private function getResultContent()
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($this->resultContent, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

}

new HelloWorldPHP();
