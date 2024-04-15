<?php

namespace onstuimig\viteproxy\controllers;

use craft\helpers\App;
use craft\web\Controller;
use nystudio107\pluginvite\helpers\FileHelper;
use nystudio107\vite\Vite;

/**
 * Get controller
 */
class GetController extends Controller
{
	public $defaultAction = 'index';
	protected array|int|bool $allowAnonymous = self::ALLOW_ANONYMOUS_LIVE;

	/**
	 * vite-proxy/get action
	 */
	public function actionIndex(?string $path = null, ?bool $public = null)
	{
		if ($path === null) {
			$path = $this->request->getQueryParam('path', '');
		}

		if ($public === null) {
			$public = $this->request->getQueryParam('public', false);
		}

		$assetUrl = $this->getAssetUrl($path, $public);

		$devConfig = [];
		if (App::devMode()) {
			$devConfig = [
				'ssl' => [
					'verify_peer' => false,
					'verify_peer_name' => false,
				],
			];
		}

		$context = stream_context_create(array_merge([
			'http' => [
				'follow_location' => true,
			],
		], $devConfig));

		if (empty($assetUrl)) {
			http_response_code(404);
			exit;
		}

		$headers = get_headers($assetUrl, false, $context);

		foreach ($headers as $header) {
			header($header, true);
		}
		
		@readfile($assetUrl, false, $context);
		exit;
	}

	/**
	 * vite-proxy/get/public action
	 */
	public function actionPublic(?string $path = null)
	{
		return $this->actionIndex($path, true);
	}

	private function getAssetUrl(string $path, bool $public = false): string
	{
		if (Vite::getInstance()->vite->devServerRunning()) {
			return FileHelper::createUrl(Vite::getInstance()->vite->devServerInternal, $path);
		}
		return Vite::getInstance()->vite->asset($path, $public);
	}
}
