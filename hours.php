<?php
$overskredet = 0;
//ALT HERUNDER BRUGES TIL TIMERNE

//Returnerer antal timer i angivet måned og år
function getHrs($aar_maaned, $until=false){
  $append = ($until) ? "AND [Done] < '{$until}-01 0:00:00.000'" : '' ;
  $query = mssql_query("SELECT [TimeUsed]
    FROM [RIS].[dbo].[ServiceCaseTime] WHERE  '{$aar_maaned}-01 0:00:00.000' < [Done] {$append}

    ORDER BY [Done] DESC
  ");
//[InvoiceType] != 3 AND
  $timer = 0;
  do {
    while ($row = mssql_fetch_row($query)) {
    // var_dump($row);
    //print $row;
    $timer = $timer + $row[0];
    }
  } while (mssql_next_result($query));
  mssql_free_result($query);

  return $timer;
}



function getDays(){
  $stamp = date("Y-m-d", mktime(0, 0, 0, date('m'), date('j')-29, date('Y')));

  $query = mssql_query("SELECT [TimeUsed], [Done]
    FROM [RIS].[dbo].[ServiceCaseTime] WHERE [InvoiceType] != 3 AND '{$stamp} 0:00:00.000' < [Done]

    ORDER BY [Done] DESC
  ");

  $array_of_days = array_fill(0, 30, 0);

  do {
    while ($row = mssql_fetch_row($query)) {
      $then = new DateTime(date('Y-m-d', mktime(0, 0, 0, date('m'), date('j')-29, date('Y'))));
      $now  = new DateTime($row[1]);

      $i = $then->diff($now)->format("%a");

      $array_of_days[$i] += $row[0];
    }
  } while (mssql_next_result($query));
  mssql_free_result($query);

  return $array_of_days;
}
//Returnerer et timestamp asdjg til inputtede måned og år
function mt($month, $year, $abs_m=false, $abs_y=false){
  $month += ($abs_m) ? 0 : date('m');
  $year += ($abs_y) ? 0 : date('Y');
  return mktime(0, 0, 0, $month, 1, $year);
}

include("connect.db.php");

$calcedHrs = getHrs(date('Y-m'));

print sprintf($calcedHrs);
?>

