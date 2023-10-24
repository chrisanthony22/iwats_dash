<?php
  $urlParams = explode('/', $_SERVER['REQUEST_URI']);
  $functionName = $urlParams[2];
  include 'server_connection.php';
  
  $result = array();
  $res_arr = array();
  $data = "";
  $sql = "{call [dbo].[dashboard_LoadYearList]}";
  $stmt = sqlsrv_prepare($conn, $sql);

  if (!$stmt) {
    echo "Slow Internet connection....Try to refresh the page.";
    return null;
  } else {
    $result = sqlsrv_execute($stmt);

    if (!$result) {
      return null;
      $data = '[{"Result":"No data gathered"}]';
    } else {
      while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $res_arr[] = $row;
      }
		

      header('Content-type: application/json');
      $json = json_encode($res_arr);

      if ($json === false) {
        $json = json_encode(["jsonError" => json_last_error_msg()]);
        if ($json === false) {
          $json = '{"jsonError":"unknown"}';
        }
        http_response_code(500);
      }

      echo $json;
    }
  }
?>
