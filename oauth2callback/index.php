<?php
// oauth2callback/index.php


require('../settings.php');

require_once('../classes/Google_OAuth2_Token.class.php');
require_once('../classes/Google_Timeline_Item.class.php');
	
/**
 * the OAuth server should have brought us to this page with a $_GET['code']
 */
if(isset($_GET['code'])) {
    // try to get an access token
    $code = $_GET['code'];
 
	// authenticate the user
	$Google_OAuth2_Token = new Google_OAuth2_Token();
	$Google_OAuth2_Token->code = $code;
	$Google_OAuth2_Token->client_id = $settings['oauth2']['oauth2_client_id'];
	$Google_OAuth2_Token->client_secret = $settings['oauth2']['oauth2_secret'];
	$Google_OAuth2_Token->redirect_uri = $settings['oauth2']['oauth2_redirect'];
	$Google_OAuth2_Token->grant_type = "authorization_code";

	try {
		$Google_OAuth2_Token->authenticate();
	} catch (Exception $e) {
		// handle this exception
		print_r($e);
	}

	// A user just logged in.  Let's figure out where their Glass is
	if ($Google_OAuth2_Token->authenticated) {
		
		$Google_Timeline_Item = new Google_Timeline_Item($Google_OAuth2_Token);
		// fetch the timeline item by the Timeline ID
		$Google_Timeline_Item->get('3664d583-f39f-4a15-99dc-d2860c722f78');	
		
		// fetch the attachments - we will display them inline later
		if ($Google_Timeline_Item->attachments) {
			 foreach ($Google_Timeline_Item->attachments as $Attachment) {
				$Attachment->fetchContent();
			}
		}
		
		
	}
}

?>
	<h2>Timeline Item</h2>
	<dl>
		<dt>ID</dt>
		<dd><?= $Google_Timeline_Item->id; ?></dd> 
		
		<dt>Created</dt>
		<dd><?= $Google_Timeline_Item->created; ?></dd>
		
		<? if ($Google_Timeline_Item->recipients) { ?>
		<dt>Recipients</dt>
		<? $numRecipients = count($Google_Timeline_Item->recipients); ?>
		Sent to <?= $numRecipients; ?> recipient<? if ($numRecipients !== 1) { ?>s<? } ?>:
		<? foreach ($Google_Timeline_Item->recipients as $Recipient) { ?>
			<dd><?= $Recipient->displayName; ?></dd>
		<? } ?>
		<? } ?>
		
		<? if ($Google_Timeline_Item->html) { ?>
			<dd><?= $Google_Timeline_Item->html; ?></dd>
		<? } ?>
		
		<? if ($Google_Timeline_Item->text) { ?>
			<dd><?= $Google_Timeline_Item->text; ?></dd>
		<? } ?>
			
		<? if ($Google_Timeline_Item->attachments) { ?>
		<dt>Attachments</dt>
		<? $numAttachments = count($Google_Timeline_Item->attachments); ?>
		Found <?= $numAttachments; ?> attachment<? if ($numAttachments !== 1) { ?>s<? } ?>:
		<? foreach ($Google_Timeline_Item->attachments as $Attachment) { ?>	
			<?
			// we can display images inline in our HTML
			// http://stackoverflow.com/questions/11474346/how-to-encode-images-within-html
			$imageType = $Attachment->contentType;
			$imagedata = base64_encode($Attachment->content);
			?>
			<dd><img alt="image" src="data:<?= $imagetype; ?>;base64,<?= $imagedata; ?>" width="320" height="234" /></dd>
		<? } ?>
		<? } ?>
	</dl>