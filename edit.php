<?php
$db = new PDO('sqlite:data.db');

$id          = 'new';
$fornavn     = '';
$efternavn   = '';
$spejdernavn = '';
$skole       = '';
$klasse      = '';
$patrulje    = '';
$billede     = 'tom.png';
$overskrift  = 'Ny spejder';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	if ($_FILES['files']['size'][0] === 0) {
		$db = new PDO('sqlite:data.db');

		if ($_POST['id'] == 'new') {
			$request = $db->prepare('
				INSERT INTO spejdere ( fornavn, efternavn, spejdernavn, skole, klasse, patrulje, billede)
				VALUES (:fornavn, :efternavn, :spejdernavn, :skole, :klasse, :patrulje, :billede)');
			$request->execute(array(
				':fornavn'     => $_POST['fornavn'],
				':efternavn'   => $_POST['efternavn'],
				':spejdernavn' => $_POST['spejdernavn'],
				':skole'       => $_POST['skole'],
				':klasse'      => $_POST['klasse'],
				':patrulje'    => $_POST['patrulje'],
				':billede'     => $_POST['billede']
			)) or die('Fejl i oprettelse.');
		} else {
			if (isset($_POST['delete'])) {
				$request = $db->prepare('DELETE FROM spejdere WHERE id = :id');
				$request->execute(array(':id' => $_POST['id'])) or die('Fejl i sletning.');
			} else {
				$request = $db->prepare('
					UPDATE spejdere SET
						fornavn = :fornavn,
						efternavn = :efternavn,
						spejdernavn = :spejdernavn,
						skole = :skole,
						klasse = :klasse,
						patrulje = :patrulje,
						billede = :billede
					WHERE
						id = :id');
				$request->execute(array(
					':id'          => $_POST['id'],
					':fornavn'     => $_POST['fornavn'],
					':efternavn'   => $_POST['efternavn'],
					':spejdernavn' => $_POST['spejdernavn'],
					':skole'       => $_POST['skole'],
					':klasse'      => $_POST['klasse'],
					':patrulje'    => $_POST['patrulje'],
					':billede'     => $_POST['billede']
				)) or die('Fejl i redigering.');
			}
		}

		header('Location: ./');
	} else {
		$top_index = 0;
		$last_img = '';
		foreach ($_FILES['files']['tmp_name'] as $index => $name) {
			$top_index = $index;

			$image = imagecreatefromstring(file_get_contents($name));
			if ($image === false) {
				continue;
			}

			$image = imagerotate($image, array_values([0, 0, 0, 180, 0, 0, -90, 0, 90])[@exif_read_data($name)['Orientation'] ?: 0], 0);
			if (imagesx($image) > 1000) {
				$image = imagescale($image, 1000);
			}

			foreach (range(0, 1000000) as $number) {
				if (!file_exists("img/img$number.jpg")) {
					imagejpeg($image, "img/img$number.jpg");
					$last_img = "img$number.jpg";
					break;
				}
			}
		}
		$id          = $_POST['id'];
		$fornavn     = $_POST['fornavn'];
		$efternavn   = $_POST['efternavn'];
		$spejdernavn = $_POST['spejdernavn'];
		$skole       = $_POST['skole'];
		$klasse      = $_POST['klasse'];
		$patrulje    = $_POST['patrulje'];
		$billede     = $_POST['billede'];

		if ($top_index == 0) {
			$billede = $last_img;
		}

		if ($id === 'new') {
			$overskrift = 'Ny spejder';
		} else {
			$overskrift = 'Redigér spejder';
		}
	}
} else {
	$request = $db->prepare('SELECT * FROM spejdere WHERE id = :id');
	$request->execute(array(':id' => $_GET['id']));
	$request->setFetchMode(PDO::FETCH_OBJ);

	foreach ($request as $row) {
		$id          = $row->id;
		$fornavn     = $row->fornavn;
		$efternavn   = $row->efternavn;
		$spejdernavn = $row->spejdernavn;
		$skole       = $row->skole;
		$klasse      = $row->klasse;
		$patrulje    = $row->patrulje;
		$billede     = $row->billede;
		$overskrift  = 'Redigér spejder';
	}
}

$in_use = array();
$request = $db->query('SELECT billede FROM spejdere;');
$request->setFetchMode(PDO::FETCH_OBJ);

foreach ($request as $row) {
	$in_use[] = $row->billede;
}

$unused = array();
if ($dirfh = opendir('img/')) {
	while (false !== ($entry = readdir($dirfh))) {
		if (substr($entry, 0, 1) === '.') {
			continue;
		}
		if (!in_array($entry, $in_use)) {
			$unused[] = $entry;
		}
	}
	closedir($dirfh);
}

if (!in_array('tom.png', $unused)) {
	$unused[] = 'tom.png';
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
		<form method="POST" enctype="multipart/form-data">
		<div id="container">
		<h1><? echo $overskrift; ?></h1>
			<input type="hidden" name="id" value="<? echo htmlspecialchars($id); ?>">
			<input id="billede" type="hidden" name="billede" value="<? echo htmlspecialchars($billede); ?>">
			<ul class="list" id="list">
				<li style="height: 300px; width: 600px;">
					<div class="img"><img id="active" src="img/<? echo htmlspecialchars($billede); ?>"></div>
					<table>
						<tr><th>Navn: </th><td><input type="text" name="fornavn" value="<? echo htmlspecialchars($fornavn); ?>"><input type="text" name="efternavn" value="<? echo htmlspecialchars($efternavn); ?>"></td></tr>
						<tr><th>Patrulje: </th><td><input type="text" name="patrulje" value="<? echo htmlspecialchars($patrulje); ?>"></td></tr>
						<tr><th>Spejdernavn: </th><td><input type="text" name="spejdernavn" value="<? echo htmlspecialchars($spejdernavn); ?>"></td></tr>
						<tr><th>Skole: </th><td><input type="text" name="skole" value="<? echo htmlspecialchars($skole); ?>"></td></tr>
						<tr><th>Klasse: </th><td><input type="text" name="klasse" value="<? echo htmlspecialchars($klasse); ?>"></td></tr>
					</table>
					<input name="delete" type="submit" value="Slet" style="right: 45px">
					<input name="save" type="submit" value="Gem">
				</li>
			</ul>
		</div>
		<h3>Vælg billede</h3>
		<div style="clear: both; background: #eee;">
			<label for="file">Upload billeder:</label>
			<input type="file" id="file" name="files[]" multiple="multiple" accept="image/*" onchange="this.form.submit()">
		</div>
		<div style="clear: left;">
<?php
	foreach ($unused as $image) {
?>
	<img  src="img/<? echo htmlspecialchars($image); ?>" style="height: 200px; cursor: pointer; cursor: hand;" onclick="document.getElementById('billede').value = '<? echo htmlspecialchars($image); ?>';document.getElementById('active').src = 'img/<? echo htmlspecialchars($image); ?>';" title="<? echo htmlspecialchars($image); ?>">
<?php
	}
?>
		</div>
		</form>
	</body>
</html>
