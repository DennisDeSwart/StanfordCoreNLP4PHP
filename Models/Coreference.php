<?php
include_once 'Model.php';
class Coreference extends Model
{
	public static $counter = 0;
	public $id;
	public $mentions = array(); //of mentions in the sentence	
	
	public function __construct(SimpleXMLElement $xml)
	{
		$this->id = Coreference::$counter;
		Coreference::$counter++;
		if(isset($xml->mention)){
			foreach($xml->mention as $mention)
			{
				array_push($this->mentions, new Mention($mention));
			}
		}
	}

}
?>