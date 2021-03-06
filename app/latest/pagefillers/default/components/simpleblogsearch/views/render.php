<?php

	// Dependencies
	Wi3::inst()->plugins->load("plugin_jquery_wi3");

	$this->css("style.css");

	// Publish an array as JS object on the frontend
	$results = Array("pages"=>Array(),"articles"=>Array());
	foreach($pages as $index => $page) {
		$results["pages"][] = Array(
			"title" => $page->longtitle,
			"text" => "",
			"url" => Wi3::instance()->urlof->page($page)
		);
	}
	foreach($articles as $index => $article) {
		if (isset($article->title) && isset($article->summary) && isset($article->pageurl)) {
			$results["articles"][] = Array(
				"title" => $article->title,
				"text" => $article->summary,
				"url" => $article->pageurl
			);
		}
	}

	$this->javascriptObject("wi3.pagefiller.default.simpleblogsearch.searchdata",$results);

	$this->javascript("js.js");

?>

<input class='wi3_pagefiller_default_component_simpleblogsearch' placeholder='zoeken'></input>
<div style='background: #fff; max-width: 200px;'>
	<div class='wi3_pagefiller_default_component_simpleblogsearch_result'>
		...
	</div>
</div>