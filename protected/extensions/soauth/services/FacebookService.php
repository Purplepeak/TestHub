<?php
require_once dirname(dirname(__FILE__)) . '/SOauthBase.php';

class FacebookService extends SOauthBase
{

    public $provider = 'facebook';

    public $title = 'Facebook';

    protected $providerAuthorizeUrl = 'https://www.facebook.com/dialog/oauth';

    protected $providerAccessUrl = 'https://graph.facebook.com/oauth/access_token';

    protected $providerAttributesUrl = 'https://graph.facebook.com/me';

    public function socialAttributes()
    {
        $accessToken = $this->response;
        
        $attributes = $this->getAttributes($accessToken);
        $attributes->photo = 'https://graph.facebook.com/' . $attributes->id . '/picture?' . $this->fbPictureSize;
        
        $attributes = $this->normalizeAttributes($this->provider, $attributes->id, $attributes->first_name, $attributes->last_name, $attributes->gender, $attributes->link, $attributes->photo);
        
        return $attributes;
    }
}