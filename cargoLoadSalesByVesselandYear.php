<?php
  $urlParams = explode('/', $_SERVER['REQUEST_URI']);
  $functionName = $urlParams[2];
  include 'server_connection.php';
  $vesselID = $urlParams[3];
  $year = $urlParams[4];
  $result = array();
  $res_arr = array();
  $data = "";
     $params = array(
          array($vesselID, SQLSRV_PARAM_IN),
          array($year, SQLSRV_PARAM_IN)
          );
          $sql = "{call [dbo].[dashboard_LoadCargoVesselSales](?,?)}";
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
			  $sumSales = 0;
			  $sumAmountClaimed = 0;
			  $sumAgentComAmount = 0;
			  $sumNetAmount = 0;
			  $sumGrossAmount = 0;
			  $sumCargo = 0;
			  $sumCancelledRefundedCargo = 0;
              while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                // $agentID_arr = $row['AgentGroupID'];
				//  $row['Sales'] = (float) $row['Sales'];
				 $sumGrossAmount += (float)$row['GrossAmount'];
				 $sumNetAmount += (float)$row['NetAmount'];
				 $sumTaxableSales += (float)$row['TaxableSales'];
				 $sumVat += (float)$row['TotalVat'];
				 $sumAgentComAmount += (float)$row['TotalCom'];
				 $sumAmountClaimed += (float)$row['CustomerClaims'];
				 $sumSales += (float)$row['Sales'];
				 $sumCargo += $row['TotalCargo'];
				 $sumCancelledRefundedCargo += $row['TotalCancelledRefundedCargo'];
                $res_arr[] = $row;
              }
				// Add a new row at the end of the result
			  $newRow = array(
				"VesselName" => "TOTAL",
				"SalesMonth" => "",
				"SalesYear" => "",
				"GrossAmount" => "$sumGrossAmount",
				"NetAmount" => "$sumNetAmount",
				"TaxableSales" => "$sumTaxableSales",
				"TotalVat" => "$sumVat",
				"TotalCom" => "$sumAgentComAmount",
				"CustomerClaims" => "$sumAmountClaimed",
				"Sales" => "$sumSales",
				"TotalCargo" => "$sumCargo",
				"TotalCancelledRefundedCargo" => "$sumCancelledRefundedCargo"
			  );
			  array_push($res_arr, $newRow);
				
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