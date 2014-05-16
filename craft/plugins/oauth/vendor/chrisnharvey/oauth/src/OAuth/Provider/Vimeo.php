<?php

namespace OAuth\Provider;

use \OAuth\OAuth2\Token\Access;

class Vimeo extends \OAuth\OAuth2\Provider
{
    public $name = 'vimeo';
    protected $scope = array('public', 'private');
    public $method = 'POST';

    public $scope_seperator = ' ';

    public function requestTokenUrl()
    {
        return 'https://api.vimeo.com/oauth/request_token';
    }

    public function authorizeUrl()
    {
        return 'https://api.vimeo.com/oauth/authorize';
    }

    public function accessTokenUrl()
    {
        return 'https://api.vimeo.com/oauth/access_token';
    }

    public function getUserInfo()
    {
        $url = 'https://api.vimeo.com/me';

        $cp = curl_init();


        curl_setopt($cp, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($cp, CURLOPT_URL, $url);
        curl_setopt($cp, CURLOPT_TIMEOUT, 60);
        curl_setopt($cp, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($cp, CURLOPT_SSL_VERIFYPEER, false);

        $auth = array(
            "Authorization: bearer " . $this->token->access_token,
        );

        curl_setopt($cp, CURLOPT_HTTPHEADER,$auth);


        // unset($params['client_id']);
        // unset($params['client_secret']);

        $response = curl_exec($cp);
        $errors = curl_error($cp);
        $errorsNo = curl_errno($cp);
        //var_dump($auth, $params, $errorsNo, $errors, $response);
        curl_close($cp);

        $return = json_decode($response, true);

        $image = false;

        if(!empty($return['pictures'][2]['link']))
        {
            $image = $return['pictures'][2]['link']; // 100x100
        }

        return array(
            'uid' => substr($return['uri'], strlen('/users/')),
            'name' => $return['name'],
            'location' => $return['location'],
            'description' => $return['bio'],
            'image' => $image
        );
    }



    /*
    * Get access to the API
    *
    * @param    string  The access code
    * @return   object  Success or failure along with the response details
    */
    public function access($code, $options = array())
    {
        $params = array(
            'client_id'     => $this->client_id,
            'client_secret' => $this->client_secret,
            'grant_type'    => isset($options['grant_type']) ? $options['grant_type'] : 'authorization_code',
        );

        $params = array_merge($params, $this->params);

        switch ($params['grant_type']) {
            case 'authorization_code':
                $params['code'] = $code;
                $params['redirect_uri'] = isset($options['redirect_uri']) ? $options['redirect_uri'] : $this->redirect_uri;
            break;

            case 'refresh_token':
                $params['refresh_token'] = $code;
            break;
        }

        $response = null;

        $url = $this->accessTokenUrl();

        //var_dump($params);

        // unset($params['client_id']);
        // unset($params['client_secret']);

        switch ($this->method) {
            case 'GET':


                // Need to switch to Request library, but need to test it on one that works
                $url .= '?'.http_build_query($params);

                $cp = curl_init();
                curl_setopt($cp, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($cp, CURLOPT_URL, $url);
                curl_setopt($cp, CURLOPT_TIMEOUT, 60);
                $response = curl_exec($cp);
                curl_close($cp);

                parse_str($response, $return);

            break;

            case 'POST':

                $opts = array(
                    'http' => array(
                        'method'  => 'POST',
                        'header'  => 'Content-type: application/x-www-form-urlencoded',
                        'content' => http_build_query($params),
                    )
                );

                $cp = curl_init();


                curl_setopt($cp, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($cp, CURLOPT_URL, $url);
                curl_setopt($cp, CURLOPT_TIMEOUT, 60);
                curl_setopt($cp, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($cp, CURLOPT_SSL_VERIFYPEER, false);

                $auth = array(
                    "Authorization: basic " . base64_encode($params['client_id'] . ":" . $params['client_secret']),
                    'Content-type: application/x-www-form-urlencoded',
                );

                curl_setopt($cp, CURLOPT_HTTPHEADER,$auth);


                // unset($params['client_id']);
                // unset($params['client_secret']);

                curl_setopt($cp, CURLOPT_POSTFIELDS, http_build_query($params));

                $response = curl_exec($cp);
                $errors = curl_error($cp);
                $errorsNo = curl_errno($cp);
                //var_dump($auth, $params, $errorsNo, $errors, $response);
                curl_close($cp);

                $return = json_decode($response, true);

            break;

            default:
                throw new \OutOfBoundsException("Method '{$this->method}' must be either GET or POST");
        }

        if ( ! empty($return['error'])) {
            throw new Exception($return);
        }

        switch ($params['grant_type']) {
            case 'authorization_code':
                return new Access($return);
            break;

            case 'refresh_token':
                return new Access($return);
            break;
        }

    }

} // End Provider_Vimeo
