<?php

require_once 'Css2Sql.class.php';

$queries = array(
	'posts#18',
	'posts[author_id=2]::title',
	'posts[author_id=users#2::id]',
	'posts[author_id=users[pseudo=viki53]::id]'
);

foreach ($queries as $query) {
	echo '<pre>';
	
	echo $query.'<hr />';

	$selector = Css2Sql::parse_selector(trim($query));

	echo Css2Sql::selector_to_sql($selector, 'prefix_');

	echo '</pre><br />';
}