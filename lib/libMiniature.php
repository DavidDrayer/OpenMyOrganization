<?

	function checkMinSize ($path, $number, $type, $sizeX, $sizeY) {
		if (file_exists($path.$number.".jpg")) {
			if ($type=="jpg")  $src = @imagecreatefromjpeg($path.$number.".jpg");
			if ($type=="jpeg")  $src = @imagecreatefromjpeg($path.$number.".jpg");
			if ($type=="gif")  $src = @imagecreatefromgif($path.$number.".gif");
			if ($type=="png")  $src = @imagecreatefrompng($path.$number.".png");
			if ($src) {
				$srcX=imagesx($src);
				$srcY=imagesy($src);
				if ($srcX<$sizeX || $srcY<$sizeY) {
					return false;
				}
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	
	
	function checkMini ($image, $sizeX, $sizeY, $cat, $setFormat=0, $pos=5, $enlarge=1) {
		$lastSlash=strrpos($image,"/");
		$lastPoint=strrpos($image,".");
		$path=substr($image,0,$lastSlash+1);
		$number=substr($image,$lastSlash+1,$lastPoint-$lastSlash-1);
		$type=substr($image,$lastPoint+1);	
		return checkMiniature ($path, $number, $type, $sizeX, $sizeY, $cat, $setFormat, $pos, $enlarge);
		
		
	}
	
	// Fonction contrôlant l'existance d'une miniature, et se chargeant de la créer si nécesaire
	// Entrées  : $path : chemin d'accès aux images
	//            $number : numéro de l'image, ou son nom
	//            $type : type de l'image (jpeg, gif, png)
	//            $sizeX : plus grande largeur autorisée
	//            $sizeY : plus grande hauteur autorisée
	//            $cat : nom du répertoire dans lequel stoquer l'image
	function checkMiniature ($path, $number, $type, $sizeX, $sizeY, $cat, $setFormat=0, $pos=5, $enlarge=1){
		// Contrôle que l'équivalent miniature existe
		if (!file_exists($path.$cat."/".$number.".jpg")) {
			
			//Crée le répertoire si nécessaire
			if (!is_dir($path.$cat)) {
				@mkdir($path.$cat, 0777);
			}
			@chmod ( $path.$cat, 0777 );

			// Crée la miniature
			if ($type=="jpg")  $src = @imagecreatefromjpeg($_SERVER["DOCUMENT_ROOT"].$path.$number.".jpg");
			if ($type=="jpeg")  $src = @imagecreatefromjpeg($_SERVER["DOCUMENT_ROOT"].$path.$number.".jpg");
			if ($type=="gif")  $src = @imagecreatefromgif($_SERVER["DOCUMENT_ROOT"].$path.$number.".gif");
			if ($type=="png")  $src = @imagecreatefrompng($_SERVER["DOCUMENT_ROOT"].$path.$number.".png");
			if (isset($src)) {
				$srcX=@imagesx($src);
				$srcY=@imagesy($src);
				if ($srcX<1) {
					return false;
				}
				$coinX=0;$coinY=0;
				if ($setFormat==0) {
					if ($enlarge==0 && $srcX<$sizeX && $srcY<$sizeY) {
						$tailleX=$srcX;
						$tailleY=$srcY;
					} else {
						// Calcul la taille de l'image finale (en gardant le ratio
						if ($srcX/$srcY > $sizeX/$sizeY) {
							$tailleX=$sizeX;
							$tailleY=$srcY/$srcX*$tailleX;
						} else {
							$tailleY=$sizeY;
							$tailleX=$srcX/$srcY*$tailleY;
						}	
					}
					
					// Les miniatures sont de toute façon des jpeg
					$im=imagecreatetruecolor ($tailleX, $tailleY);
					
				} else {
					// Calcul la taille de l'image finale (en gardant le ratio
					if ($srcX/$srcY > $sizeX/$sizeY) {
						$tailleY=$sizeY;
						$tailleX=$srcX/$srcY*$tailleY;
						if ($pos=2 || $pos=5 || $pos=8) $coinX=($sizeX-$tailleX)/2;
						if ($pos=3 || $pos=6 || $pos=9) $coinX=($sizeX-$tailleX);
					} else {
						$tailleX=$sizeX;
						$tailleY=$srcY/$srcX*$tailleX;
						if ($pos>=4 && $pos<=6) $coinY=($sizeY-$tailleY)/2;
						if ($pos>=7 && $pos<=9) $coinY=($sizeY-$tailleY);
					}	
					// Les miniatures sont de toute façon des jpeg
					$im=imagecreatetruecolor ($sizeX, $sizeY);
				}
				
				imagefill($im, 0,0, ImageColorAllocate( $im, 255, 255, 255 ));
				$ok=imagecopyresampled ( $im, $src, $coinX, $coinY, 0, 0, $tailleX, $tailleY, $srcX, $srcY);
				
				imagejpeg($im,$_SERVER["DOCUMENT_ROOT"].$path.$cat."/".$number.".jpg",90);
				imagedestroy($im);	
				return true;	
			} else {
				return false;
			}
		} else {
			return true;
		}	 	
	}	
?>