<?php //this is a demo for the StanfordNLP4PHP wrapper class.

//require all the necessary objects
require 'Core.php';
foreach(glob('Models\*') as $file)
{
	include_once $file;
}
foreach(glob('Exceptions\*') as $file)
{
	require $file;
}

//instantiate the core object
//this object handles the files and parsing
$core = new Core();

//process some text
$core->process("A penny for a spool of thread, A penny for a needle. That's the way the money goes. Pop! goes the weasel.");

//get the last xml document that was produces or supply a file name w/o the file extension
$doc = $core->getDocument();

//a couple of functions to get info from the document
$mentions = $doc->getObjects('mentions');
$nouns = $doc->getObjectsBy('tokens','POS','NN');

//a little helper function to view the data
$core->preview($mentions);
$core->preview($nouns);

//that's the gist of this wrapper
//there are also methods to toggle the annotators
//used and to delete files, views files etc.
//this is all in the core object if you're curious.

?>