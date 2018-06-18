<html>
<head>
    <title>TESTING...</title>
</head>
<body>
<?php
$json = file_get_contents("https://eu.api.battle.net/wow/character/Twisting%20Nether/Mayron?locale=en_GB&apikey=nq2tpwy8tr5xvyxk4fn8rwjyhh6k85xe");
$obj = json_decode($json);
var_dump($obj);


$json = file_get_contents("https://eu.api.battle.net/wow/character/Twisting%20Nether/Mayron?fields=appearance&locale=en_GB&apikey=nq2tpwy8tr5xvyxk4fn8rwjyhh6k85xe");
$obj = json_decode($json);

echo "<img src=" . $obj->thumbnail . "</img>";

?>

</body>
</html>


