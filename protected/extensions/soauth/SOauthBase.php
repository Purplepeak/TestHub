<?php

abstract class SOauthBase extends CComponent
{

    public $attributes = array();

    public $title;

    public $type;

    public $provider;

    protected $providerUrl;

    protected $clientId;

    protected $clientSecret;

    protected $redirectUrl;

    protected $providerOptions = array();

    protected $providerAuthorizeUrl;

    protected $providerAccessUrl;

    protected $errorParam = 'error';

    protected $authenticated = 'false';

    protected $response;

    protected $gender = array(
        'female' => 1,
        'male' => 2,
        'undefined' => 3
    );

    /**
     *
     * @var string Error description key name in _GET options.
     */
    protected $errorDescriptionParam = 'error_description';

    /**
     *
     * @var string Error code for access_denied response.
     */
    protected $errorAccessDeniedCode = 'access_denied';

    public function init($provider, $gender)
    {
        $this->redirectUrl = Yii::app()->request->hostInfo . Yii::app()->request->requestUri;
        $this->clientId = Yii::app()->params['socialKeys'][$provider]['clientId'];
        $this->clientSecret = Yii::app()->params['socialKeys'][$provider]['clientSecret'];
        
        if (! empty($gender)) {
            $this->gender = $gender;
        }
    }

    public function authenticate()
    {
        if (isset($_GET[$this->errorParam])) {
            $error_code = $_GET[$this->errorParam];
            if ($error_code === $this->errorAccessDeniedCode) {
                // access_denied error (user canceled)
                Yii::app()->request->redirect(Yii::app()->request->hostInfo);
            } else {
                $error = $error_code;
                if (isset($_GET[$this->errorDescriptionParam])) {
                    $error = $_GET[$this->errorDescriptionParam] . ' (' . $error . ')';
                }
                throw new SOauthException($error);
            }
            return false;
        }
        
        // Get the access_token and save them to the session.
        if (isset($_GET['code'])) {
            $code = $_GET['code'];
            $response = $this->getServiceResponse($code);
            if (isset($response)) {
                $this->authenticated = true;
                $this->response = $response;
            }
        } else {
            // Use the URL of the current page as the callback URL.
            if (isset($_GET['redirect_uri'])) {
                $redirect_uri = $this->redirectUrl;
            } else {
                $redirect_uri = $this->redirectUrl;
            }
            $url = $this->getCode($redirect_uri);
            Yii::app()->request->redirect($url);
        }
        
        return $this->authenticated;
    }

    public function getCode($redirect_uri)
    {
        return $this->providerAuthorizeUrl . '?client_id=' . $this->clientId . '&redirect_uri=' . urlencode($redirect_uri) . '&response_type=code';
    }

    public function getServiceResponse($code)
    {
        if (strpos($this->redirectUrl, '?') !== false) {
            $urlString = explode('?', $this->redirectUrl);
            $this->redirectUrl = $urlString[0];
        }
        $response = $this->makeRequest($this->getTokenUrl($code), array(), false);
        
        return $response;
    }

    public function getTokenUrl($code)
    {
        return $this->providerAccessUrl . '?client_id=' . $this->clientId . '&redirect_uri=' . urlencode($this->redirectUrl) . '&client_secret=' . $this->clientSecret . '&code=' . $code;
    }

    public function getAttributes($params)
    {
        $attributes = $this->makeRequest($this->providerAttributesUrl . '?' . $params);
        return $attributes;
    }

    public function normalizeAttributes($provider, $id, $name, $surname, $gender, $url, $photo)
    {
        $info = array(
            'name' => $name,
            'surname' => $surname,
            'gender' => $gender,
            'photo' => $photo
        );
        
        return array(
            'provider' => $provider,
            'social_user_id' => $id,
            'url' => $url,
            'info' => json_encode($info)
        );
    }

    public function validateSocialModel($oauthModel)
    {
        $identity = new UserIdentity();
        $identity->userClass = $oauthModel;
        
        $identity->socialAuthenticate($oauthModel->provider, $oauthModel->social_user_id);
        
        if ($identity->errorCode === UserIdentity::ERROR_NONE) {
            Yii::app()->user->login($identity);
        }
        
        if ($identity->errorCode === UserIdentity::ERROR_UNKNOWN_IDENTITY) {
            
            $oauthModel->isNewRecord = true;
            
            if ($oauthModel->validate()) {
                return $oauthModel;
            } else {
                return false;
            }
        }
    }

    protected function makeRequest($url, $options = array(), $parseJson = true)
    {
        $ch = $this->initRequest($url, $options);
        
        $result = curl_exec($ch);
        $headers = curl_getinfo($ch);
        
        if (curl_errno($ch) > 0) {
            throw new SOauthException(curl_error($ch), curl_errno($ch));
        }
        
        if ($headers['http_code'] != 200) {
            Yii::log('Invalid response http code: ' . $headers['http_code'] . '.' . PHP_EOL . 'URL: ' . $url . PHP_EOL . 'Result: ' . $result, CLogger::LEVEL_ERROR, 'application.extensions.soauth');
            throw new SOauthException("Invalid response http code: {$headers['http_code']}", $headers['http_code']);
        }
        
        curl_close($ch);
        
        if ($parseJson) {
            $result = $this->parseJson($result);
        }
        
        return $result;
    }

    protected function initRequest($url, $options = array())
    {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        
        if (isset($options['data'])) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, urldecode(http_build_query($options['data'])));
        }
        
        curl_setopt($ch, CURLOPT_URL, $url);
        
        return $ch;
    }

    protected function parseJson($response)
    {
        try {
            $result = json_decode($response);
            $error = $this->fetchJsonError($result);
            if (! isset($result)) {
                throw new SOauthException('Invalid response format.', 500);
            } else {
                if (isset($error) && ! empty($error['message'])) {
                    throw new SOauthException($error['message'], $error['code']);
                } else {
                    return $result;
                }
            }
        } catch (Exception $e) {
            throw new SOauthException($e->getMessage(), $e->getCode());
        }
    }

    protected function fetchJsonError($json)
    {
        if (isset($json->error)) {
            return array(
                'code' => 500,
                'message' => 'Unknown error occurred.'
            );
        } else {
            return null;
        }
    }

    protected function normalizeGender($gender)
    {
        if ($gender == 1) {
            $gender = $this->gender['female'];
        } elseif ($gender == 2) {
            $gender = $this->gender['male'];
        } else {
            $gender = $this->gender['undefined'];
        }
        
        return $gender;
    }
}
