<?php
function getHrs($aar_maaned, $until=false){
  $append = ($until) ? "AND [Done] < '{$until}-01 0:00:00.000'" : '';
  $query = mssql_query("SELECT [TimeUsed]
    FROM [RIS].[dbo].[ServiceCaseTime] WHERE  '{$aar_maaned}-01 0:00:00.000' < [Done] {$append}
    ORDER BY [Done] DESC");
  $timer = 0;
  do {
    while ($row = mssql_fetch_row($query)) {
    $timer = $timer + $row[0];
    }
  } while (mssql_next_result($query));
  mssql_free_result($query);
  return $timer;
}
include("connect.db.php");
$calcedHrs = getHrs(date('Y-m'));
print sprintf($calcedHrs);
?>