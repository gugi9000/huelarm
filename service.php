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

$j=0;

do {
        while ($row = mssql_fetch_row($query)) {
            if ($row[1]==0) { $overskredet=1;}
            if ($row[1]==1) {$overskredet = 2;}
            $j++;
        }
} while (mssql_next_result($query));


mssql_free_result($query);

if ($overskredet == 1) {
        print 1;
} else if ($overskredet == 2) {
        print 2;
} else  if ($overskredet == 0){
       print 0;
}

?>