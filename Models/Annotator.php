<?php //model for annotator
class Annotator
{
	public $name;
	public $use = true; //boolean on/off
	public $dependencies = array(); //list of other needed annotators
	
	public function __construct($name, $dependencies)
	{
		$this->name = $name;
		$this->dependencies = $dependencies;
	}
	
	//return a list of Annotator objects
	public static function getAnnotators($file)
	{
		$list = array();
		if(file_exists($file))
		{
			$contents = file_get_contents($file);
			$lines = explode("\n",$contents);
			foreach($lines as $line)
			{
				$data = explode("\t",$line);
				$name = $data[0];
				$dependencies = explode(',',$data[1]);
				array_push($list, new Annotators($name, $dependencies));
			}
		}
		else
		{
			throw new FileNotFoundException($file);
		}
		return $list;
	}
	
	//toggle the $use switch
	public function toggle()
	{
		$this->use = ($this->use) ? false : true;
	}
}
?>