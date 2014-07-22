<?php
require_once dirname(dirname(__FILE__)) . '/SOauthBase.php';

class MailRuService extends SOauthBase
{

    public $provider = 'mail';

    public $title = 'Mail.ru';

    protected $providerUrl = 'http://vk.com/';

    protected $providerAuthorizeUrl = 'https://connect.mail.ru/oauth/authorize';

    protected $providerAccessUrl = 'https://connect.mail.ru/oauth/token';

    protected $providerAttributesUrl = 'http://www.appsmail.ru/platform/api';

    public function socialAttributes()
    {
        $response = $this->response;
        
        if (isset($response->access_token)) {
            $sign = md5("app_id={$this->clientId}method=users.getInfosecure=1session_key={$response->access_token}" . $this->clientSecret);
            
            $params = array(
                'method' => 'users.getInfo',
                'secure' => '1',
                'app_id' => $this->clientId,
                'session_key' => $response->access_token,
                'sig' => $sign
            );
        }
        
        $attributes = $this->getAttributes(urldecode(http_build_query($params)));
        $attributes = $attributes[0];
        $attributes->sex = $this->normalizeGender($attributes->sex);
        
        $attributes = $this->normalizeAttributes($this->provider, $attributes->uid, $attributes->first_name, $attributes->last_name, $attributes->sex, $attributes->link, $attributes->pic_50);
        
        return $attributes;
    }

    public function getServiceResponse($code)
    {
        if (strpos($this->redirectUrl, '?') !== false) {
            $urlString = explode('?', $this->redirectUrl);
            $this->redirectUrl = $urlString[0];
        }
        
        $params = array(
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $this->redirectUrl
        );
        
        $response = $this->makeRequest($this->providerAccessUrl, array(
            'data' => $params
        ));
        
        return $response;
    }
}