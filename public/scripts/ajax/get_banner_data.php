<?php
require_once("../../../includes/init.php");
if (array_key_exists("requestBannerData", $_POST)) {
    header('Content-Type: text/json');
    $requestNum = (integer) $_POST["requestBannerData"];

    $num_rows = DB::query("SELECT COUNT(*) FROM projects WHERE featured = 1;");
    $num_rows = $num_rows->fetchColumn();

    $result = DB::query("SELECT id FROM projects WHERE featured = 1;");

    if ($requestNum > ($num_rows - 1)) $requestNum = 0;

    $nextNum = $requestNum + 1;
    if ($nextNum > ($num_rows - 1)) $nextNum = 0;

    $project = false;
    for ($i = 0; $i <= $requestNum; $i++) {
        if ($result && $row = $result->fetch()){
            if ($i === $requestNum) {
                $project = new Project($row["id"]);
                break;
            }
        } else {
            echo "failed";
            exit();
        }
    }

    if ($project) {
        $response = $project->packData(false);
        $response = array_merge($response, array(
            "success" => true,
            "nextNum" => $nextNum,
        ));
        echo json_encode($response);
    } else {
        echo "something wrong!";
    }
} else {
    echo "<p>failed</p>";
    print_array($_POST);
}
