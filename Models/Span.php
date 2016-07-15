<?php //model for span object
include_once 'Model.php';
class Span extends Model
{
	public $start;
	public $end;
	
	public function __construct(SimpleXMLElement $xml)
	{
		foreach($xml->attributes() as $k => $v)
		{
			$this->$k = (string) $v;
		}
	}
}
?>