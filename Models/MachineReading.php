<?php //model for machine reading
include_once 'Model.php';
class MachineReading extends Model
{
	public $entities = array();
	public $relations = array();
	
	public function __construct(SimpleXMLElement $xml)
	{
		//$this->construct($xml);
		if(isset($xml->entities->entity))
		{
			foreach($xml->entities->entity as $entity)
			{
				array_push($this->entities, new Entity($entity));
			}
		}
		if(isset($xml->relations->relation))
		{
			foreach($xml->relations->relation as $relation)
			{
				array_push($this->relations, new Relation($relation));
			}
		}
	}
}
?>