<?php 

error_reporting(E_ALL | E_STRICT);
	ini_set('memory_limit','512M');
	ini_set('display_errors', 1);
	ini_set('max_execution_time', 900); //300 seconds = 5 minutes
	session_start();

	require_once '../app/Mage.php';
	Mage::app();
	
	
class File_Convert_Sql{
	
function getFile(){
			/*Read files to insert in Table */
	include 		'read_xls/Excel/reader.php';
	
	$filename 	=	'Reefman_10_subikar.xls';
	$tableName	=	'reefman_products';
	$fileData	=	$this->readFile($filename);

	$xls_col				=	$fileData['cells'][1];  //[1] & [2]
	$xlsTotalNumCols		=	$fileData['numCols'];
	$xlsColName				=	$fileData['cells'][1];
	$Val					= 	$fileData['cells'];
	$xlsTotalNumRows		= 	count($fileData['cells']);
	///put into the prodtcs array.
	$ProductConvertedInsert	= $this->createdProductArray($xlsTotalNumRows,$xlsTotalNumCols,$xlsColName,$Val,$tableName);
}


///////////Reading values from xls for array 
function createdProductArray($totalrows,$totalCols,$colname,$colval,$tableName){
	
	$ProductFields=array();
	//$totalrows=7;
	$colIndex=0;
	// echo "XlsVAlCol ++++"; print_r($colval[3][1]); 
	$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
			$table = new Varien_Db_Ddl_Table();
			$table->setName($tableName);
			$table->addColumn('insert_flag',Varien_Db_Ddl_Table::TYPE_INTEGER,null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ));
$concount=0;
	for($valRowIndex =3; $valRowIndex <= $totalrows; $valRowIndex++) {
		
		///open table connectionss....
		if($concount==50){
			$connection->getConnection();
		}
		
		for($valIndex = 1; $valIndex <= $totalCols; $valIndex++) {
	
				$TableColoumn	= "".str_replace(":","_",str_replace(" ","_",$colname[$valIndex]))."";
				$TableColoumn= str_replace(".","_",$TableColoumn);
				
				$table->addColumn($TableColoumn, Varien_Db_Ddl_Table::TYPE_TEXT);
				$TableColoumn= "`".str_replace(".","_",$TableColoumn)."`";
				//echo '<br>'.$TableColoumn.'=>'."'".addslashes($colval[$valRowIndex][$valIndex])."', ";
				//echo "<br>INSERT INTO `main_products` (".$TableColoumn.") VALUES (".addslashes($colval[$valRowIndex][$valIndex]).")";
				$key.=$TableColoumn.',';
				//if($valIndex == $totalCols){
				$val.="'".addslashes($colval[$valRowIndex][$valIndex])."', ";
			}
				 if ($connection->isTableExists($tableName) != true) { $connection->createTable($table);echo "Table Created successfully";}
			/////trimmed last comma				
			$key=rtrim($key,',');
			$val=rtrim($val,', ');
		 	$sql = "INSERT INTO `$tableName` (".$key.") VALUES (".$val.")";
			$connection->query($sql); 
			$concount++;
			if($concount==50){
			$connection->closeConnection();
			}
			$key='';
			$val ='';			
			echo "<br> <br> Record inserted successfully<br><br>"; 
			//$colIndex++;
		
		$colIndex++;
		///send products data for  the insertion ........
	}
		//echo "Array Total Size Products ".count($ProductFields);
		//echo"<pre>"; print_r(count($xlsFeilds)); echo "</pre>";
		//exit;
	return  $ProductFields;	
	}//////function xlsValcol end
		
	function readFile($fileName){
	
	$excel = new Spreadsheet_Excel_Reader();
	$excel->read($fileName);
	   
	return  $excel->sheets[0];
	  } ////function readFile
	
	
}
/************************/// calling class from here  ***************************************************************/

$my_controller = new File_Convert_Sql;
$my_controller->getFile();

/***************************************************************************************/

?>