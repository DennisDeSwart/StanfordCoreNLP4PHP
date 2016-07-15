<?php
include_once 'Model.php';
class Document extends Model
{
	public $xml;
	public $sentences = array(); //of sentences in the document
	public $coreference = array(); //of coreferences in the document
	
	public function __construct(SimpleXMLElement $xml)
	{		
		$this->xml = $xml;
		
		if(isset($xml->sentences->sentence))
		{
			foreach($xml->sentences->sentence as $sentence)
			{
				array_push($this->sentences, new Sentence($sentence));
			}
		}
		if(isset($xml->coreference->coreference))
		{
			foreach($xml->coreference->coreference as $coreference)
			{
				array_push($this->coreference, new Coreference($coreference));
			}
		}
	}
	
	//change the document back into text form
	public function toText()
	{
		$text = '';
		foreach($this->sentences as $sentence)
		{
			$text .= $sentence->toText();
		}
		return $text;
	}
}
?>