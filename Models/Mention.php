<?php
include_once 'Model.php';
class Mention extends Model
{
	public static $counter = 0;
	public $id,$sentence, $start, $end, $head, $text;
	
	public function __construct(SimpleXMLElement $xml)
	{
		$this->id = Mention::$counter;
		Mention::$counter++;
		foreach($xml as $k => $v)
		{
			$this->$k  = (string) $v;
		}
	}
}
?>