<?php

namespace WilokeHeroSlider\Share;

class AutoPrefix
{
	public static function namePrefix($name)
	{
		return strpos($name, WILOKE_WILOKEHEROSLIDER_PREFIX) === 0 ? $name : WILOKE_WILOKEHEROSLIDER_PREFIX . $name;
	}

	public static function removePrefix(string $name): string
	{
		if (strpos($name, WILOKE_WILOKEHEROSLIDER_PREFIX) === 0) {
			$name = str_replace(WILOKE_WILOKEHEROSLIDER_PREFIX, '', $name);
		}

		return $name;
	}
}