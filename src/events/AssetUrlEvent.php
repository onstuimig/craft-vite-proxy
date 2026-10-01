<?php

namespace onstuimig\viteproxy\events;

use yii\base\Event;


class AssetUrlEvent extends Event
{
	public string $assetUrl;
	public string $path;
	public bool $public = false;
}
