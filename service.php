<?php
$overskredet = 0;
include ("connect.db.php");
$query = mssql_query('
SELECT [Received]
      ,[TimeoutNotificationSent]
  FROM [RIS].[dbo].[MailCase] 
  where
  [HandledByEmployeeId] IS NULL and
  [MailCaseMailboxId] = 10011
ORDER BY [Received] DESC
');
do {
	while ($row = mssql_fetch_row($query)) {
	    if (count($row) > 4) { $overskredet=1;}
	    if ($row[1]==1) {$overskredet = 2;} 
	}
} while (mssql_next_result($query));
mssql_free_result($query);
if ($overskredet == 1) {
        print 1;
} else if ($overskredet == 2) {
        print 2;
}
?>