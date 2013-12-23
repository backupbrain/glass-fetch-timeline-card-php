<?php
// classes/Google_Userinfo.class.php
require_once('HttpPost.class.php');
require_once('Google_Timeline_Item.class.php');

/**
 * Get the Google+ user account from
 * https://www.googleapis.com/oauth2/v1/userinfo
 * 
 * Requires that user has been OAuth authenticated
 */
class Google_Timeline {
	
	const URL = 'https://www.googleapis.com/mirror/v1/timeline/';
	public $fetched = false;
	
	// this is the scope required to access the userinfo
	public static $scopes = array(
		'https://www.googleapis.com/auth/glass.timeline',
		'https://www.googleapis.com/auth/glass.location'
	);
	
	
	// we can only grab userinfo from an authenticated user
	public $Google_OAuth2_Token;
	
	
	// This is what we send to the server
	public $bundleId,
		$includeDeleted,
		$maxResults,
		$orderBy,
		$pageToken,
		$pinnedOnly,
		$sourceItemId;
		
	// this will come back from the server
	public $kind,
		$nextPageToken,
		$items;

	
		
	/**
	 * Use the authenticated Google_OAuth2_Token
	 */
	public function __construct($Google_OAuth2_Token) {
		$this->Google_OAuth2_Token = $Google_OAuth2_Token;
	}
	
	/**
	 * Fetch the Google+ profile information
	 */
	public function list_items() {
		
		if ($this->bundleId) $postData['bundleId'] = $this->bundleId;
		if ($this->includeDeleted) $postData['includeDeleted'] = $this->includeDeleted;
		if ($this->maxResults) $postData['maxResults'] = $this->maxResults;
		if ($this->orderBy) $postData['orderBy'] = $this->orderBy;
		if ($this->pageToken) $postData['pageToken'] = $this->pageToken;
		if ($this->pinnedOnly) $postData['pinnedOnly'] = $this->pinnedOnly;
		if ($this->sourceItemId) $postData['orderBy'] = $this->sourceItemId;
		
		$json = '';
		if ($postData) $json = json_encode($postData);

		
		// we will be stending the OAuth2 access_token through the HTTP headers
		$headers = array(
			'Authorization: '.$this->Google_OAuth2_Token->token_type.' '.$this->Google_OAuth2_Token->access_token
		);
		if ($json) $headers[] = 'Content-Type: application/json';
		if ($json) $headers[] = 'Content-length: '. strlen($json);
		
		$this->HttpPost = new HttpPost(self::URL);
		$this->HttpPost->setHeaders( $headers );
		if ($json) $this->HttpPost->setRawPostData( $json );
		
		if ($this->Google_OAuth2_Token->authenticated) {
			$this->HttpPost->send();
		    $response = json_decode($this->HttpPost->httpResponse);
		
		} else {
			throw new Exception ("Google_OAuth2_Token needs to be authenticated before you can fetch locations.");
		}

		
		
		// is there an error here?
		if ($response->error) {
			throw new Exception("The server reported an error: '".$response->error->errors[0]->message."'");
		} else {
			// we grabbed this from the locations API, which sends a list of locations
			// therefore we need to grap the first location
			$this->kind = $response->kind;
			$this->nextPageToken = $response->nextPageToken;
			
			
			if ($response->kind == "mirror#timeline") {
				foreach ($response->items as $timelineItem) {
					$TimelineItem = new Google_Timeline_Item($this->Google_OAuth2_Token);
					$TimelineItem->fromJSONObject($timelineItem);
					$this->TimelineItems[] = $TimelineItem;

				}
			}
			//print_r($this->TimelineItems);
			
			$this->fetched = true;
		}
	}
	
	
}

?>