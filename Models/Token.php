<?php
include_once 'Model.php';
class Token extends Model
{
	public $id, $word, $lemma, $CharacterOffsetBegin, $CharacterOffsetEnd, $POS, $NER, $Speaker, $TrueCase, $TrueCaseText, $sentiment;
	
	public function __construct(SimpleXMLElement $xml)
	{
		$this->id = (string) $xml->attributes()->id;
		
		foreach($xml as $k => $v)
		{
			$this->$k  = (string) $v;
		}
	}
	
	public function toString()
	{
		$str = '';
		foreach($this as $k => $v)
		{
			$str .= "$k => $v, ";
		}
		return $str;
	}

}
?>