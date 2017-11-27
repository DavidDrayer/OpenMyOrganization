<?php
	namespace displaymanager;
	
	class GraphicManager implements GenericGraphicManager
	{
		private $_manager;
		
		const HTML = 1;
		const PDF = 2;
		const Popup = 3;
		
		public function __construct($format=1)
		{
			switch ($format) {
				case 1:
					$this->_manager=new HTMLManager();
					break;
				case 2:
					$this->_manager=new PDFManager();
					break;
				case 3:
					$this->_manager=new PopupManager();
					break;
				default:
					$this->_manager=new HTMLManager();
			}
		}
		
		public function getBrowser($object) {
			return $this->_manager->getBrowser($object);
		}
		public function getEditor($object) {
			return $this->_manager->getEditor($object);
		}
		
	}
?>
