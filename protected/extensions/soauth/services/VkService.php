<?php 

require_once dirname(dirname(__FILE__)) . '/SOauthBase.php';

class VkService extends SOauthBase {
	public $provider = 'vk';
	public $title = 'Vkontakte';
	protected $providerUrl = 'http://vk.com/';
	protected $providerAuthorizeUrl = 'http://oauth.vk.com/authorize';
	protected $providerAccessUrl = 'https://oauth.vk.com/access_token';
	protected $providerAttributesUrl = 'https://api.vk.com/method/users.get';
	
	public function socialAttributes()
	{
		$response = $this->response;
			
		$response = json_decode($response);
		$params = 'uids=' .$response->user_id. '&fields=uid,first_name,last_name,nickname,screen_name,sex,bdate,city,country,timezone,photo&access_token=' .$response->access_token;
		
    	$attributes = $this->getAttributes($params);
    	$attributes = $attributes->response[0];
    	
    	$attributes->sex = $this->normalizeGender($attributes->sex);
    	
    	$attributes = $this->normalizeAttributes(
    			$this->provider,
    			$attributes->uid,
    			$attributes->first_name,
    			$attributes->last_name,
    			$attributes->sex,
    			$this->providerUrl . $attributes->screen_name,
    			$attributes->photo
    	);
    	
    	return $attributes;
	}
}