<?php
// sample command string: java -cp "*" -Xmx2g edu.stanford.nlp.pipeline.StanfordCoreNLP -annotators tokenize,ssplit,pos,lemma,ner,parse,dcoref -file input.txt
//more info: http://stanfordnlp.github.io/CoreNLP/cmdline.html#notes

class Core
{
	//***************
	// Java variables
	//***************
	private $java_path = 'java'; // the command to run java
	private $java_options = array(); // array of java switch options
	private $path = '';	// path to where the standford corenlp directory resides
	private $stanford_path = 'stanford-corenlp-full';
	private $stanford_directory_name = 'stanford-corenlp-full';
	
	//**********************************************************
	// Files - the text is stored in a tmp file which is parsed
	//**********************************************************
	private $seperator = '\\'; //the windows symbol for seperating file locations
	private $tmp_path = 'Files'; // directory to store tmp file
	private $tmp_prefix = 'nlp'; // prefix of tmp file
	private $tmp_permission = 0644; // permission to set tmp file
	private $tmp_file = '';			//this will be random if not set by user
	private $regexner_path = 'Config'; //path to the regexner file
	private $regexner_file = 'regexner.txt'; //name of the regexner file
	
	//**************
	//data variables
	//**************
	public $document; //the stanford nlp document object for working with the data
	public $regexners = array(); //all the RegexNER Objects
	protected $annotators = array( 	//what parts of the Stanford NLP took kit to use
			'tokenize' => true,
			'ssplit' => true,
			'parse' => true,
			'pos' => true,
			'lemma' => true,
			'ner' => true,
			'regexner' => true,
			'sentiment' => true,
			'truecase' => true,
			'depparse' => true,
			'dcoref' => true,
			'relation' => true,
			'natlog' => true,
			'quote' =>true,
			'cleanxml' => true,
	);

	
	//************************************************
	//CONSTRUCTOR
	//@path = path to jar files
	//@java_option = option for the java command
	//************************************************
	public function __construct($path = '', $java_options = array('Xmx2g'))
	{
		
		//did the user provide a path?
		//if not use the current directory
		if(trim($path) == '')
		{
			$path = __DIR__ ;
		}
		
		//get Regexners (Regular Expression Named Entity Recognition objects)
		$this->regexners = Regexner::getRegexners($this->regexner_path . $this->seperator . $this->regexner_file);
		//set the path to the directory where the stanford jar files are located
		$this->set_path($path);
		$this->set_java_options($java_options);
	}


	
	
	//***********************************************************
	//run a text document through the Stanford NLP Core Processor
	//@txt a string that is the text to parse
	//Writes an XML file and appends .out to the temp file name
	//***********************************************************
	public function process($txt, $filename = null)
	{
		//create the tmp file
		$this->tmp_file = tempnam($this->tmp_path, $this->tmp_prefix);
		
		//change the file permissions
		chmod($this->tmp_file, octdec($this->tmp_permission));
		chmod($this->tmp_file, 0644);
		
		//spell check the supplied text
		$this->spellcheck($txt);
		
		//put supplied text into the temp file
		file_put_contents($this->tmp_file, $txt);
		
		//create the command string
		//$cmd = 'java -cp "'. $this->stanford_path .'\\*" -'. $options .' edu.stanford.nlp.pipeline.StanfordCoreNLP -annotators '. $this->listAnnotators() .' -file '. $this->tmp_file . ' -outputDirectory '. $this->tmp_path . ' -regexner.mapping '. $this->tmp_path .'\\'. $this->regexner_file;
		$cmd = $this->createCommand();
		
		//echo out for testing purposes
		//echo $cmd;
		
		//execute the command
		shell_exec($cmd);
	}
	
	//******************************************************************************************
	//Create a command line string for stanford core nlp based on the current core configuration
	//@return string the command line string
	//******************************************************************************************
	public function createCommand()
	{
		//start the string
		$command = '';
		
		//add the java command or the path to java
		$command .= $this->java_path;
		
		//add the class path
		$command .= ' -cp "'. $this->stanford_path . $this->seperator . '*" ';
		
		//add options
		$options = implode(' ', $this->java_options);
		$command .= '-'.$options;
		
		//add the call to the pipeline object
		$command .= ' edu.stanford.nlp.pipeline.StanfordCoreNLP ';

		//add the annotators
		$command .= '-annotators '. $this->listAnnotators();
		
		//add the input and output directors
		$command .= ' -file '. $this->tmp_file . ' -outputDirectory '. $this->tmp_path;
		
		//this is for testing purposes
		//$command .= ' -file '. $this->tmp_path .'\\nlp3F25.tmp' . ' -outputDirectory '. $this->tmp_path;
		
		//if using regexner add this to the command string
		if($this->annotators['regexner'] === true)
		{
			$command .=' -regexner.mapping '. $this->regexner_path . $this->seperator . $this->regexner_file;
		}
		
		
		return $command;
	}
	
	//****************************************
	//instantiate a StanfordNLPDocument object
	//@$filename name of the xml file to parse
	//****************************************
	public function getDocument($filename = '')
	{
	  try{
		//if file name not supplied set it to the last file that was created
		$filename = $filename == '' ? $this->tmp_file : $this->tmp_path . '\\' . $filename . '.tmp';
		
		//test to see if still exists
		if(! file_exists($filename))
		{
			throw new FileNotFoundException($filename);
		}
		
		//get the xml from the file
		$xml = simplexml_load_file($filename .'.out') or die("Error: Cannot create object");
		
		//create the StanfordNLPDocument
		$this->document = new Document($xml->document);
		
		return $this->document;
	  }
	  catch(Exception $e)
	  {
		$this->preview($e);
	  }
	}
	
	//*******************************************************
	//deletes a the text and corrisponding XML file
	//@filename is the name of the text file to delete
	// 	if no file name provided the last processed file will
	//	be deleted
	//throws file not found exception
	//*******************************************************
	public function delete($filename = null)
	{
	  try
	  {
		//if file name not supplied set it to the last file that was created
		$filename = $filename == '' ? $this->tmp_file : $this->tmp_path . '\\' . $filename . '.tmp';
		
		//test to see if still exists
		if(! file_exists($filename))
		{
			throw new FileNotFoundException($filename);
		}

		unlink($filename) or die("Error can't delete file.");
		if(file_exists($filename . '.out')) unlink($filename . '.out') or die("Error can't delete file.");
		
		return "Files deleted.";
	  }
	  catch(Exception $e)
	  {
		$this->preview($e);
	  }
	}
	
	//get all files
	public function getAllFiles()
	{
		$filenames = glob($this->tmp_path . $this->seperator . "*.tmp");
		return $filenames;
	}
	
	//php spell check utility
	public function spellcheck($txt)
	{
		$o = '';
		if(function_exists('pspell_new'))
		{
			$pspell_link = pspell_new("en");
			foreach($words as $k => $v)
			{
				if (!pspell_check($pspell_link, $v))
				{
					$o .= pspell_suggest($pspell_link, $v).' ';
				}
			}
			$txt = $o;
		}
		return $txt;
	}	
	
	//*****************************************************************
	//switch annotators from true to false or false to true
	//@list_of_annotators a string will the names of various annotators
	//*****************************************************************
	public function toggleAnnotators($list_of_annotators)
	{
		foreach($this->annotators as $k => $v)
		{
			//weird fix for the strpos function
			$list_of_annotators = ' ' . $list_of_annotators;
			
			//if the key is in the string then set it to the opposite of what it is currently set to
			$this->annotators[$k] = strpos($list_of_annotators, $k) ? !$v : $v;
		}
	}
	
	//*********************************************************************************
	//get a list of the annotators (and hopefully their purpose) and status (on or off)
	//*********************************************************************************
	public function viewAnnotators()
	{
		$list_of_annotators = '';
		foreach($this->annotators as $k => $v)
		{
			$status = $v ? 'on' : 'off';
			$list_of_annotators .= "$k = $status, ";
		}
		return $list_of_annotators;
	}
	
	//***********************************************
	//get a command line ready list of the annotators
	//***********************************************
	public function listAnnotators()
	{
		$count = 0;
		$list_of_annotators = '';
		foreach($this->annotators as $k => $v)
		{
			$list_of_annotators .= $v ? "$k," : "";
		}
		
		//trim the last comma off and return
		return rtrim($list_of_annotators, ",");
	}
	
	//a simple helper function to preview data
	public function preview($obj)
	{
		echo "<pre>";
		print_r($obj);
		echo "</pre>";
	}

	public function set_path($path)
	{
		$this->path = trim(rtrim(trim($path),'/')).'\\';
		$this->set_tmp_path($this->path);
		$this->set_stanford_path($this->path);
		$this->set_regexner_path($this->path);
	}

	public function set_stanford_path($path)
	{
		$this->stanford_path = trim(rtrim($path,'/')) . $this->stanford_path;
	}

	public function set_regexner_path($path)
	{
		$this->regexner_path = trim(rtrim($path,'/')) . $this->regexner_path;
	}

	public function set_java_path($java_path)
	{
		$this->java_path = trim($java_path);
	}

	public function set_java_options($java_options = array())
	{
		$this->java_options = $java_options;
	}

	public function set_tmp_path($path)
	{
		$this->tmp_path = trim(rtrim($path,'/')) . $this->tmp_path;
	}

	public function set_tmp_prefix($prefix)
	{
		$this->tmp_prefix = trim(ltrim($prefix,'/'));
	}

	public function set_tmp_permission($perm)
	{
		$this->tmp_permission = $perm;
	}

	public function set_annotators($annotators)
	{
		$this->annotators = $annotators;
	}
	
	public function get_annotators()
	{
		return $this->annotators;
	}
}
// EOF