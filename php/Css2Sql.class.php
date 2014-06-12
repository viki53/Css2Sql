<?php

class Css2Sql{
	
	public static function parse_selector($query) {
		$selector_regex = '/^([\w]+)(\[([\w]+)=(.*)\])?(\#([\d]+))?(::([\w]+))?$/';

		preg_match($selector_regex, $query, $selector_parts);

		if(empty($selector_parts) || sizeof($selector_parts) <= 2) {
			return $query;
		}

		$res = new StdClass();
		$res->selectors = array();

		$res->entity = $selector_parts[1];

		if(!empty($selector_parts[8])) {
			$res->entity_attributes = array_map('trim', explode(',', $selector_parts[8]));
		}
		if(!empty($selector_parts[3]) && !empty($selector_parts[4])) {
			$res->selectors[$selector_parts[3]] = Css2Sql::parse_selector($selector_parts[4]);
		}
		if(!empty($selector_parts[6])) {
			$res->selectors['id'] = $selector_parts[6];
		}
		return $res;
	}

	public static function selector_to_sql($selector, $table_prefix = '') {
		$sql = 'SELECT ';

		$sql .= (!empty($selector->entity_attributes) ? implode(', ', $selector->entity_attributes) : '*');

		$sql .= ' FROM `'.$table_prefix.$selector->entity.'`';

		if(!empty($selector->selectors)){
			foreach($selector->selectors as $col => $value){
				if(is_object($value)){
					$sql .= ' WHERE `'.$col.'` = ('.Css2Sql::selector_to_sql($value).')';
				}
				else{
					if(ctype_digit($value)) {
						$sql .= ' WHERE `'.$col.'` = '.intval($value).'';
					}
					else{
						$sql .= ' WHERE `'.$col.'` = "'.$value.'"';
					}
				}
			}
		}

		return $sql;
	}
}