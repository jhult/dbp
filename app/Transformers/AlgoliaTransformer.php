<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class AlgoliaTransformer extends BaseTransformer
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform($items)
    {
	    switch ($this->route) {
		    case "v4_algolia.bibles":    return $this->transformBibles($items);
		    case "v4_algolia.languages": return $this->transformLanguages($items);
	    }
    }

    private function transformBibles($bible)
    {
	    $types = $bible->links->pluck('type')->merge($bible->filesets->pluck('set_type_code'))->flatten()->map(function ($item, $key) {
		    $item = strtolower($item);
		    if(str_contains($item, '_')) {
			    $item = explode('_',$item);
			    $item = $item[0];
		    }
		    if($item == "pdf" | $item == "mobi" | $item == "epub") return "ebook";
		    if($item == "text" | $item == "web" | $item == "app" | $item == "cat") return "web";
		    return $item;
	    });
    	return [
		    'name'          => $bible->translations->where('iso','eng')->first()->name ?? "",
			'vname'         => $bible->translations->where('iso',$bible->iso)->first()->name ?? "",
		    'iso'           => $bible->language->first()->iso,
	        'language_name' => $bible->language->first()->name,
			'date'          => $bible->date,
			'country_id'    => $bible->country->first()->id ?? "",
		    'country_name'  => $bible->country->first()->name ?? "",
			'continent_id'  => $bible->country->first()->continent_id ?? "",
			'abbr'          => $bible->abbr,
			'script'        => $bible->script,
			'autonym'       => "",
			'organizations' => "",
			'types'         => array_flatten($bible->types),
	    ];
    }

    private function transformLanguages($language)
    {

		    $class = [];
		    foreach ($language->classifications as $classification) {
			    $class['lvl'.$classification->order] = $classification->name;
		    }
		    $language->classification = $class;
		    $language->alt_names = $language->translations->pluck('name')->unique()->flatten();

		    $language->country_id = $language->countries->first()->id ?? "";
		    $language->continent_id = $language->countries->first()->continent ?? "";
		    $language->country_name = $language->countries->first()->name ?? "";

		    unset($language->countries);
		    unset($language->translations);
		    unset($language->classifications);
			return $language;
    }

}
