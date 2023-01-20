<?php

namespace WilokeHeroSlider\Share;

class TaxonomyHelper
{
	public static array $aExcludeTaxonomies
		= [
			'post_format',
			'product_shipping_class'
		];


	public static function getTaxonomyOptions(): array
	{
		$aTaxonomyOptions = get_taxonomies([
			'public' => true
		], 'objects');

		$aOptions = [];
		foreach ($aTaxonomyOptions as $oTaxonomy) {
			if (in_array($oTaxonomy->name, self::$aExcludeTaxonomies)) {
				continue;
			}
			$aOptions[$oTaxonomy->name] = $oTaxonomy->label;
		}
		return $aOptions;
	}
}