<?php
  $urlParams = explode('/', $_SERVER['REQUEST_URI']);
  $functionName = $urlParams[2];
  $agentID = $urlParams[3];
  include 'server_connection.php';
  $dateFrom = $urlParams[4];
  $dateTo = $urlParams[5];
  $result = array();
  $res_arr = array();
  $data = "";
     $params = array(
          array($dateFrom, SQLSRV_PARAM_IN),
          array($dateTo, SQLSRV_PARAM_IN),
          array($agentID, SQLSRV_PARAM_IN)
          );
          $sql = "{call [dbo].[iwatsdashboard_GetTotalSalesPass_PerDateRange](?,?,?)}";
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
              $json = json_encode($dat);
				echo $json;
            }else{
              while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $res_arr[] = $row;
              }
				// Add a new row at the end of the result
				/*
			  $newRow = array(
				"VesselName" => "TOTAL",
				"SalesMonth" => "",
				"SalesYear" => "",
				"GrossAmount" => "$sumGrossAmount",
				"NetAmount" => "$sumNetAmount",
				"TaxableSales" => "$sumTaxableSales",
				"VatExemptSales" => "$sumVatExemptSales",
				"TotalVat" => "$sumTotalVat",
				"TotalCom" => "$sumAgentComAmount",
				"CustomerClaims" => "$sumAmountClaimed",
				"Sales" => "$sumSales",
				"TotalPassengers" => "$sumPassenger"
			  );
				*/
			  //array_push($res_arr, $newRow);
				
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