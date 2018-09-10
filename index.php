<?php
$db = new PDO('sqlite:data.db');

$prereq = $db->query('
	CREATE TABLE IF NOT EXISTS spejdere (
		id integer primary key autoincrement,
		fornavn text,
		efternavn text,
		spejdernavn text,
		skole text,
		klasse text,
		patrulje text,
		rolle int,
		billede text
	)');
$prereq = $db->query('
	CREATE TABLE IF NOT EXISTS afkrydsninger (
		id integer primary key autoincrement,
		tid integer
	)');
$prereq = $db->query('
	CREATE TABLE IF NOT EXISTS spejderkryds (
		id integer primary key autoincrement,
		status integer,
		afkrydsning_id integer REFERENCES afkrydsninger(id) ON DELETE CASCADE,
		spejder_id integer REFERENCES spejdere(id) ON DELETE CASCADE
	)');

$result = $db->query('SELECT * FROM spejdere');
$result->setFetchMode(PDO::FETCH_OBJ);

$spejdere = array();
foreach ($result as $row) {
	$spejdere[] = $row;
}

$sort_field = 'fornavn';
$headers = false;
$last_header = '';

if (isset($_GET['sort'])) {
	switch ($_GET['sort']) {
	case 'fornavn':
	case 'efternavn':
	case 'spejdernavn':
		$sort_field = $_GET['sort'];
		$headers = false;
		break;
	case 'skole':
	case 'patrulje':
		$sort_field = $_GET['sort'];
		$headers = true;
		break;
	}
}

usort($spejdere, function($a, $b) use ($sort_field) {
	return strcmp($a->{$sort_field}, $b->{$sort_field});
});
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" type="text/css" href="style.css">
		<title>Spejderbog</title>
	</head>
	<body>
		<form method="get" action="edit.php">
			<input type="hidden" name="id" value="new">
			<input type="submit" value="Ny spejder" style="position: absolute; top: 25px; right: 5px; z-index: 1;">
		</form>
		<form method="get">
			<select id="sortpick" name="sort" onchange="this.form.submit()" style="position: absolute; top: 5px; right: 5px; z-index: 1;">
				<option <? if ($sort_field == 'fornavn')     {print 'selected="selected"';} ?> value="fornavn">Sortér: Fornavn</option>
				<option <? if ($sort_field == 'efternavn')   {print 'selected="selected"';} ?> value="efternavn">Sortér: Efternavn</option>
				<option <? if ($sort_field == 'spejdernavn') {print 'selected="selected"';} ?> value="spejdernavn">Sortér: Spejdernavn</option>
				<option <? if ($sort_field == 'skole')       {print 'selected="selected"';} ?> value="skole">Sortér: Skole</option>
				<option <? if ($sort_field == 'patrulje')    {print 'selected="selected"';} ?> value="patrulje">Sortér: Patrulje</option>
			</select>
		</form>
		<div id="container">
		<ul class="list">
<?php
foreach ($spejdere as $spejder) {
	if ($headers) {
		if ($last_header != $spejder->{$sort_field}) {
			$last_header = $spejder->{$sort_field};
			printf("\t\t\t<h1>%s</h1>\n", $spejder->{$sort_field});
		}
	}
?>
			<li id="spejder<? echo htmlspecialchars($spejder->id); ?>" onclick="toggleColor('<? echo htmlspecialchars($spejder->id); ?>')">
				<div class="img"><img src="img/<? echo htmlspecialchars($spejder->billede); ?>"></div>
				<dl>
					<dt>Navn:&nbsp;</dt>
					<dd><? echo htmlspecialchars($spejder->fornavn), ' ', htmlspecialchars($spejder->efternavn); ?> </dd>
					<dt>Patrulje:&nbsp;</dt>
					<dd><? echo htmlspecialchars($spejder->patrulje); ?> </dd>
					<dt>Spejdernavn:&nbsp;</dt>
					<dd><? echo htmlspecialchars($spejder->spejdernavn); ?> </dd>
					<dt>Skole:&nbsp;</dt>
					<dd><? echo htmlspecialchars($spejder->skole); ?> </dd>
					<dt>Klasse:&nbsp;</dt>
					<dd><? echo htmlspecialchars($spejder->klasse); ?> </dd>
				</dl>
				<form method="get" action="edit.php">
					<input type="hidden" name="id" value="<? echo htmlspecialchars($spejder->id); ?>">
					<input type="submit" value="Redigér">
				</form>
			</li>
<? } ?>
		</ul>
		</div>
		<form method="post" action="attendance.php">
<?php
foreach ($spejdere as $spejder) {
?>
			<input type="hidden" name="status<? echo htmlspecialchars($spejder->id); ?>" id="status<? echo htmlspecialchars($spejder->id); ?>" value="0">
<? } ?>
			<input type="submit" value="Gem afkrydsning" style="position: absolute; top: 45px; right: 5px; z-index: 1;" id="attendbutton" disabled>
		</form>
		<script>
			function toggleColor(id) {
				document.getElementById('attendbutton').disabled = false;
				switch (document.getElementById('status' + id).value) {
				case "0":
					document.getElementById('spejder' + id).style.backgroundColor = "#9D9";
					document.getElementById('status' + id).value = "1";
					break;
				case "1":
					document.getElementById('spejder' + id).style.backgroundColor = "#D99";
					document.getElementById('status' + id).value = "2";
					break;
				case "2":
					document.getElementById('spejder' + id).style.backgroundColor = "#DDD";
					document.getElementById('status' + id).value = "0";
					break;
				}
			}
		</script>
	</body>
</html>
