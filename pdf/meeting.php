<?php

	// Inclus les éléments partagés entre plusieurs pages: base de donnée, instanciation de classe, etc...
	$root = realpath($_SERVER["DOCUMENT_ROOT"]);
	include_once($root."/include.php");
	// Instantiation du gestionnaire de base de donnée
	$manager=new \datamanager\SqlManager($_SESSION["currentDB"]);
	
	// S'assure que l'on a bien le droit de voir ce document
	

	require($root.'/plugins/fpdf.php');
	
	
//HTML2PDF by Clément Lavoillotte
//ac.lavoillotte@noos.fr
//webmaster@streetpc.tk
//http://www.streetpc.tk


//function hex2dec
//returns an associative array (keys: R,G,B) from
//a hex html code (e.g. #3FE5AA)
function hex2dec($couleur = "#000000"){
    $R = substr($couleur, 1, 2);
    $rouge = hexdec($R);
    $V = substr($couleur, 3, 2);
    $vert = hexdec($V);
    $B = substr($couleur, 5, 2);
    $bleu = hexdec($B);
    $tbl_couleur = array();
    $tbl_couleur['R']=$rouge;
    $tbl_couleur['V']=$vert;
    $tbl_couleur['B']=$bleu;
    return $tbl_couleur;
}

//conversion pixel -> millimeter at 72 dpi
function px2mm($px){
    return $px*25.4/72;
}

function txtentities($html){
    $trans = get_html_translation_table(HTML_ENTITIES, ENT_COMPAT | ENT_HTML401, "ISO-8859-1");
    $trans = array_flip($trans);
    return strtr($html, $trans);
}
////////////////////////////////////

class PDF_HTML extends FPDF
{
//variables of html parser
protected $B;
protected $I;
protected $U;
protected $HREF;
protected $fontList;
protected $issetfont;
protected $issetcolor;

function __construct($orientation='P', $unit='mm', $format='A4')
{
    //Call parent constructor
    parent::__construct($orientation,$unit,$format);
    //Initialization
    $this->B=0;
    $this->I=0;
    $this->U=0;
    $this->HREF='';
    $this->fontlist=array('arial', 'times', 'courier', 'helvetica', 'symbol');
    $this->issetfont=false;
    $this->issetcolor=false;
}

function WriteHTML($html)
{
    //HTML parser
    $html=strip_tags($html,"<h1><h2><h3><del><ins><hr><li><b><u><i><a><img><p><br><strong><em><font><tr><blockquote>"); //supprime tous les tags sauf ceux reconnus
    $html=str_replace("\n",' ',$html); //remplace retour à la ligne par un espace
    $a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE); //éclate la chaîne avec les balises
    foreach($a as $i=>$e)
    {
        if($i%2==0)
        {
            //Text
            if($this->HREF)
                $this->PutLink($this->HREF,$e);
            else
				if (trim(txtentities($e))!="") 
                $this->Write(5,stripslashes(txtentities($e)));
        }
        else
        {
            //Tag
            if($e[0]=='/')
                $this->CloseTag(strtoupper(substr($e,1)));
            else
            {
                //Extract attributes
                $a2=explode(' ',$e);
                $tag=strtoupper(array_shift($a2));
                $attr=array();
                foreach($a2 as $v)
                {
                    if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
                        $attr[strtoupper($a3[1])]=$a3[2];
                }
                $this->OpenTag($tag,$attr);
            }
        }
    }
}

function OpenTag($tag, $attr)
{
    //Opening tag
    switch($tag){
		case 'INS':
			$this->SetStyle('B',true);
			break;
		case 'DEL':
			$this->SetTextColor(127,127,127);
            $this->issetcolor=true;
			break;
		case 'HR':
			$this->SetLineWidth(0.2);
			$this->SetDrawColor(0);
			$this->Line($this->getLeftMargin(),$this->getY(),210-$this->getRightMargin(),$this->getY());
			break;
        case 'STRONG':
            $this->SetStyle('B',true);
            break;
        case 'EM':
            $this->SetStyle('I',true);
            break;
        case 'B':
        case 'I':
        case 'U':
            $this->SetStyle($tag,true);
            break;
        case 'A':
            $this->HREF=$attr['HREF'];
            break;
        case 'IMG':
            if(isset($attr['SRC']) && (isset($attr['WIDTH']) || isset($attr['HEIGHT']))) {
                if(!isset($attr['WIDTH']))
                    $attr['WIDTH'] = 0;
                if(!isset($attr['HEIGHT']))
                    $attr['HEIGHT'] = 0;
                $this->Image($attr['SRC'], $this->GetX(), $this->GetY(), px2mm($attr['WIDTH']), px2mm($attr['HEIGHT']));
            }
            break;
        case 'TR':
        case 'BLOCKQUOTE':
        case 'LI':
            if ($this->getX()-$this->getLeftMargin()>1) $this->Ln();
            $this->Write(5,chr(149)."  ");
            $this->SetLeftMargin(20);
            break;
        case 'BR':
            if ($this->getX()-$this->getLeftMargin()>1) $this->Ln();
           break;
        case 'P':
            if ($this->getX()-$this->getLeftMargin()>1) $this->Ln();
          
            break;
        case 'H1':
        	$this->SetFont('Arial','',16);
        break;
        case 'H2':
        	$this->SetFont('Arial','',13);
        break;
       case 'H3':
        	$this->SetFont('Arial','',11);
 			$this->SetTextColor(100);
        break;
        case 'FONT':
            if (isset($attr['COLOR']) && $attr['COLOR']!='') {
                $coul=hex2dec($attr['COLOR']);
                $this->SetTextColor($coul['R'],$coul['V'],$coul['B']);
                $this->issetcolor=true;
            }
            if (isset($attr['FACE']) && in_array(strtolower($attr['FACE']), $this->fontlist)) {
                $this->SetFont(strtolower($attr['FACE']));
                $this->issetfont=true;
            }
            break;
    }
}

function CloseTag($tag)
{
    //Closing tag
    if($tag=='INS'){
		 $this->SetStyle('B',false);
	}
	if($tag=='DEL'){
		 $this->SetTextColor(0);
		 $this->issetcolor==false;
	}
    if($tag=='STRONG')
        $tag='B';
    if($tag=='P') {
      if ($this->getX()-$this->getLeftMargin()>1) $this->Ln();
      $this->setX($this->getLeftMargin());
	 }
    if($tag=='EM')
        $tag='I';
     if ($tag=='LI')
		$this->SetLeftMargin(15);
    if($tag=='B' || $tag=='I' || $tag=='U')
        $this->SetStyle($tag,false);
    if($tag=='A')
        $this->HREF='';    
    if($tag=='H1'){
        $this->SetFont('Arial','',9);
      if ($this->getX()-$this->getLeftMargin()>1) $this->Ln();        
	}
    if($tag=='H2'){
        $this->SetFont('Arial','',9);
      if ($this->getX()-$this->getLeftMargin()>1) $this->Ln();        
	}
    if($tag=='H3') {
        $this->SetFont('Arial','',9);
        $this->SetTextColor(0);
      if ($this->getX()-$this->getLeftMargin()>1) $this->Ln();        
	}
    if($tag=='FONT'){
        if ($this->issetcolor==true) {
            $this->SetTextColor(0);
        }
        if ($this->issetfont) {
            $this->SetFont('arial');
            $this->issetfont=false;
        }
    }
}

function SetStyle($tag, $enable)
{
    //Modify style and select corresponding font
    $this->$tag+=($enable ? 1 : -1);
    $style='';
    foreach(array('B','I','U') as $s)
    {
        if($this->$s>0)
            $style.=$s;
    }
    $this->SetFont('',$style);
}

function PutLink($URL, $txt)
{
    //Put a hyperlink
    $this->SetTextColor(0,0,255);
    $this->SetStyle('U',true);
    $this->Write(5,$txt,$URL);
    $this->SetStyle('U',false);
    $this->SetTextColor(0);
}

}//end of class



	if (isset($_GET["id"]) && $_GET["id"]>0) {
		// Chargement de l'élément cercle sélectionné
		$meeting=$manager->loadMeeting($_GET["id"]);

		$pdf = new PDF_HTML();
		$pdf->AddPage();

		// Entête
		$pdf->SetY(15);
		$pdf->SetLeftMargin(15);
		$pdf->SetRightMargin(15);
	
		// Affichage de la liste des politiques
		$history=$meeting->getHistory(); 
		
		// Affichage de l'entête
		$pdf->WriteHTML("<h1>".$meeting->getCircle()->getName()."</h1>");
 		$pdf->WriteHTML("<h2>PV de la réunion de ".$meeting->getMeetingType()." du ".$meeting->getDate()->format("d.m.Y")."</h2>");
 		$pdf->WriteHTML("<h3>De ".$meeting->getOpeningTime()->format("H\hi")." à ".$meeting->getClosingTime()->format("H\hi").($meeting->getLocation()!=""?" - ".$meeting->getLocation():"")."</h3>");
        $pdf->Ln();
		$pdf->WriteHTML("<p><font color='#888888'><b>Présents:</b> liste des présents</font></p>");
 		$pdf->WriteHTML("<p><font color='#888888'><b>Excusés :</b> liste des excusés</font></p>");
		$pdf->WriteHTML("<p><font color='#888888'><b>Absents :</b> liste des absents</font></p>");
        $pdf->Ln();

	    $pdf->WriteHTML("<h3>Décisions</h3>");
		foreach($history as $entry) {
			// Groupe d'actions et de projets issus du traitement d'une tension
			$pdf->SetFont('Arial','',9);
			if ($entry->getTensionId()>0) {
				$pdf->WriteHTML("<hr><b>Tension : ".$entry->getTension()->getTitle()."</b>".($entry->getTension()->getUserId()>0?" amenée par <b>".$entry->getTension()->getUser()->getUserName()."</b>".($entry->getTension()->getRoleId()>0?" dans son rôle <b>[".$entry->getTension()->getRole()->getName()."]</b>":""):"")."<br><font color='#888888'><i>".$entry->getTension()->getDescription()."</i></font>");
			} else {
				$pdf->WriteHTML("<hr>");
			}
			
          if ($pdf->getX()-$pdf->getLeftMargin()>1) $pdf->Ln();

			
			$childs=$entry->getChilds();
	
			// Liste de chaque action/projet
			if (count($childs)>0) {
				foreach($childs as $child) {
					$pdf->SetFont('Arial','',9);
					$pdf->WriteHTML($child->getTitle());
					if ($pdf->getX()-$pdf->getLeftMargin()>1) $pdf->Ln();

					$pdf->SetFont('Arial','',7);
					$pdf->WriteHTML($child->getDescription());
					if ($pdf->getX()-$pdf->getLeftMargin()>1) $pdf->Ln();

						
					// Affichage du détail
					$sndchilds=$child->getChilds();
			
					// Liste de chaque action/projet
					if (count($sndchilds)>0) {
						foreach($sndchilds as $sndchild) {
							$pdf->SetFont('Arial','',9);
							$pdf->WriteHTML("<li>".$sndchild->getTitle()."</li>");
							if ($pdf->getX()-$pdf->getLeftMargin()>1) $pdf->Ln();
							$pdf->SetFont('Arial','',7);
							$pdf->WriteHTML($sndchild->getDescription());
							if ($pdf->getX()-$pdf->getLeftMargin()>1) $pdf->Ln();

								
							// Affichage du détail
							
						
						}
					}	
					$pdf->Ln(1);				
				
				}
			}
		}

		
		$pdf->Output();
	}
?>

