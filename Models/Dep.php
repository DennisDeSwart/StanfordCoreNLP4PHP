<?php
include_once 'Model.php';
class Dep extends Model
{
	public static $counter = 0;
	public $id;
	public $type;
	public $governor;
	public $governor_id;
	public $dependent;
	public $dependent_id;
	
	public function __construct(SimpleXMLElement $xml)
	{
		$this->id = Dep::$counter;
		Dep::$counter++;
		foreach($xml as $k => $v)
		{
			$id = $k . '_id';
			$this->$k  = (string) $v[0];
			$this->$id = (string) $v->attributes()->idx;
		}
	}
}
?>