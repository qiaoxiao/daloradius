<?php

    include ("library/checklogin.php");
    $operator = $_SESSION['operator_user'];
        
    $hotspot = !empty($_REQUEST['ps-hotspot']) ? $_REQUEST['ps-hotspot'] : '';
	$startdate = $_REQUEST['ps-startdate'];
	$enddate = $_REQUEST['ps-enddate'];


	include_once('library/config_read.php');
    $log = "visited page: ";
    $logQuery = "performed query for hotspot [$ps-hotspot] with start date [$ps-startdate] and end date [$ps-enddate] on page: ";
    include('include/config/logging.php');

?>

<?php

    include ("menu-billing.php");

?>		
		
		<div id="contentnorightbar">
		
		<h2 id="Intro"><a href="#"><?echo $l[Intro][billpersecond.php]; ?></a></h2>
				
				<p>
				
						<?php echo $l[captions][acctrecsforhotspot]." <i>".$hotspot."</i>" ?>
				
				</p>

					
<?php

                include 'library/opendb.php';

        echo "<table border='2' class='table1'>\n";
        echo "
                        <thead>
                                <tr>
                                <th colspan='7'>".$l[all][Records]."</th>
                                </tr>
                        </thead>
                ";

        echo "<thread> <tr>
                        <th scope='col'> ".$l[all][Username]." </th>
                        <th scope='col'> ".$l[all][HotSpot]." </th>
                        <th scope='col'> ".$l[all][LastLoginTime]." </TH>
                        <th scope='col'> ".$l[all][TotalSessionTime]." </th>
                        <th scope='col'> ".$l[all][Rate]." </th>
                        <th scope='col'> ".$l[all][Billed]." </th>
                </tr> </thread>";

	// First we search for all users that have been connected AT LEAST ONCE by checking if they appear in the radacct table,
	// then we get their max-all-session attribute to see to how long their time is limited (they're card bank, represented in secs)
	// BUT this will only list rates that have a max-all-session defined for them.

	$sql = "select distinct(".$configValues['CONFIG_DB_TBL_RADACCT'].".UserName), ".$configValues['CONFIG_DB_TBL_DALOHOTSPOTS'].".name, ".$configValues['CONFIG_DB_TBL_RADACCT'].".AcctStartTime, Sum(".$configValues['CONFIG_DB_TBL_RADACCT'].".AcctSessionTime), ".$configValues['CONFIG_DB_TBL_DALORATES'].".rate from ".$configValues['CONFIG_DB_TBL_RADACCT'].", ".$configValues['CONFIG_DB_TBL_DALORATES'].", ".$configValues['CONFIG_DB_TBL_DALOHOTSPOTS'].", ".$configValues['CONFIG_DB_TBL_RADCHECK']." where (".$configValues['CONFIG_DB_TBL_RADACCT'].".Username = ".$configValues['CONFIG_DB_TBL_RADCHECK'].".UserName) and (".$configValues['CONFIG_DB_TBL_RADCHECK'].".Attribute LIKE '%Password') and (".$configValues['CONFIG_DB_TBL_RADACCT'].".AcctStartTime >= '$startdate') and (".$configValues['CONFIG_DB_TBL_RADACCT'].".AcctStartTime <= '$enddate' ) and (".$configValues['CONFIG_DB_TBL_DALORATES'].".type = 'per second') and (".$configValues['CONFIG_DB_TBL_RADACCT'].".calledstationid = ".$configValues['CONFIG_DB_TBL_DALOHOTSPOTS'].".mac) and (".$configValues['CONFIG_DB_TBL_DALOHOTSPOTS'].".name like '$hotspot') group by ".$configValues['CONFIG_DB_TBL_RADACCT'].".UserName";
	$res = $dbSocket->query($sql);

	$sum = 0;
	$count = 0;
	$hs = "";	// hotspot name

	while($row = $res->fetchRow()) {
                echo "<tr>
                        <td> $row[0] </td>
                        <td> $row[1] </td>
                        <td> $row[2] </td>
                        <td> $row[3] </td>
                        <td> $row[4] </td>
                        <td>";

			$billed  = $row[3] * $row[4];
			echo $billed;

		 echo" </td>
                </tr>";

		$sum = $sum + $billed;
		$count = $count + 1;
		$hs = $row[1];
		
        }
        echo "</table>";

        include 'library/closedb.php';


	echo "<br/><br/>";
        echo "<table border='2' class='table1'>\n";
        echo "
                        <thead>
                                <tr>
                                <th colspan='7'>".$l[all][Summary]."</th>
                                </tr>
                        </thead>
                ";

        echo "<thread> <tr>
                        <th scope='col'> ".$l[all][HotSpot]." </th>
                        <th scope='col'> ".$l[all][TotalUsers]." </th>
                        <th scope='col'> ".$l[all][TotalBilled]." </th>
                </tr> </thread>";

                echo "<tr>
                        <td> $hs </td>
                        <td> $count </td>
                        <td> $sum </td>
			</tr>
			</table>";	






?>









	

		</div>
		
		<div id="footer">
		
								<?php
        include 'page-footer.php';
?>

		
		</div>
		
</div>
</div>


</body>
</html>
