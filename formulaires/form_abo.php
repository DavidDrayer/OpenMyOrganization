<?
	$root = realpath($_SERVER["DOCUMENT_ROOT"]);
	include_once("$root/include.php");

	// Y a-til une action demandée?
	if (isset($_REQUEST["action"])) {
	
		// Edition ou création d'une org
		if ($_REQUEST["action"]=="refresh") {
			// Recharge l'organisation
			$organisation=$_SESSION["currentManager"]->loadOrganisation($_REQUEST["org"]);
		}

		// Edition ou création d'une org
		if ($_REQUEST["action"]=="addAbo") {

			// Recharge l'organisation
			$organisation=$_SESSION["currentManager"]->loadOrganisation($organisation->getId());
?>
			<!-- Message de confirmation du bon traitement du formulaire -->
			<script>
				alert("Les modifications ont bien été sauvegardées.");
			</script>
<?
	
		}	
	}
	
	// Affiche le contenu du formulaire
	echo "<input type='hidden' id='form_abo_target' value='/formulaires/form_abo.php'>";
	echo "<input type='hidden' name='org' value='".$organisation->getId()."'>";
	echo "<input type='hidden' name='action' value='addSubscription'>";
	
	echo "<table style='width:100%;height:100%;' class='containment-wrapper' cellspacing=0>";
	echo "<tr><td style='width:48%;padding-right:2%;'>";
			echo "<h1>Vos abonnements actuels</h1>";
			// Affiche la liste des abonnements de l'organisation
			$subscriptions=$organisation->getSubscriptions();
			foreach ($subscriptions as $subscription) {
				
				// Ancien abonnement
				if ($subscription->getEndDate() < date_create()) {
					echo "<div style='color:#aaaaaa'>".$subscription->getName()."</div>";
					echo "<div>du ".$subscription->getStartDate()->format("d.m.y")." au ".$subscription->getEndDate()->format("d.m.y")."</div>";
					echo "Ancien: ".($subscription->getEndDate() < date_create())." - ".date_create()->format("d.m.y");
				} else 
				// Futur abonnement
				if ($subscription->getStartDate()>date_create()) {
					echo "<div style=''>".$subscription->getName()."</div>";
					echo "<div>du ".$subscription->getStartDate()->format("d.m.y")." au ".$subscription->getEndDate()->format("d.m.y")."</div>";
					echo "Futur: ".($subscription->getStartDate()>date_create())." - ".date_create()->format("d.m.y");
				} else {
					// Abonnement courant
					echo "<div style='font-weight:bold'>".$subscription->getName()."</div>";
					echo "<div>du ".$subscription->getStartDate()->format("d.m.y")." au ".$subscription->getEndDate()->format("d.m.y")."</div>";
					echo date_create()->format("d.m.y");
				}
			}
							
		// 2ème colonne
		echo "</td>";
		echo "<td style='width:48%;padding-left:2%;'>";

			echo "<h1>Renouveler votre abonnement</h1>";
		
		echo "<input type='hidden' name='test' value='test'>";
		
			$subscriptions2=\security\Subscription::getSubscriptions();
			
			foreach ($subscriptions2 as $subscription) {
				echo "<div><input type='radio' name='suscr' value='".$subscription->getId()."'>".$subscription->getName()."</div>";
				echo "<div> &nbsp; &nbsp; prix:  ".$subscription->getPrice().".- CHF</div>";
			}
			
			// Aucun abonnement, commence à la date courante
			if (count($subscriptions)==0) {
				echo "<div>Dès le : <input type='text' name='date' value='".date_create()->format("d.m.Y")."'></div>";
			} else {
				$date=clone $subscriptions[count($subscriptions)-1]->getEndDate();
				echo "<div>Dès le : <input type='text' name='date' value='".$date->add(new \DateInterval("P1D"))->format("d.m.Y")."'></div>";
			}
			
			
			
			// Bouton paypal
			echo '<img id="buy_abo" src="https://www.paypal.com/fr_FR/FR/i/btn/btn_xpressCheckout.gif" style="margin-right:7px;">';
		
		
		echo "</td></tr></table>";
?>
<script>
		
	 $('#buy_abo').click(function() {$("#formulaire_abo").submit()});

</script>

