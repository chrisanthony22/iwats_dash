<?php
  $urlParams = explode('/', $_SERVER['REQUEST_URI']);
  $functionName = $urlParams[2];
  include 'server_connection.php';
  $tripID = $urlParams[3];
  $vesselID = $urlParams[4];
  $agentID = $urlParams[5];
  $date_from = $urlParams[6];
  $date_to = $urlParams[7];
  $result = array();
  $res_arr = array();
  $data = "";
     $params = array(
          array($tripID, SQLSRV_PARAM_IN),
          array($vesselID, SQLSRV_PARAM_IN),
          array($agentID, SQLSRV_PARAM_IN),
          array($date_from, SQLSRV_PARAM_IN),
          array($date_to, SQLSRV_PARAM_IN)
          );
          $sql = "{call [dbo].[dashboard_LoadSales](?,?,?,?,?)}";
          $stmt = sqlsrv_prepare($conn, $sql, $params);
         // $stmt = sqlsrv_prepare($conn, $sql);
          if( !$stmt ){
            echo "Slow Internet connection....Try to refresh the page.";
            return null;
          }else{
            $result = sqlsrv_execute($stmt);
            if( !$result ) {
              return null;
              //$json = json_encode($dat);
            }else{
			  $sumSales = 0;
			  $sumAmountClaimed = 0;
			  $sumAgentComAmount = 0;
			  $sumNetAmount = 0;
			  $sumGrossAmount = 0;
			  $sumPassenger = 0;
			  $sumCancelledPassenger = 0;
              while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                // $agentID_arr = $row['AgentGroupID'];
				//  $row['Sales'] = (float) $row['Sales'];
				 $sumGrossAmount += (float)$row['GrossAmount'];
				 $sumNetAmount += (float)$row['NetAmount'];
				 $sumTaxableSales += (float)$row['TaxableSales'];
				 $sumVatExemptSales += (float)$row['VatExemptSales'];
				 $sumVat += (float)$row['TotalVat'];
				 $sumAgentComAmount += (float)$row['TotalCom'];
				 $sumAmountClaimed += (float)$row['CustomerClaims'];
				 $sumSales += (float)$row['Sales'];
				 $sumPassenger += $row['TotalPassenger'];
				 $sumCancelledPassenger += $row['TotalCancelledRefundedPassenger'];
                $res_arr[] = $row;
              }
				// Add a new row at the end of the result
			  $newRow = array(
				"DateRange" => "TOTAL",
				"TripLocation" => "",
				"VesselName" => "",
				"AgentGroupName" => "",
				"GrossAmount" => "$sumGrossAmount",
				"NetAmount" => "$sumNetAmount",
				"TaxableSales" => "$sumTaxableSales",
				"VatExemptSales" => "$sumVatExemptSales",
				"TotalVat" => "$sumVat",
				"TotalCom" => "$sumAgentComAmount",
				"ComPercentage" => "",
				"CustomerClaims" => "$sumAmountClaimed",
				"Sales" => "$sumSales",
				"TotalPassenger" => "$sumPassenger",
				"TotalCancelledRefundedPassenger" => "$sumCancelledPassenger",
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