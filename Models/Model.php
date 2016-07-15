<?php
//A couple functions to help collect data
//note these methods work with themselves so 
//any aggregate objects extending this class
//can also be searched through
abstract class Model
{
	//recursive method for getting a list of objects from document
	public function getObjectsBy($list_name, $prop_name, $prop_value)
	{
	  //try catch block
	  try{
	  
	    //a flag to see if the list was found
		$found = false;
		
		//starting the return array
		$objects = array();
		
		//if the list is a property of this object
		//push all those objects in the list into the
		//$objects array and send it back to the previous
		//calling model or driver
		if(isset($this->$list_name))
		{
			foreach($this->$list_name as $object)
			{
				if($object->$prop_name == $prop_value)
				{
					array_push($objects, $object);
				}
			}
			$found = true;
		}
		
		//lets also check the properties of $this object 
		foreach($this as $key => $property)
		{
			//well if we already found it
			//don't even bother looking any further
			if($found) break;
			
						
			//if the property is a list
			if(is_array($property))
			{
				//get all the objects in the array
				foreach($property as $object)
				{

					//and call their getObjects function
					//which will return a list objects
					//so we need to push every one of those objects
					//into the $objects array on this level.
					foreach($object->getObjects($list_name) as $object)
					{
						if($object->$prop_name == $prop_value)
						{
							array_push($objects, $object);
						}
					}
				}
				continue;
			}
			
			//if this property is an object
			//well we should ask that object to
			//check itself for the list which 
			//then (if not found) will ask any of
			//it's aggregate objects to check its self (recursion)
			if($this->isModel($key) && is_object($property))
			{				
				//Call this Model's getObject method
				//which if the list was found will return a list of objects
				//so we need to push every one of those objects
				//into the $objects array on this level.
				//other wise it will return an empty array and not loop
				foreach($property->getObjects($list_name) as $object)
				{
					if($object->$prop_name == $prop_value)
					{
						array_push($objects, $object);
					}
				}
			}

		}

		//then we need to return the objects
		//back to the previous calling model.
		//if the calling model is really the Driver
		//then it will contain all the called for objects(By their property name)
		return $objects;
	  }
	  catch (Exception $e)
	  {
		$this->preview($e);
		die;
	  }
	}
	
	//recursive method for getting a list of objects from document
	public function getObjects($list_name)
	{
	  //try catch block
	  try{
	  
	    //a flag to see if the list was found
		$found = false;
		
		//starting the return array
		$objects = array();
		
		//if the list is a property of this object
		//push all those objects in the list into the
		//$objects array and send it back to the previous
		//calling model or driver
		if(isset($this->$list_name) && is_array($this->$list_name))
		{
			foreach($this->$list_name as $object)
			{
				array_push($objects, $object);
			}
			$found = true;
		}
		
		//lets also check the properties of $this object 
		foreach($this as $key => $property)
		{
			//well if we already found it
			//don't even bother looking any further
			if($found) break;
			
						
			//if the property is a list
			if(is_array($property))
			{
				//get all the objects in the array
				foreach($property as $object)
				{

					//and call their getObjects function
					//which might return a list objects
					//so we need to push every one of those objects
					//into the $objects array on this level.
					foreach($object->getObjects($list_name) as $object)
					{
						array_push($objects, $object);
					}
				}
				continue;
			}
			
			//if this property is an object
			//well we should ask that object to
			//check itself for the list which 
			//then (if not found) will ask any of
			//it's aggregate objects to check its self (recursion)
			if($this->isModel($key) && is_object($property))
			{				
				//Call this Model's getObject method
				//which if the list was found will return a list of objects
				//so we need to push every one of those objects
				//into the $objects array on this level.
				//other wise it will return an empty array and not loop
				foreach($property->getObjects($list_name) as $object)
				{
					array_push($objects, $object);
				}
			}

		}

		//then we need to return the objects
		//back to the previous calling model.
		//if the calling model is really the Driver
		//then it will contain all the called for objects(By their property name)
		return $objects;
	  }
	  catch (Exception $e)
	  {
		$this->preview($e);
		die;
	  }
	}
	
	//check to see if something is a model
	public function isModel($name)
	{
		$models = glob('models/*.php');
		foreach($models as $model)
		{
			$tmp = explode('.',$model);
			$modelName = $tmp[0];
			$tmp = explode('/', $modelName);
			$modelName = $tmp[1];
			if(strToLower($name) == strToLower($modelName))
			{
				return true;
			}
		}
		return false;
	}	
	
	//an easy way to view data
	public function preview($obj)
	{
		echo "<pre>";
		print_r($obj);
		echo "</pre>";
	}
}
?>