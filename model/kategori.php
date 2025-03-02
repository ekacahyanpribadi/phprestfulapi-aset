<?php
header("Content-Type: application/json");

$header = array();
$header = getallheaders();
//die();

//echo print_r($_POST); die();
$method = $_SERVER['REQUEST_METHOD'];

$input = json_decode(file_get_contents('php://input'), true);

//log params start
$actual_link1 = $_SERVER['REQUEST_URI'];
$actual_link = str_replace(' ', '', $actual_link1);
$inputJson = json_encode($input);
$jsonResponse = isset($GLOBALS["jsonResponse"]) ? $GLOBALS["jsonResponse"] : '';
$stsResponse = isset($GLOBALS["stsResponse"]) ? $GLOBALS["stsResponse"] : '';
$logMethod = $_SERVER['REQUEST_METHOD'];
//log params end


$reqToken = isset($header['token']) ? $header['token'] : '';

$sqlCekToken = "SELECT token FROM production.token_access WHERE token='$reqToken'";
//echo $sqlCekToken; die();
$resultCekToken = DB::query($sqlCekToken);
if (count($resultCekToken) < 1) {
    jsonResp(400, 'Token access tidak ditemukan!');
    logHit($logMethod, $actual_link, $inputJson, $stsResponse, $jsonResponse);
    return;
}

$pdo = "";

switch ($method) {
    case 'GET':
        handleGet($pdo);
        break;
    case 'POST':
        handlePost($pdo, $input);
        break;
    case 'PUT':
        handlePut($pdo, $input);
        break;
    case 'DELETE':
        handleDelete($pdo, $input);
        break;
    default:
        responseHasil(400, false, "Invalid_user request method");
        logHit($logMethod, $actual_link, $inputJson, $stsResponse, $jsonResponse);
        break;
}

function handleGet($pdo)
{
    $request = $_SERVER['REQUEST_URI'];
    $getId = explode("/", $request);
    $idUrl = $getId[count($getId) - 1];
    //echo $idUrl; die();

    $tambah = '';
    $header = array();
    $header = getallheaders();
    if (isset($header['id_kategori'])) {
        $cek = $header['id_kategori'];
    } else {
        $cek = "9";
    }
    //echo $cek; die();
    if ($cek == 9) {
        $tambah = " AND id_kategori!=0 ";
    } else {
        $tambah = "";
    }

    if ($idUrl != "") {
        $tambah .= " AND id_kategori='$idUrl' ";
    }


    $sql = "
	select
	`id_kategori`,
	`kategori`,
	`sub_kategori`,
	`keterangan`,
	`jumlah_aset`,
	`status_kategori`,
	`masa_manfaat`,
	`penyusutan_persen_pertahun`	
	from    
	`easet`.`kategori_aset`
    WHERE 1=1
      $tambah
	order by ins_date ASC
	";
    //echo $sql; die;

    $result = DB::query($sql);
    jsonResp(200, 'Success', $result);
}

function handlePost($pdo, $input)
{
    $pk = getPk();
    $id_kategori = $pk;
    $kategori = $input['kategori'];
    $sub_kategori = $input['sub_kategori'];
    $keterangan = $input['keterangan'];
    $jumlah_aset = $input['jumlah_aset'];
    $status_kategori = $input['status_kategori'];
    $masa_manfaat = $input['masa_manfaat'];
    $penyusutan_persen_pertahun = $input['penyusutan_persen_pertahun'];
    $ins_user = $input['ins_user'];

    $sql = "
    INSERT INTO `easet`.`kategori_aset` VALUES
  (
    '$id_kategori',
    '$kategori',
    '$sub_kategori',
    '$keterangan',
    '$jumlah_aset',
    '$status_kategori',
    '$masa_manfaat',
    '$penyusutan_persen_pertahun',
    '$ins_user',
    now(),
    '',
    ''
  );";
    //echo $sql; die;

    $sqlCekExisting = "SELECT kategori FROM kategori_aset WHERE kategori='$kategori'";
    //echo $sqlCekExisting; die();
    $resultCekExisting = DB::query($sqlCekExisting);
    if (count($resultCekExisting) > 0) {
        responseHasil(400, false, "Kategori tersebut sudah ada");
        logHit($logMethod, $actual_link, $inputJson, $stsResponse, $jsonResponse);
        return;
    }

    $results = DB::query($sql);
    if ($results) {
        jsonResp(200, 'Success', "Kategori: " . $kategori . " created successfully");
    } else {
        jsonResp(400, 'Error', "Kategori: " . $kategori . " not created");
    }

}

function handlePut($pdo, $input)
{
    $id_kategori = $input['id_kategori'];
    $kategori = $input['kategori'];
    $sub_kategori = $input['sub_kategori'];
    $keterangan = $input['keterangan'];
    $jumlah_aset = $input['jumlah_aset'];
    $status_kategori = $input['status_kategori'];
    $masa_manfaat = $input['masa_manfaat'];
    $penyusutan_persen_pertahun = $input['penyusutan_persen_pertahun'];
    $upd_user = $input['upd_user'];

    $sql = "
		update
		`easet`.`kategori_aset`
		set
		`kategori` = '$kategori',
		`sub_kategori` = '$sub_kategori',
		`keterangan` = '$keterangan',
		`jumlah_aset` = '$jumlah_aset',
		`status_kategori` = '$status_kategori',
		`masa_manfaat` = '$masa_manfaat',
		`penyusutan_persen_pertahun` = '$penyusutan_persen_pertahun',
		`upd_user` = '$upd_user',
		`upd_date` = now()
		where `id_kategori` = '$id_kategori';
    ";
    //echo $sql; die;

    $results = DB::query($sql);
    if ($results) {
        jsonResp(200, 'Success', "Kategori: " . $kategori . " update successfully");
    } else {
        jsonResp(400, 'Error', "Kategori: " . $kategori . " not update");
    }
}

function handleDelete($pdo, $input)
{
    $id_kategori = $input['id_kategori'];
    //echo $input['id_user']; die();

    $sql = "
	delete
	from
	`easet`.`kategori_aset`
	where `id_kategori` = '$id_kategori';
	";
    //echo $sql; die;
    if ($id_kategori != "") {
        $results = DB::query($sql);
        if ($results) {
            jsonResp(200, 'Success', "Kategori: " . $id_kategori . " delete successfully");
        } else {
            jsonResp(400, 'Error', "Kategori: " . $id_kategori . " not delete");
        }
    } else {
        jsonResp(400, "ID Kategori is empty", "");

    }

}

//insert to tabel log hit

logHit($logMethod, $actual_link, $inputJson, $stsResponse, $jsonResponse);

//echo jsonResp(200, 'Kategori');
?>