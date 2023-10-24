<?php
	$urlParams = explode('/', $_SERVER['REQUEST_URI']);
	$functionName = $urlParams[2];
  include 'server_connection.php';
  $QRNo = $urlParams[3];
  
  $result = array();
  $res_arr = array();
  $data = "";
     $params = array(
          array($QRNo, SQLSRV_PARAM_IN)
          );
          $sql = "{call [dbo].[mobile_Checkin_this_QR](?)}";
          $stmt = sqlsrv_prepare($conn, $sql, $params);
         // $stmt = sqlsrv_prepare($conn, $sql);
          if( !$stmt ){
            echo "Slow Internet connection....Try to refresh the page.";
            return null;
          }else{
            $result = sqlsrv_execute($stmt);
            if( !$result ) {
              return null;
              $data = '[{"Result":"No data gathered"}]';
              //$json = json_encode($dat);
            }else{
              $data = '[';
              while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                // $agentID_arr = $row['AgentGroupID'];
                $res_arr[] = $row;
              }
              $data = substr($data, 0, -1);
              $data = $data."]";
              header('Content-type: application/json');
			  $json = json_encode($res_arr);
				if ($json === false) {
				    // Avoid echo of empty string (which is invalid JSON), and
				    // JSONify the error message instead:
				    $json = json_encode(["jsonError" => json_last_error_msg()]);
				    if ($json === false) {
				        // This should not happen, but we go all the way now:
				        $json = '{"jsonError":"unknown"}';
				    }
				    // Set HTTP response status code to: 500 - Internal Server Error
				    http_response_code(500);
				}
				echo $json;
            }
          }
?>