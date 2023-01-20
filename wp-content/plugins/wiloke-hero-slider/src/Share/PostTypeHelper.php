<?php

namespace WilokeHeroSlider\Share;

class PostTypeHelper
{
	public static function getOptions(): array
	{
		$aPosTypes = get_post_types([
			'public' => true
		], 'objects');

		$aOptions = [];
		foreach ($aPosTypes as $oItem) {
			$aOptions[$oItem->name] = $oItem->labels->singular_name;
		}
		return $aOptions;
	}
}