<?php
//require __DIR__ . '/lib.php';

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
$cekHeader=$header['authorization'];
if($cekHeader!="20250215161022FY9H01"){
    responseHasil(400, false, "Token authorization invalid!");
    exit;
}



try {
    $pdo = new PDO('mysql:host=localhost;dbname=easet', 'eka', 'eka');

} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
    exit();
}
//echo print_r($_POST); die();
$method = $_SERVER['REQUEST_METHOD'];

$input = json_decode(file_get_contents('php://input'), true);

//echo $input['id_user']; die();

$input['token']=$cekHeader;

if(!isset($input['token'])){$token = "";}else{$token = $input['token'];}

if ($token == "") {
    responseHasil(400, false, "Token request kosong");
    return;
}

$sqlCekToken = "SELECT token FROM token WHERE token='$token'";
//echo $sqlCekToken; die();
$resultCekToken = DB::query($sqlCekToken);
if (count($resultCekToken) < 1) {
    responseHasil(400, false, "Token request invalid");
    return;
}

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
        //echo json_encode(['message' => 'Invalid_user request method']);
        responseHasil(400, false, "Invalid_user request method");
        break;
}



function handleGet($pdo)
{
    $tambah='';
    $header=array();
    $header=getallheaders();
    if(isset($header['id_aset'])){$cek = $header['id_aset'];} else {$cek = "9";}
    //echo $cek; die();
    if($cek==9){
        $tambah="AND id_aset!=0";
    }else{
        $tambah="";
    }

    $sql = "
        select
          `id_aset`,
          `kode`,
          `nama`,
          `kategori_aset`,
          `merk`,
          `tipe`,
          `produsen`,
          `no_seri`,
          `tahun_produksi`,
          `deskripsi`,
          `tanggal_pembelian`,
          `toko_distributor`,
          `no_invoice`,
          `jumlah`,
          `harga_satuan`,
          `harga_total`,
          `foto`,
          `lampiran`,
          `keterangan_tambahan`,
          `tahun_penyusutan`,
          `penyusutan_perbulan`,
          `status_aset`
        from
          `easet`.`aset`
    WHERE 1=1
      $tambah
	ORDER BY ins_date ASC
	";
    $result = DB::query($sql);
    responseHasil(200, true, $result);
    //echo json_encode($result);
    
}

function handlePost($pdo, $input)
{
    $pk = getPk();
	$id_aset = $pk;
    $penanggung_jawab = $input['penanggung_jawab'];
    $no_telepon = $input['no_telepon'];
    $alamat = $input['alamat'];
    $email = $input['email'];
    $jabatan = $input['jabatan'];
    $instansi = $input['instansi'];
    $keterangan = $input['keterangan'];
    $status_pj = $input['status_pj'];    
	$ins_user = $input['ins_user'];

    $sql = "
        INSERT INTO `easet`.`penanggung_jawab` VALUES
          (
            '$id_aset',
            '$penanggung_jawab',
            '$no_telepon',
            '$alamat',
            '$email',
            '$jabatan',
            '$instansi',
            '$keterangan',
            '$status_pj',
            '$ins_user',
            now(),
            '',
            ''
          );
	";
    //echo $sql; die;

    $sqlCekExisting = "SELECT penanggung_jawab FROM penanggung_jawab WHERE penanggung_jawab='$penanggung_jawab'";
    //echo $sqlCekExisting; die();
    $resultCekExisting = DB::query($sqlCekExisting);
    if (count($resultCekExisting) > 0) {
        responseHasil(400, false, "penanggung_jawab tersebut sudah ada");
        return;
    }

    $results = DB::query($sql);
    if ($results) {
        responseHasil(200, true, "Penanggung Jawab: " . $penanggung_jawab . " created successfully");
    } else {
        responseHasil(400, false, "Penanggung Jawab: " . $penanggung_jawab . " not created");
    }

}

function handlePut($pdo, $input)
{
    //$pk = $input['pk'];
    $id_aset = $input['id_aset'];
    $penanggung_jawab = $input['penanggung_jawab'];
    $no_telepon = $input['no_telepon'];
    $alamat = $input['alamat'];
    $email = $input['email'];
    $jabatan = $input['jabatan'];
    $instansi = $input['instansi'];
    $keterangan = $input['keterangan'];
    $status_pj = $input['status_pj'];
	$upd_user = $input['upd_user'];

    $sql = "
        update
          `easet`.`penanggung_jawab`
        set
          `penanggung_jawab` = '$penanggung_jawab',
          `no_telepon` = '$no_telepon',
          `alamat` = '$alamat',
          `email` = '$email',
          `jabatan` = '$jabatan',
          `instansi` = '$instansi',
          `keterangan` = '$keterangan',
          `status_pj` = '$status_pj',
          `upd_user` = '$upd_user',
          `upd_date` = now()
        where `id_aset` = '$id_aset';
    ";
    //echo $sql; die;

    $results = DB::query($sql);
    if ($results) {
        responseHasil(200, true, "Penanggung Jawab: " . $penanggung_jawab . " update successfully");
    } else {
        responseHasil(400, false, "Penanggung Jawab: " . $penanggung_jawab . " not update");
    }
}

function handleDelete($pdo, $input)
{
    $id_aset = $input['id_aset'];
	//echo $input['id_user']; die();

    $sql = "DELETE FROM penanggung_jawab WHERE id_aset = '$id_aset'";
    //echo $sql; die;
    if ($id_aset != "") {
        $results = DB::query($sql);
        if ($results) {
            responseHasil(200, true, "ID Penanggung Jawab: " . $id_aset . " delete successfully");
        } else {
            responseHasil(400, false, "ID Penanggung Jawab: " . $id_aset . " not delete");
        }
    } else {
        responseHasil(400, false, "ID Penanggung Jawab is empty");
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

//echo json_response(200, 'Aset');
?>