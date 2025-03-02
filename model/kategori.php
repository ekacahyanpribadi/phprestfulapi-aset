<?php
header("Content-Type: application/json");

$id_log = getPk();
//$jsonResponse='';
function responseHasil($code, $status, $data)
{
    $jsonData = array(
        'code' => $code,
        'status' => $status,
        'data' => $data
    );
    echo json_encode($jsonData);

    $GLOBALS["jsonResponse"] = json_encode($jsonData);
}

$header=array();
$header=getallheaders();
//die();


$reqToken = isset($header['token']) ? $header['token'] : '';

$sqlCekToken = "SELECT token FROM production.token_access WHERE token='$reqToken'";
//echo $sqlCekToken; die();
$resultCekToken = DB::query($sqlCekToken);
if (count($resultCekToken) < 1) {
    echo json_response(400, 'Token access tidak ditemukan!');
    return;
}

//echo print_r($_POST); die();
$method = $_SERVER['REQUEST_METHOD'];

$input = json_decode(file_get_contents('php://input'), true);
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
        break;
}



function handleGet($pdo)
{
    $tambah='';
    $header=array();
    $header=getallheaders();
    if(isset($header['id_kategori'])){$cek = $header['id_kategori'];} else {$cek = "9";}
    //echo $cek; die();
    if($cek==9){
        $tambah="AND id_kategori!=0";
    }else{
        $tambah="";
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
    
    $result = DB::query($sql);
    //responseHasil(200, true, $result);
    echo json_response(200, 'Data kategori',$result);
    //echo json_encode($result);  
}

function handlePost($pdo, $input)
{
    $pk = getPk();
	$id_kategori=$pk;
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
    'penyusutan_persen_pertahun',
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
        return;
    }

    $results = DB::query($sql);
    if ($results) {
        responseHasil(200, true, "Kategori: " . $kategori . " created successfully");
    } else {
        responseHasil(400, false, "Kategori: " . $kategori . " not created");
    }

}

function handlePut($pdo, $input)
{
    $id_kategori=$input['id_kategori'];
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
        responseHasil(200, true, "Kategori: " . $kategori . " update successfully");
    } else {
        responseHasil(400, false, "Kategori: " . $kategori . " not update");
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
            responseHasil(200, true, "Kategori: " . $id_kategori . " delete successfully");
        } else {
            responseHasil(400, false, "Kategori: " . $id_kategori . " not delete");
        }
    } else {
        responseHasil(400, false, "ID Kategori is empty");
    }

}

//insert to tabel log hit
$actual_link = $_SERVER['REQUEST_URI'];
$actual_link = str_replace(' ', '', $actual_link);
$jsonResponse = $GLOBALS["jsonResponse"];
$inputJson = json_encode($input);
//$headerJson =json_encode($all_headers);
$sqlLogHit = "
insert into `easet`.`log_hit` values
  (
    '$id_log',
    now(),
    '$method',
    '$actual_link',
    '$inputJson',
    '$jsonResponse'
  );
";
//echo $sqlLogHit; die();
$resultLogHit = DB::query($sqlLogHit);
if ($resultLogHit) {

} else {
    responseHasil(400, false, "Log gagal disimpan");
    return;
}

//echo json_response(200, 'Kategori');
?>