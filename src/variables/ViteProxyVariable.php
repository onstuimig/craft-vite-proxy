<?php

namespace onstuimig\viteproxy\variables;

use craft\helpers\Template;
use craft\helpers\UrlHelper;
use nystudio107\vite\Vite;
use Twig\Markup;

class ViteProxyVariable
{
	/**
	* Return the URL for the given asset
	*
	* @param string $path
	*
	* @return Markup
	*/
	public function asset(string $path, bool $public = false): Markup
	{
		if (Vite::getInstance()->vite->devServerRunning()) {
			$trimmedPath = trim($path, '/');
			$proxyPath = '_vite_' . ($public ? 'public_' : '');
			$assetUrl = UrlHelper::siteUrl($proxyPath . '/' . $trimmedPath);
		} else {
			$assetUrl = Vite::getInstance()->vite->asset($path, $public);
		}
		
		return Template::raw($assetUrl);
	}
}
