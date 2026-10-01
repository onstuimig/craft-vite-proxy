<?php

namespace onstuimig\viteproxy\variables;

use craft\base\Component;
use craft\helpers\Template;
use craft\helpers\UrlHelper;
use nystudio107\vite\Vite;
use onstuimig\viteproxy\events\AssetUrlEvent;
use Twig\Markup;

class ViteProxyVariable extends Component
{
	/**
	 * @event AssetUrlEvent The event that is triggered when modifying the proxy asset URL.
	 */
	public const EVENT_MODIFY_PROXY_ASSET_URL = 'modifyProxyAssetUrl';

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

			$event = new AssetUrlEvent([
				'assetUrl' => $assetUrl,
				'path' => $path,
				'public' => $public
			]);
			$this->trigger(self::EVENT_MODIFY_PROXY_ASSET_URL, $event);

			$assetUrl = $event->assetUrl;
		} else {
			$assetUrl = Vite::getInstance()->vite->asset($path, $public);
		}
		
		return Template::raw($assetUrl);
	}
}
