<?php //object model for Regular Expression Named Entity Relationship
	  //data for a config file
	  
class Regexner
{
	public static $counter = 0; 
	public $id;
	public $regex;
	public $type;
	public $override;
	
	public function __construct($data)
	{
		$this->id = regexner::$counter;
		regexner::$counter++;
		$this->regex = $data[0];
		$this->type = $data[1];
		if(isset($data[2])) $this->override = $data[2];
	}
	
	//returns a string prepared to be saved in a file
	public function toFileString()
	{
		$str = $this->regex . "\t" . $this->type;
		if(isset($this->override)) $str .= "\t". $this->override;
		$str .= "\n";
		return $str;
	}
	
	//return a list of regular expression named entity recognition objects
	public static function getRegexners($file)
	{
		$list = array();
		if(file_exists($file))
		{
			$contents = file_get_contents($file);
			$lines = explode("\n",$contents);
			foreach($lines as $line)
			{
				$data = explode("\t",$line);
				array_push($list, new regexner($data));
			}
		}
		else
		{
			throw new FileNotFoundException($file);
		}
		return $list;
	}
	
	//remove regexner by id
	public function removeRegexnerFrom($id, $array)
	{
		$new_array = array();
		foreach($a as $regexner)
		{
			if($regexner->id == $id) continue;
			array_push($new_array, $regexner);
		}
		$array = $new_array;
		return $array;
	}
	
	//add a new regexner to an array and return the provided array
	public function addregexner($array, $regex, $type, $override = null)
	{
		array_push($array, new regexner(array($regex, $type, $override)));
		return $array;
	}
	
	//rewrite the regexner file
	public static function rewriteregexners($file)
	{
		if(file_exists($file))
		{
			$fh = fopen($file, 'w');
			foreach($this->regexners as $regexner)
			{
				fwrite($fh, $regexner->toFileString());
			}
			fclose($fh);
		}
		else
		{
			throw new FileNotFoundException($file);
		}
	}
}
?>