<?php
	require_once("etc/tables.inc.php");
	require_once("etc/names.inc.php");
	require_once("etc/database.inc.php");

	require_once("inc/classes/classes.inc.php");
	require_once("inc/classes/class_log.inc.php");
	require_once("inc/classes/class_database.inc.php");	

	$log = new Log();
	$db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
	$db->debug(0);

	$page = new Interface('default.css');


	$menu = array();
	$menu[0][link] = 'login.php';
	$menu[0][text] = 'Zurück zum Login';
	$menu[1][link] = 'http://www.Bithugo.de/';
	$menu[1][text] = 'Bithugo';
	$menu[2][link] = 'http://www.Tarnkappenbaum.org/';
	$menu[2][text] = 'TarnkappenBaum';
	$menu[3][text] = 'THW M&uuml;nchen-West';
	$menu[3][link] = 'http://www.thw-muenchen-west.de/';

	$page->html_header();
	$page->title_bar();

	$output = '
		<table width=100% align=center border=0>
			<tr>
				<td colspan=2 align=center>
					<h2>THW-Intern</h2>
				</td>
			</tr>

			<tr>
				<td widtH=50% align=right>
					Idee & Konzept:
				</td>
				<td>
					<a href="mailto:">Thomas Müller</a>, <a href="">Roland Diera</a> und <a href="mailto:Jakob@TarnkappenBaum.org">Jakob Külzer</a>
				</td>
			</tr>
			<tr>
				<td widtH=50% align=right>
					Dokumentation:
				</td>
				<td>
					<a href="">Roland Diera</a>
				</td>
			</tr>
			<tr>
				<td widtH=50% align=right>
					Informatikgott:
				</td>
				<td>
					<a href="mailto:">Thomas Müller</a>
				</td>
			</tr>
			<tr>
				<td widtH=50% align=right>
					Javascriptgott:
				</td>
				<td>
					<a href="">Roland Diera</a>
				</td>
			</tr>
			<tr>
				<td widtH=50% align=right>
					Lord of the source:
				</td>
				<td>
					<a href="mailto:Jakob@TarnkappenBaum.org">Jakob Külzer</a>
				</td>
			</tr>

			<tr>
				<td widtH=50% align=right>
					Programmierung:
				</td>
				<td>
					<a href="mailto:Jakob@TarnkappenBaum.org">Jakob Külzer</a>
				</td>
			</tr>
			<tr>
				<td widtH=50% align=right>
					Datenbankentwicklung:
				</td>
				<td>
					<a href="mailto:Jakob@TarnkappenBaum.org">Jakob Külzer</a> und <a href="">Roland Diera</a>
				</td>
			</tr>
			<tr>
				<td widtH=50% align=right rowspan=1>
					Beta-Tester:
				</td>
				<td>
					Andreas "Mr. W/P" Bieleck (Er hat wirklich ALLES ausprobiert! Danke!)
				</td>
			</tr>

			<tr>
				<td colspan=2 align=center>
					Und vielen Dank an <b>ALLE</b> die uns irgendwie in irgend einer Form unterstützt haben und
					den OV München-West, der mir für die Dauer der Entwicklung in der Unterkunft (<b>14</b> Tage) ein neues Zuhause zur Verfügung
					gestellt hat!! (o;
				</td>
			</tr>
			<tr>
				<td align=right>
					Begin der Entwicklung (UK):
				</td>
				<td>
					<b>26. Mai 2003</b>
				</td>
			</tr>
			<tr>
				<td align=right>
					Ende der Entwicklung (UK):
				</td>
				<td>
					<b>8. Juni 2003</b>
				</td>
			</tr>
			<tr>
				<td align=right>
					Entwicklung (Fortsetzung):
				</td>
				<td>
					<b>16. Juni 2003</b>
				</td>
			</tr>
			<tr>
				<td align=right>
					Ende der Entwicklung (Fortsetzung):
				</td>
				<td>
					<b>??</b>
				</td>
			</tr>
			<tr>
				<td align=right>
					Verspeiste Döner:
				</td>
				<td>
					<b>Unzählige</b>
				</td>
			</tr>
			<tr>
				<td align=right>
					Verbrauchtes Cappuchinopulver:
				</td>
				<td>
					<b>bis jetzt ca. 500g (Update: Jetzt wohl eher schon 1,5kg)</b>
				</td>
			</tr>
			<tr>
				<td align=right>
					Verbrauchtes Spezi/Citron:
				</td>
				<td>
					<b>Mehr als unzählig!!</b>
				</td>
			</tr>
		</table>

		';
	echo $page->dialog_box('Credits', $output, $menu, 0,  '');

	$page->html_footer();

?>
