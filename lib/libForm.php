<script>
function AddLink(editeur)
{
    //identify selected text
    var sText = editeur.selection.createRange();
    if (sText.text != "")
    {
      //create link
      document.execCommand("CreateLink");
      //change the color to indicate success
      if (sText.parentElement().tagName == "A")
      {
        sText.execCommand("ForeColor",false,"#FF0033");
      }
    }
    else
    {
        alert("Please select some text!");
    }   
}
function SetBold(editeur_id)
{
    //identify selected text
    editeur=document.getElementById(editeur_id);
    editeur.focus();
    var sText = document.selection.createRange();
   
    if (sText.text != "")
	{	
      //create link
      document.execCommand('bold',false,null);

    }
    else
    {
        // Rien à faire... 
        sText.expand ("word");                                        
        sText.select ();
         document.execCommand('bold',false,null);
    }   
}

</script>
<?

	function writeEditableText($id, $texte,$style) {
		echo "<div><div><a href='#' onclick='SetBold(\"".$id."\"); return false'>B</a> </div><div tabindex=0 id='".$id."' style='border:2px solid black; min-height:50px;".$style."' contenteditable='true'>".$texte."</div></div>";
	}
?>