<?php
	namespace ui;

class Page
{
	private $_url ;		// URL demand� par l'utilisateur
	private $_content ;	// Fichier du contenu
	private $_layout ;	// Layout � utiliser
	private $_regexp ;	// Expression r�guli�re qui a permi de d�terminer la page
	
	public function __construct()
	{
	    $ctp = func_num_args();
	    $args = func_get_args();
	    switch($ctp)
	    {
	        case 4:	// Un seul argument, c'est le manager
	            $this->_construct4($args[0],$args[1],$args[2],$args[3]);
	            break;
	         default:
	            break;
	    }
	}

	public function _construct4($url, $content, $layout, $regexp)
	{
		$this->_url=$url;
		$this->_content=$content;
		$this->_layout=$layout;
		$this->_regexp=$regexp;
	}
	
	public function getContent() {
		return $this->_content;
	}
	
	
}