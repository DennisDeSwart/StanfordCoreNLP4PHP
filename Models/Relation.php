<?php
include_once 'Model.php';
class Relation extends Model
{
	public $id;
	public $relation;
	public $arguments = array();
	public $probabilities = array();
	public function __construct(SimpleXMLElement $xml)
	{
		$this->relation = (string) $xml;

		if(isset($xml->arguments->entity))
		{
			foreach($xml->arguments->entity as $entity){
				array_push($this->arguments, new Entity($entity));
			}
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