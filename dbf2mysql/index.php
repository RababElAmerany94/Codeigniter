<html>
<head></head>
<body>
    <div style="border: 1px solid black;">
        <center>
<?php
session_start();
set_time_limit(0); 

require 'src/XBase/Table.php';
require 'src/XBase/Column.php';
require 'src/XBase/Record.php';
require 'src/XBase/Memo.php';
use XBase\Table;

$mysqli = new mysqli("localhost", "root", "","silkroad_1");

if(isset($_FILES['dbfFile']['tmp_name'])) {
    $_SESSION['dbfPath'] = "dbf/".time()."_".$_FILES['dbfFile']['name'];
    move_uploaded_file($_FILES['dbfFile']['tmp_name'], $_SESSION['dbfPath']);
    $dbf = new Table($_SESSION['dbfPath']);
    $dbfColumns = $dbf->getColumns();
    
    $_SESSION['destinationTable'] = $_POST['destinationTable'];
    $result = $mysqli->query("SELECT * FROM ".$_POST['destinationTable']." LIMIT 1 ");
    $tableFields = $result->fetch_fields();

    echo '<form method="POST" action="index.php"><table>';
    echo "<tr><td>".$_FILES['dbfFile']['name']."</td><td>".$_POST['destinationTable']."</td></tr>";
    foreach($dbfColumns as $col) {
        echo "<tr><td>".$col->name."</td><td><select name=\"Columns[".$col->name."]\"><option>Ignore</option>";
        foreach($tableFields as $tableField) 
            echo "<option value=\"".$tableField->name."\">".$tableField->name."</option>";
        echo "</select></td></tr>";
        }
    echo "</table><br/><br/>";
    ?>
        <input type="submit" value="Start Import" style="width: 200px;"/>
    </form>
    <?php
} elseif(isset($_POST['Columns'])) {
$dbf = new Table($_SESSION['dbfPath']);
$table = $_SESSION['destinationTable'];
$columns = $_POST['Columns'];

foreach ($columns as $dbfColumn =>$tableColumn)
    if($tableColumn == "Ignore") 
        unset($columns[$dbfColumn]);


$mysqli->query("TRUNCATE ".$table);

$inserted = 0;
while($record = $dbf->nextRecord())
{
    $recordValues = $trace = [];
    foreach ($columns as $dbfColumn => $tableColumn) {
        
        $value = $record->forceGetString($dbfColumn);
        $value = mysqli_escape_string($mysqli,$value);

        if(!is_numeric($value) && !empty($value))
            $recordValues[$tableColumn] = "'".$value."'";
        elseif(is_numeric($value) && ($value > 0 || $value < 0))
            $recordValues[$tableColumn] = "'".$value."'";
        elseif((!isset($recordValues[$tableColumn]) && empty($value)) || empty($recordValues[$tableColumn]))
            $recordValues[$tableColumn] = "NULL";

        $trace[] = $dbfColumn.' # '.$tableColumn.' # '.$record->forceGetString($dbfColumn).' # '.$value;
    }

    $query = "INSERT INTO ".$table." (".implode(',',array_unique(array_values($columns))).") VALUES(".implode(",",$recordValues).")";

    if($mysqli->query($query)) 
        $inserted++;
    else 
        die("<h1>".$mysqli->error."</h1>".$query.print_r($trace));
}

echo $dbf->recordCount." records in file => ".$inserted." imported to MySQL<br/><br/>";
echo ($dbf->recordCount==$inserted)?'Success!<br/><a href="index.php">Start Over</a>':"Error.";
    
} else {
$result = $mysqli->query("SHOW TABLES");
$tables = $result->fetch_all();

?>
    <form action="index.php" method="POST" enctype="multipart/form-data">
        <br/>
        dBase File:
        <input type="file" name="dbfFile" id="dbfFile">
        <br/><br/>
        Destination Table:
        <select name="destinationTable"><?php foreach($tables as $table) echo "<option>".$table[0]."</option>";?></select>
        <br/><br/>
        <input type="submit" value="Upload" name="submit" style="width: 200px;">
    </form>
<?php 
}
?>
</center>
</div>
</body>
</html>