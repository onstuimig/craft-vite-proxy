<?php

namespace onstuimig\viteproxy;

use Craft;
use craft\base\Plugin;
use craft\events\RegisterUrlRulesEvent;
use craft\web\twig\variables\CraftVariable;
use craft\web\UrlManager;
use onstuimig\viteproxy\variables\ViteProxyVariable;
use yii\base\Event;

/**
 * Vite Proxy plugin
 *
 * @method static ViteProxy getInstance()
 * @author Onstuimig
 * @copyright Onstuimig
 * @license https://craftcms.github.io/license/ Craft License
 */
class ViteProxy extends Plugin
{
	public string $schemaVersion = '1.0.0';

	public static function config(): array
	{
		return [
			'components' => [
				
			],
		];
	}

	public function init(): void
	{
		parent::init();

		// Defer most setup tasks until Craft is fully initialized
		Craft::$app->onInit(function() {
			$this->attachEventHandlers();
		});
	}

	private function attachEventHandlers(): void
	{
		Event::on(
			UrlManager::class,
			UrlManager::EVENT_REGISTER_SITE_URL_RULES,
			function(RegisterUrlRulesEvent $event) {
				$event->rules['_vite_'] = 'vite-proxy/get/index';
				$event->rules['_vite_/<path:.*>'] = 'vite-proxy/get/index';
				$event->rules['_vite_public_'] = 'vite-proxy/get/public';
				$event->rules['_vite_public_/<path:.*>'] = 'vite-proxy/get/public';
			}
		);

		Event::on(
			CraftVariable::class,
			CraftVariable::EVENT_INIT,
			function(Event $event) {
				/** @var CraftVariable $variable */
				$variable = $event->sender;
				
				$variable->set('viteProxy', ViteProxyVariable::class);
			}
		);
	}
}
