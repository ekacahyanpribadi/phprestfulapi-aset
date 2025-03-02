<?php
DB::$dsn = 'mysql:host=localhost;dbname=easet';
DB::$user = 'c5sWttLvLZ';
DB::$password = 'hF1zJCDbvsVkodiMMzaq';
function json_response($code = 200, $message = null, $data = null)
{
    // clear the old headers
    header_remove();
    // set the actual code
    http_response_code($code);
    // set the header to make sure cache is forced
    header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
    // treat this as json
    header('Content-Type: application/json');
    $status = array(
        200 => '200 OK',
        400 => '400 Bad Request',
        422 => 'Unprocessable Entity',
        500 => '500 Internal Server Error'
        );
    // ok, validation error, or failure
    header('Status: '.$status[$code]);
    // return the encoded json
    return json_encode(array(
        'status' => $code < 300, // success or not?
        'message' => $message,
        'data' => $data
        ));
}

/*
// if you are doing ajax with application-json headers
if (empty($_POST)) {
    $_POST = json_decode(file_get_contents("php://input"), true) ? : [];
}

// usage
echo json_response(200, 'working'); // {"status":true,"message":"working"}

// array usage
echo json_response(200, array(
  'data' => array(1,2,3)
  ));
// {"status":true,"message":{"data":[1,2,3]}}

// usage with error
echo json_response(500, 'Server Error! Please Try Again!'); // {"status":false,"message":"Server Error! Please Try Again!"}
*/

function acakHuruf($bdigit){
    $var0 = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ'); // and any other characters
    shuffle($var0); // probably optional since array_is randomized; this may be redundant
    $var1 = '';
    foreach (array_rand($var0, $bdigit) as $var3) $var1 .= $var0[$var3];
    return $var1;
}

function getPk(){
    $var1 =date('YmdHis');
	$var2 = acakHuruf(6);
	$var=$var1.$var2;

    return $var;
}
//echo getPk();
?>