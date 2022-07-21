<?php
try {
    $bdd = new PDO('mysql:host=localhost;port=3308;dbname=pays;charset=utf8', 'root', 'dwwm2022');
} catch (PDOException $e) {
    print "Erreur : " . $e->getMessage() . "<br/>";
    die();
}

$reqLibelleRegion = 'SELECT id_region, libelle_region FROM t_regions ORDER BY libelle_region';

if(isset($_GET["continent"]) &&$_GET["continent"]) {

    $req='SELECT tr.libelle_region AS  nom, SUM(population_pays) AS population , AVG(taux_natalite_pays) AS natalite, AVG(taux_mortalite_pays) AS mortalite, AVG(esperance_vie_pays) AS esperance, AVG(taux_mortalite_infantile_pays) AS mortaliteEnfant, AVG(nombre_enfants_par_femme_pays) AS enfantFemme, AVG(taux_croissance_pays) AS croissance, SUM(population_plus_65_pays) AS pop65 FROM t_regions tr INNER JOIN t_pays tp ON tp.region_id = tr.id_region WHERE tp.continent_id = '.$_GET["continent"].' GROUP BY tr.libelle_region ASC;';
    $reqLibelleRegion = 'SELECT id_region, libelle_region  FROM t_regions WHERE continent_id='.$_GET["continent"].' ORDER BY libelle_region';

} else {

    $req='SELECT tc.libelle_continent AS nom, SUM(population_pays) AS population , AVG(taux_natalite_pays) AS natalite, AVG(taux_mortalite_pays) AS mortalite, AVG(esperance_vie_pays) AS esperance, AVG(taux_mortalite_infantile_pays) AS mortaliteEnfant, AVG(nombre_enfants_par_femme_pays) AS enfantFemme, AVG(taux_croissance_pays) AS croissance, SUM(population_plus_65_pays) AS pop65 FROM t_continents tc INNER JOIN t_pays tp ON tp.continent_id = tc.id_continent GROUP BY tc.libelle_continent ASC;';

}

if (isset($_GET["continent"]) &&$_GET["continent"] == 3 ) {

    $req='SELECT tp.libelle_pays AS  nom, SUM(population_pays) AS population , AVG(taux_natalite_pays) AS natalite, AVG(taux_mortalite_pays) AS mortalite, AVG(esperance_vie_pays) AS esperance, AVG(taux_mortalite_infantile_pays) AS mortaliteEnfant, AVG(nombre_enfants_par_femme_pays) AS enfantFemme, AVG(taux_croissance_pays) AS croissance, SUM(population_plus_65_pays) AS pop65 FROM t_pays tp 
    INNER JOIN t_continents tc ON tc.id_continent = tp.continent_id
    WHERE tc.id_continent=3
    GROUP BY tp.libelle_pays';

};

if (isset($_GET["region"]) &&$_GET["region"]) {

    $req='SELECT tp.libelle_pays AS  nom, SUM(population_pays) AS population , AVG(taux_natalite_pays) AS natalite, AVG(taux_mortalite_pays) AS mortalite, AVG(esperance_vie_pays) AS esperance, AVG(taux_mortalite_infantile_pays) AS mortaliteEnfant, AVG(nombre_enfants_par_femme_pays) AS enfantFemme, AVG(taux_croissance_pays) AS croissance, SUM(population_plus_65_pays) AS pop65
    FROM t_pays tp INNER JOIN t_regions tr ON tr.id_region = tp.region_id WHERE region_id='.$_GET["region"].' GROUP BY tp.libelle_pays';

} ;


$reqContinent = $bdd->prepare($req);
$reqContinent->execute();
$datasContinent = $reqContinent->fetchAll();

$reqLibelleContinent = 'SELECT id_continent,libelle_continent FROM t_continents ORDER BY libelle_continent';
$listeContinent = $bdd->prepare($reqLibelleContinent);
$listeContinent->execute();
$listeContinentDb = $listeContinent->fetchAll();

$listeRegion = $bdd->prepare($reqLibelleRegion);
$listeRegion->execute();
$listeRegionDb = $listeRegion->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Démographie</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
</head>

<body>
    <div>
        <h1>Démographie mondiale</h1>
    </div>

    <div>
        <form action="/" method="GET">
            <select name="continent" id="" onchange="this.form.submit()">
                <option value="0">Monde</option>

                <?php foreach ($listeContinentDb as $dataContinent) : ?>
                    
                    <?php if ($dataContinent['id_continent'] == $_GET['continent']) : ?>
                        <option value="<?= $dataContinent['id_continent'] ?>" selected ><?= $dataContinent['libelle_continent'] ?></option>
                    <?php else : ?>
                        <option value="<?= $dataContinent['id_continent'] ?>"><?= $dataContinent['libelle_continent'] ?></option>
                    <?php endif ?>

                <?php endforeach ?>

            </select>

            <select name="region" id="" onchange="this.form.submit()">
                <option value="0">Région</option>

                <?php foreach ($listeRegionDb as $dataRegion) : ?>

                    <?php if ($dataRegion['id_region'] == $_GET['region']) : ?>
                        <option value="<?= $dataRegion['id_region'] ?>" selected ><?= $dataRegion['libelle_region'] ?></option>
                    <?php else : ?>
                        <option value="<?= $dataRegion['id_region'] ?>"><?= $dataRegion['libelle_region'] ?></option>
                    <?php endif ?>

                <?php endforeach ?>

            </select>
        </form>

    </div>
    <table class="table table-striped">
        <tr>
            <th>Nom</th>
            <th>Population totale (en milliers)</th>
            <th>Taux de natalité</th>
            <th>Taux de mortalité</th>
            <th>Espérance de vie</th>
            <th>Taux de mortalité infantile</th>
            <th>Nombre d\'enfant(s) par femme</th>
            <th>Taux de croissance</th>
            <th>Population de 65 ans et plus (en milliers)</th>
        </tr>

        <?php foreach ($datasContinent as $data) : ?>
            <tr>
                <td><?= $data['nom'] ?></th>
                <td><?= $data['population'] ?></th>
                <td><?= round($data['natalite'],1) ?></th>
                <td><?= round($data['mortalite'],1) ?></th>
                <td><?= round($data['esperance'],1) ?></th>
                <td><?= round($data['mortaliteEnfant'],1) ?></th>
                <td><?= round($data['enfantFemme'],1) ?></th>
                <td><?= round($data['croissance'],1) ?></th>
                <td><?= $data['pop65'] ?></th>

            </tr>
        <?php endforeach ?>

    
    </table>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>

</html>