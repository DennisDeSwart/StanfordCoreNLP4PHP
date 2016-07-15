<?php //model for Entity
include_once 'Model.php';
class Entity extends Model
{
	public $id;
	public $span;
	public $probabilities = array();
	
	public function __construct(SimpleXMLElement $xml)
	{
		if(isset($xml->span))
		{
			$this->span = new Span($xml->span);
		}
		if(isset($xml->probabilities->probability))
		{
			foreach($xml->probabilities->probability as $probability)
			{
				array_push($this->probabilities, new Probability($probability));
			}
		}
	}
}
?>