<?php
$db = new PDO('sqlite:data.db');

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	$tid = time();

	$request = $db->prepare('INSERT INTO afkrydsninger (tid) VALUES (:tid)');
	$request->execute(array(
		':tid' => $tid
	)) or die('Fejl i oprettelse af afkrydsning.');

	$afkrydsning_id = $db->lastInsertId();

	$request = $db->prepare('
		INSERT INTO spejderkryds
			(afkrydsning_id, spejder_id, status)
		VALUES
			(:afkrydsning_id, :spejder_id, :status)
		');

	foreach($_POST as $key => $value) {
		if (preg_match('/^status(\d+)$/', $key, $match)) {
			$request->execute(array(
				':afkrydsning_id' => $afkrydsning_id,
				':spejder_id'     => $match[1],
				':status'         => $value
			));
		}
	}
}

$result = $db->query('SELECT * FROM spejdere');
$result->setFetchMode(PDO::FETCH_OBJ);

$spejdere = array();
foreach ($result as $row) {
	$spejdere[] = $row;
}

$result = $db->query('SELECT * FROM afkrydsninger');
$result->setFetchMode(PDO::FETCH_OBJ);

$afkrydsninger = array();
foreach ($result as $row) {
	$afkrydsninger[] = $row;
}

$result = $db->query('SELECT * FROM spejderkryds');
$result->setFetchMode(PDO::FETCH_OBJ);

$spejderkryds = array();
foreach ($result as $row) {
	$spejderkryds[$row->spejder_id][$row->afkrydsning_id] = $row->status;
}
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
		<table>
			<tr class="header">
				<td>&nbsp;</td>
<? foreach ($afkrydsninger as $afkrydsning) { ?>
				<th class="rotate"><div><span><? echo htmlspecialchars(strftime("%e. %b %Y", $afkrydsning->tid)); ?></span></div></th>
<? } ?>
			</tr>
<? foreach ($spejdere as $spejder) { ?>
			<tr>
				<td><? echo htmlspecialchars($spejder->fornavn ." ".$spejder->efternavn); ?></td>
<?
foreach ($afkrydsninger as $afkrydsning) {
	switch ($spejderkryds[$spejder->id][$afkrydsning->id]) {
	case "1":
		$color = "#9D9";
		break;
	case "2":
		$color = "#D99";
		break;
	default:
		$color = "#DDD";
		break;
	}
?>
				<td style="background-color: <? echo $color; ?>;"></td>
<? } ?>
			</tr>
<? } ?>
		</table>
	</body>
</html>
