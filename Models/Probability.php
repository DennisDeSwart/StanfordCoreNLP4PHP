<?php
include_once 'Model.php';
class Probability extends Model
{
	public $label;
	public $value;
	public function __construct(SimpleXMLElement $xml)
	{
		$this->construct($xml);
	}
}
?>