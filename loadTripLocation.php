<?php
  $urlParams = explode('/', $_SERVER['REQUEST_URI']);
  $functionName = $urlParams[2];
  include 'server_connection.php';
  
  $result = array();
  $res_arr = array();
  $data = "";
          $sql = "{call [dbo].[dashboard_LoadTripLocation]}";
          $stmt = sqlsrv_prepare($conn, $sql);
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
				$newRow = array(
					"ID" => "0",
					"TripLocation" => "ALL",
					"IsActive" => 1,
					"TripTime" => "1:30",
					"Save_Date" => array(
					  "date" => "2023-01-16 00:00:00.000000",
					  "timezone_type" => 3,
					  "timezone" => "America/Chicago"
					),
					"Save_User" => "admin1",
					"Trip_hrs" => null,
					"Trip_mins" => null
				  );

				  array_unshift($res_arr, $newRow);
				
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