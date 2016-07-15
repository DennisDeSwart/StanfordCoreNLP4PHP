<?php

include_once 'Model.php';
class Sentence extends Model
{
	public $id;
	public $sentimentValue;
	public $sentiment;
	public $tokens = array(); //of tokens in the sentence
	public $dependencies = array(); //of dependencies of the tokens in the sentence
	public $parse; //a string of the sentence structure
	public $MachineReading;
	
	//************************************************
	//CONSTRUCTOR
	//given a SimpleXMLElement this will parse the xml
	//and instantiate all the objects above.
	//************************************************
	public function __construct(SimpleXMLElement $xml)
	{
		//$this->construct($xml);
		$this->parse = (string) $xml->parse;

		foreach($xml->attributes() as $k => $v)
		{
			$this->$k = (string) $v;
		}
		
		if(isset($xml->tokens)){
			foreach($xml->tokens->token as $token)
			{
				array_push($this->tokens, new Token($token));
			}
		}
		
		//might need to separate by type
		if(isset($xml->dependencies)){
			foreach($xml->dependencies as $dependency)
			{
				$type = $dependency->attributes()->type;
				foreach($dependency as $dep)
				{
					array_push($this->dependencies, new Dep($dep));
				}
			}
		}
		
		if(isset($xml->MachineReading))
		{
			$this->MachineReading = new MachineReading($xml->MachineReading);
		}
	}
	
	public function toText()
	{
		$text = '';
		foreach($this->tokens as $token)
		{
			if($token->POS == '.') $text = rtrim($text);
			$text .= $token->word . ' ';
		}
		return $text;
	}
}
?>