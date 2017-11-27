<?xml version="1.0" encoding="iso-8859-1"?>

<xsl:stylesheet version= "1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:template match="/">
		<table style="width:100%; height:100%;"><tr><td style="text-align:center; vertical-align:middle;"><form id="form_login" onsubmit="loadEditor(this.tr_user.value,this.tr_password.value); return false" style="text-align:center"><center><table style="background-color:#FFFF99; color:#000000; border:2px solid black;"><tr><td>Utilisateur</td></tr><tr><td><input type="text" id="tr_user"/></td></tr><tr><td>Mot de passe</td></tr><tr><td><input type="password" id="tr_password"/></td></tr><tr><td style="text-align:right"><input type="submit" value="Login"/></td></tr></table></center></form></td></tr></table>
	</xsl:template>
	
</xsl:stylesheet>