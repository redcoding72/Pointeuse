<?php
function soustraireJourYMD($dateYMD) {
  // Convertir la date YMD en timestamp UNIX
  $timestamp = strtotime($dateYMD);

  // Soustraire un jour au timestamp
  $timestamp -= 86400;

  // Extraire la date YMD du timestamp UNIX soustrait
  $dateSoustraiteYMD = substr(date("Ymd", $timestamp), 0, 8);

  return $dateSoustraiteYMD;
}

$dateYMD = date('Ymd'); // Date au format YMD
$dateSoustraiteYMD = soustraireJourYMD($dateYMD);
//today
$d=$dateSoustraiteYMD;
$d1=$d.' 00:00:00' ;
$d2=$d.' 23:59:59' ;
// Paramètres de connexion à la base de données SQL Server
$serverName = "SRV-DC1"; // Nom du serveur
$connectionOptions = array(
    "Database" => "Pointeusedb", // Nom de la base de données
    "Uid" => "sa", // Nom d'utilisateur
    "PWD" => "123456789" // Mot de passe
);

// Connexion à la base de données SQL Server
$conn = sqlsrv_connect($serverName, $connectionOptions);

// Vérifier la connexion
if ($conn === false) {
    echo "Erreur de connexion à la base de données : " . sqlsrv_errors();
    exit();
}	
// Requête SQL SELECT
$sql = "select dbo.USERINFO.BADGENUMBER, dbo.USERINFO.NAME, FORMAT(dbo.CHECKINOUT.CHECKTIME,'dd/MM/yyyy HH:mm:ss')  from dbo.USERINFO 
inner join dbo.CHECKINOUT on dbo.USERINFO.USERID = dbo.CHECKINOUT.USERID
where CHECKTIME >= '".$d1."' and CHECKTIME <= '".$d2."' ORDER BY dbo.USERINFO.BADGENUMBER";

// Exécution de la requête
$stmt = sqlsrv_query($conn, $sql);

// Vérifier si la requête a réussi
if ($stmt === false) {
    echo "Erreur lors de l'exécution de la requête : " . sqlsrv_errors();
    exit();
}

// Création du fichier texte pour enregistrer les résultats
$fileName = "resultats_checkinout".$d.".csv";
//$fileName = $fileNName.$d;
$file = fopen($fileName, "w") or die("Impossible de créer le fichier!");

// Parcourir les résultats et les écrire dans le fichier texte
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    // Écrire chaque ligne dans le fichier texte
    fwrite($file, implode(";", $row) . "\n");
}

// Fermer le fichier
fclose($file);

// Fermer la connexion à la base de données
sqlsrv_close($conn);

echo "Les résultats ont été enregistrés dans le fichier $fileName.";




