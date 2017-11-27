<?php
	namespace displaymanager;
	
	// Manager graphique d�di� � l'affichage HTML standard
	class PDFManager implements GenericGraphicManager
	{
		// Fonction permettant de retourner un "browser", interface graphique permettant d'afficher un objet
		// (cercle, r�le, etc...) dans sa totalit�
		// Entr�e : l'objet � afficher
		// Sortie : un objet de type widget, poss�dant une m�thode "display"
		public function getBrowser($object) {
			// L'objet est-il un array ou un objet simple?
			if (is_array($object)) {
				// Choisi le browser pour un ensemble d'objets
				
				// Si le tableau est vide
				if (sizeof($object)==0) {
					// Message d'erreur
					//echo "Tableau vide.";
					return;
				}
				if(isset($object[0])){
					switch (get_class($object[0]))			
					{
						/*case "holacracy\\Organisation" : 
							// Retourne l'�l�ment wg_organisationsBrowser
							return new \widget\wg_organisationBrowser($object);
							break;*/
						default:
							// Aucun browser d�fini pour cet objet
							echo "Aucun affichage PDF pour les tableaux de classe ".get_class($object[0]);
					}
				}
				if(isset($object[1])){
					switch (get_class($object[1]))			
					{
					case "holacracy\\User" : 
							// Retourne l'�l�ment wg_moiBrowser
							return new \widget\wg_moiBrowser($object[1],$object[2]);
							break;
					case "holacracy\\Organisation" :  
							// Retourne l'�l�ment wg_accountBrowser
							return new \widget\wg_accountBrowser($object[1]);
							break;
					}
				}
			} else {
			// Lit la classe de l'objet et retourne l'�l�ment ad�quat.
			switch (get_class($object))			
			{				
				case "holacracy\\Circle" : 
					// Retourne l'�l�ment wg_circleBrowser
					return new \widget\wg_circleBrowser($object);
					break;
				default:
					// Aucun browser d�fini pour cet objet
					echo "Aucun browser pour les objets de classe -".get_class($object)."-";
			}
			}	
		}
		

		// Fonction permettant de retourner un "editeur", interface graphique permettant d'�diter un objet
		// (cercle, r�le, etc...) dans sa totalit�
		// Entr�e : l'objet � afficher
		// Sortie : un objet de type widget, poss�dant une m�thode "display"
		public function getEditor($object) {
			// Lit la classe de l'objet et retourne l'�l�ment ad�quat.
			switch (get_class($object))			
			{
				case "holacracy\\Circle" : 
					// Retourne l'�l�ment wg_circleBrowser
					return new \widget\wg_CircleEditor($object);
					break;
									
				case "holacracy\\Organisation" : 
					// Retourne l'�l�ment wg_circleBrowser
					return new \widget\wg_OrganisationEditor($object);
					break;
									
				case "holacracy\\Gouvernance" : 
					// Retourne l'�l�ment wg_circleBrowser
					return new \widget\wg_GouvernanceEditor($object);
					break;
									
				default:
					// Aucun browser d�fini pour cet objet
					echo "Aucun �diteur pour les objets de classe ".get_class($object);
			}	
		}
		
	}
?>
