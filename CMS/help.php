<style type="text/css">
  div.Text
  {
    color:black;
  }
  div.tut
  {
	color:black;
	background-color:white;
	border: 1px red solid;
	display:none;
  }
  span.code
  {
	background-color:green;
	font-family:Courier new;
	font-size: 11px;
	color:white;
	border: 1px black dotted;
  }
  span.warning
  {
	background-color:black;
	font-family:Courier new;
	font-size: 11px;
	color:orange;
	font-weight:bold;
	border: 1px red dotted;
  }
</style>
<div class='Text'>
Welkom, <?php echo $_SESSION['user'];?><br />
Dit is het CMS (<em>C</em>ontrol <em>M</em>anagement <em>S</em>ystem) voor de website 'Kennisbus.nl'.<br />
<?php 
switch($_SESSION['usertype'])
{
  case "Intern": ?>
  <br />
  Het systeem heeft u herkent als [<b>Docent/Intern</b>]. <br /> <br />
  Als Docent kunt u de onderstaande functies gebruiken:
  <br />
  <I>(Klik tweemaal op de [+] om het 'topic' te openen en daarna op de [-] om deze weer te sluiten.</I>)<br />
 	<UL style='list-style-type:none;'>
			<li><b><a ID="xd1" href="javascript:Toggle('d1')">[+]</a> Een weekoverzicht opvragen;</b>
				<div id='d1' CLASS='tut'>
					<ul>
						<li>Het opvagen van een weekoverzicht gaat als volgt: <br />
						1. Klik op 'Overzicht' In het menu 'Activiteiten';<br />
						2. Selecteer, uit de combobox, uw week naar keuze.<br />
						Het weekoverzicht wordt op het scherm getoond.<br /></li>
					</ul>
				</div>
			</li>
			<li><b><a ID="xd5" href="javascript:Toggle('d5')">[+]</a> Activiteit zoeken;</b>
				<div id='d5' CLASS='tut'>
					<ul>
						<li>Het zoeken van een activiteit gaat als volgt: <br />
						1. Klik op 'Zoeken' In het menu 'Activiteiten';<br />
						2. Vul het trefwoord van de activiteit in;<br />
						3. Vul eventueel de weeknummers waartussen u wilt zoeken in, of kies 'Alle' en 'Geen'<br />
						4. Klik 'Zoek' <br />
						5. De resultaten worden op het scherm getoond.<br />
						</li>
					</ul>
				</div>
			</li>
			<li><b><a ID="xd2" href="javascript:Toggle('d2')">[+]</a> Informatie bij een activiteit aanpassen of toevoegen;</b>
				<div id='d2' CLASS='tut'>
					<ul>
						<li>Het toevoegen of aanpassen van informatie gaat als volgt:<br />
						1. Klik op 'Overzicht' In het menu 'Activiteiten';<br />
						2. Klik op de naam van de activiteit waarbij u informatie wilt toevoegen;<br />
						3. Typ in het grote vlak de informatie die u wilt toevoegen;<br />
						4. Klik op 'Opslaan'; <br />
						5. De activiteit is opgeslagen wanneer de volgende melding wordt getoont:<br />
						<SPAN CLASS='code'> De activiteit is opgeslagen! </SPAN>
						</li>
					</ul>
				</div>
			</li>
			<li><b><a ID="xd3" href="javascript:Toggle('d3')">[+]</a> Een bestand bij een activiteit uploaden;</b>
				<div id='d3' CLASS='tut'>
					<ul>
						<li>Het uploaden van bestanden gaat als volgt: <br />
							1. Navigeer in het menu 'Activiteiten' naar 'Aanpassen';<br />
							1a. Het is helaas niet mogelijk om een bestand te uploaden als er een nieuwe activiteit word aangemaakt, omdat de activiteit nog niet in de database voorkomt;<br />
							2. Klik op de naam van de activiteit waarbij u het bestand wilt uploaden;<br />
							3. Klik op 'Bestand';<br />
							4. Klik (<I>als het bestand nog niet is geupload, anders: ga naar punt 9</I>), 
							in de pop-up, Links boven in de hoek op 'Bestand uploaden'<br />
							5. Selecteer het bestand door te klikken op 'Bladeren';<br />
							6. Klik op 'uploaden';<br />
							6a. Om het uploaden te stoppen, klik op het rode kruis.<br />
							7. Het bestand is succesvol geupload wanneer u de volgende melding ontvangt:<br />
							<SPAN CLASS='code'> Het bestand [Bestandsnaam.extentie] is met succes naar de server verstuurd. </SPAN><br />
							8. Klik op 'Terug naar koppelen bestand.';<br />
							9. Selecteer het selectie vakje van het bestand dat u wilt koppelen;<br />
							10. Klik op 'Koppelen';<br />
							11. Het bestand is succesvol gekoppelt! U kunt het pop-up scherm nu sluiten.<br />
						</li>
					</ul>
				</div>
			</li>
			<li><b><a ID="xd4" href="javascript:Toggle('d4')">[+]</a> Uw wachtwoord aanpassen;</b>
				<div id='d4' CLASS='tut'>
					<ul>
						<li>Het aanpassen van uw wachtwoord gaat als volgt: <br />
						1. Klik op 'Wachtwoord' in het menu 'Persoonlijk';<br />
						2. Voer het wachtwoord dat u nu gebruikt in, in het bovenste vakje;<br />
						3. Voer uw gewenste wachtwoord in, in het tweede vak;<br />
						4. Voer nogmaals uw gewenste wachtwoord in, in het derde vak;<br />
						5. Klik op aanpassen;<br />
						6. Uw wachtwoord is aangepast.<br />
						</li>
					</ul>
				</div>
			</li>
		</ul>
	<?php
	break;
	case "Admin": ?>
	<br />
	Het systeem heeft u herkent als [<b>Administrator</b>]. <br /> <br />
	Als administrator kunt u de onderstaande functies gebruiken:
	<br />
	<I>(Klik tweemaal op de [+] om het 'topic' te openen en daarna op de [-] om deze weer te sluiten.</I>)<br />
	<UL style='list-style-type:none;'>
		<li><b><a ID="xf1" href="javascript:Toggle('f1')">[+]</a> Een weekoverzicht opvragen;</b>
			<div id='f1' CLASS='tut'>
				<ul>
					<li>Het opvagen van een weekoverzicht gaat als volgt: <br />
					1. Klik op 'Overzicht' In het menu 'Activiteiten';<br />
					2. Selecteer, uit de combobox, uw week naar keuze.<br />
					Het weekoverzicht wordt op het scherm getoond.<br /></li>
				</ul>
			</div>
		</li>
		<li><b><a ID="xf13" href="javascript:Toggle('f13')">[+]</a> Activiteit zoeken;</b>
			<div id='f13' CLASS='tut'>
				<ul>
					<li>Het zoeken van een activiteit gaat als volgt: <br />
					1. Klik op 'Zoeken' In het menu 'Activiteiten';<br />
					2. Vul het trefwoord van de activiteit in;<br />
					3. Vul eventueel de weeknummers waartussen u wilt zoeken in, of kies 'Alle' en 'Geen'<br />
					4. Klik 'Zoek' <br />
					5. De resultaten worden op het scherm getoond.<br />
					<I>ps. Laat het veld leeg om alle resultaten te tonen(!)</I>
					</li>
				</ul>
			</div>
		</li>
		<li><b><a ID="xf2" href="javascript:Toggle('f2')">[+]</a> Activiteiten toevoegen;</b>
		<div id='f2' CLASS='tut'>
				<ul>
					<li>Het toevoegen van activiteiten gaat als volgt:<br />
					1. Klik op 'Toevoegen' In het menu 'Activiteiten';<br />
					2. Voeg de gegevens van de activiteit toe; <br />
					<SPAN CLASS='warning'>
						Denk eraan dat de velden waar een 
						<IMG src='images/info_icon.gif' height='13' width='13'> of een 
						<IMG src='images/info_icon_r.gif' height='13' width='13'> voor staat verplicht zijn!
					</SPAN><br />	
					3. Wanneer de docent die u wilt koppelen aan de activiteit niet in de lijst verschijnt, <br />
					volgt u de tutorial waarin u leert hoe u een docent toevoegt.<br />
					4. Klik op de knop 'Opslaan';<br />
					5. Als alle gegevens correct zijn ingevoerd ontvangt u de volgende melding:<br />
					<SPAN CLASS='code'> De activiteit is opgeslagen! </SPAN>
					</li>
				</ul>
			</div>
		</li>
		<li><b><a ID="xf3" href="javascript:Toggle('f3')">[+]</a> Activiteiten aanpassen;</b>
			<div id='f3' CLASS='tut'>
				<ul>
					<li>Het aanpassen van activiteiten gaat als volgt: <br />
					1. Klik op 'Overzicht' In het menu 'Activiteiten';<br />
					2. Selecteer, uit de combobox, uw week naar keuze;<br />
					3. Het weekoverzicht wordt op het scherm getoond;<br />
					4. Selecteer de activiteit die u wilt aanpassen door op de titel te klikken;<br />
					5. De, tijdens het invoeren, ingevulde gegevens die bij de activiteit horen verschijnen op het scherm;<br />
					6. Pas de gegevens die u wilt aanpassen aan; <BR>
					<SPAN CLASS='warning'>
						Denk eraan dat de velden waar een 
						<IMG src='images/info_icon.gif' height='13' width='13'> of een 
						<IMG src='images/info_icon_r.gif' height='13' width='13'> voor staat verplicht zijn!
					</SPAN><br />					
					7. Klik op de knop 'Aanpassen';<br />
					8. De activiteit wordt opgeslagen, mits u alle verplichte velden heeft ingevoerd.<br />
					9. Het weekoverzicht wordt op het scherm getoond.<br />
					</li>
				</ul>
			</div>
		</li>
		<li><b><a ID="xf4" href="javascript:Toggle('f4')">[+]</a> Bestanden uploaden bij activiteiten;</b>
		<div id='f4' CLASS='tut'>
				<ul>
					<li>Het uploaden van bestanden gaat als volgt: <br />
						1. Navigeer in het menu 'Activiteiten' naar 'Aanpassen';<br />
						1a. Het is helaas niet mogelijk om een bestand te uploaden als er een nieuwe activiteit word aangemaakt, omdat de activiteit nog niet in de database voorkomt;<br />
						2. Klik op de naam van de activiteit waarbij u het bestand wilt uploaden;<br />
						3. Klik op 'Bestand';<br />
						4. Klik (<I>als het bestand nog niet is geupload, anders: ga naar punt 9</I>), 
						in de pop-up, rechts boven in de hoek op 'Bestand uploaden'<br />
						5. Selecteer het bestand door te klikken op 'Bladeren';<br />
						6. Klik op 'uploaden';<br />
						6a. Om het uploaden te stoppen, klik op het rode kruis.<br />
						7. Het bestand is succesvol geupload wanneer u de volgende melding ontvangt:<br />
						<SPAN CLASS='code'> Het bestand [Bestandsnaam.extentie] is met succes naar de server verstuurd. </SPAN><br />
						8. Klik op 'Terug naar koppelen bestand.';<br />
						9. Selecteer het selectie vakje van het bestand dat u wilt koppelen;<br />
						10. Klik op 'Koppelen';<br />
						11. Het bestand is succesvol gekoppelt! U kunt het pop-up scherm nu sluiten.<br />
						<SPAN CLASS='warning'>
						U kunt de bestanden ook een andere naam geven <IMG src='../bestandsbeheer/icons/textfield_rename.png'>
						of verwijderen <IMG src='../bestandsbeheer/icons/bin.png'>
						van de server. Let wel op dat de activiteiten waar dit bestand aan gekoppelt is ook verandert worden omdat er anders een dode URL ontstaat.<br />
						Om bestanden los te koppelen van een activiteit moet u het proces uitvoeren tot punt 3 en verder gaan vanaf punt 10. U hoeft dus geen bestand te selecteren, maar wel op koppelen te klikken.
						</SPAN><br />					
					</li>
				</ul>
			</div>
		</li>
		<li><b><a ID="xf5" href="javascript:Toggle('f5')">[+]</a> Docenten & Administrators toevoegen;</b>
		<div id='f5' CLASS='tut'>
				<ul>
					<li>Het toevoegen van docenten en administrators gaat als volgt: <br />
					1. Klik op 'Toevoegen' In het menu 'Docent';<br />
					2. Vul de gegevens in;<br />
					<SPAN CLASS='warning'>
						Denk eraan dat de velden waar een 
						<FONT COLOR='red'>*</FONT> voor staat verplicht zijn!
					</SPAN><br />	
					3. Kies het inlogtype (Intern of Admin);<br />
					4. Klik op 'Opslaan';<br />
					5. De docent is toegevoegd mits u niet bent vergeten een van de verplichte velden in te voeren;<br />
					6. U wordt naar het overzicht gebracht.<br />
					</li>
				</ul>
			</div>
		</li>
		<li><b><a ID="xf6" href="javascript:Toggle('f6')">[+]</a> Docenten & Administrators aanpassen;</b>
		<div id='f6' CLASS='tut'>
				<ul>
					<li>Het aanpassen van docenten en administrators gaat als volgt: <br />
					1. Klik op 'Overzicht' In het menu 'Docent';<br />
					2. Een lijst met docenten wordt getoond;<br />
					3. Selecteer de docent die u wilt aanpassen door op de 'Edit-knop' in de kolom 'Edit' te klikken;<br />
					4. Wijzig de gegevens die u wilt wijzigen;<br />
					5. Klik op 'aanpassen';<br />
					6. De wijziging is opgeslagen.<br />
					7. U wordt terug gebracht naar het overzicht.<br />
					</li>
				</ul>
			</div>
		</li>
		<li><b><a ID="xf7" href="javascript:Toggle('f7')">[+]</a> Gegevens van externe docenten vastleggen en opzoeken;</b>
		<div id='f7' CLASS='tut'>
				<ul>
					<li> Zie de tutorial over het toevoegen van Docenten en Administrators. <br />
					Kies echter voor 'Extern' bij het inlogtype.<br />
					</li>
				</ul>
			</div>
		</li>
		<li><b><a ID="xf8" href="javascript:Toggle('f8')">[+]</a> Studenten toevoegen;</b>
		<div id='f8' CLASS='tut'>
				<ul>
					<li>Het toevoegen van studenten gaat als volgt: <br />
					1. Klik op 'Toevoegen' In het menu 'Student';<br />
					2. Vul de gegevens in;<br />
					3. Klik op 'Opslaan';<br />
					4. De student is toegevoegd mits u niet bent vergeten een van de verplichte velden in te voeren;<br />
					5. u wordt naar het overzicht gebracht.<br />
					</li>
				</ul>
			</div>
		</li>
		<li><b><a ID="xf9" href="javascript:Toggle('f9')">[+]</a> Studenten importeren;</b>
		<div id='f9' class='tut'>
				<ul>
					<li>Het importeren van studenten gaat als volgt: <br />
					1. <SPAN CLASS='warning'>Zorg ervoor dat u een geldig .CSV bestand heeft!</SPAN><br />
						<ul>
							<li>Een CSV bestand mag max 7 kolommen bevatten, minder mag ook;<br />
								Leerlingnummer,<br />
								E-Mailadres,<br />
								Achternaam,<br />
								Voornaam,<br />
								Archief,<br />
								tutor,<br />
								locatie								
							</li><li>Waarvan de waarden zijn:<br />
							12345 (maximaal 5 nummers); <br />
							12345@ict-idcollege.nl; <br />
							AchterNaam (maximaal 50 tekens); <br />
							VoorNaam (maximaal 50 tekens); <br />
							0 of 1 (0 = niet archief, 1 wel); <br />
							Tutor naam;<br />
							Locatie;
							</li>
							<li>De velden zijn gescheiden met een ';' of een '|' of een '||'</li>
							<li>Elke rij staat op een aparte regel!</li>
						</ul>
					2. Klik op 'Importeren', in het menu 'Student';<br />
					3. Selecteer in de tweede selectiebox het scheidingsteken (';', '|', '||');<br />
					4. Klik op bladeren en zoek het CSV bestand op;<br />
					5. Klik op 'Verzenden';<br />
					6. U krijgt nu een hele rij met velden op het scherm;<br />
					7. In de selectievakjes selecteert u de kolomnamen die bij de kolommen horen;<br />
					8. Scroll naar beneden en klik de knop 'Importeer';<br />
					9. De leerlingen worden nu toegevoegd aan de database;<br />
					10. <SPAN CLASS='warning'>Denk eraan: Als een student nummer al bestaat wordt de geimporteerde student niet opgeslagen.</span>
					</li>
				</ul>
			</div>
		</li>
		<li><b><a ID="xf10" href="javascript:Toggle('f10')">[+]</a> Studenten aanpassen;</b>
		<div id='f10' CLASS='tut'>
				<ul>
					<li>Het aanpassen van studenten gaat als volgt: <br />
					1. Klik op 'Overzicht' In het menu 'Student';<br />
					2. U kunt zowel zoeken op een student, als de student in de lijst zoeken;
					<ul>
						<li>Zoeken: Typ (een gedeelte van) de achternaam in, en druk 'Zoek';</li>
						<li>Zoek de persoon op in de lijst.</li>
					</ul>
					3. Selecteer de student die u wilt aanpassen door op de 'Edit-knop' in de kolom 'Edit' te klikken;<br />
					4. Wijzig de gegevens die u wilt wijzigen;<br />
					5. Klik op 'aanpassen';<br />
					6. De wijziging is opgeslagen.<br />
					7. U wordt terug gebracht naar het overzicht.<br />
					</li>
				</ul>
			</div>
		</li>
		<li><b><a ID="xf11" href="javascript:Toggle('f11')">[+]</a> Teksten op de homepage van <a href='http://www.kennisbus.nl'>de Kennisbus</a> aanpassen;</b>
		<div id='f11' CLASS='tut'>
				<ul>
					<li>Het aanpassen van de tekst op de homepage gaat als volgt:<BR >
					1. Ga, in het menu 'Teksten', naar 'Aanpassen';<br />
					2. Selecteer de pagina die u wilt aanpassen in de selectiebox;<br />
					3. De tekst en de titel worden geladen in een formulier;<br />
					4. Pas de tekst of titel naar wens aan;<br />
					5. Klik op 'Verzenden';<br />
					6. De pagina is succesvol aangepast.<br />
					</li>
				</ul>
			</div>
		</li>
		<li><b><a ID="xf12" href="javascript:Toggle('f12')">[+]</a> Uw wachtwoord aanpassen.</b>
		<div id='f12' CLASS='tut'>
				<ul>
					<li>Het aanpassen van uw wachtwoord gaat als volgt: <br />
					1. Klik op 'Wachtwoord' in het menu 'Persoonlijk';<br />
					2. Voer het wachtwoord dat u nu gebruikt in, in het bovenste vakje;<br />
					3. Voer uw gewenste wachtwoord in, in het tweede vak;<br />
					4. Voer nogmaals uw gewenste wachtwoord in, in het derde vak;<br />
					5. Klik op aanpassen;<br />
					6. Uw wachtwoord is aangepast.<br />
					</li>
				</ul>
			</div>
		</li>
	</ul>

	<?php
	break;
}
?>
</div>