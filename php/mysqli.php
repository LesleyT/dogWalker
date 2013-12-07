<?php
  //connects to the database and returns the resource for later use
  function connect(){
      $connect = new mysqli("**", "**", "**", "**");
      if(!$connect){
        return "connection could not be astablished" . mysqli_connect_error();
      }
      else{
        $connect->set_charset("utf8");
      }

      return $connect;
  }
  	//werkt nog niet XD
    function sanitize($accountInfo){
        $sanitizedInfo = array();
        foreach($accountInfo as $infoField => $infoValue){
            $infoValue = trim($infoValue);
            $infoValue = strip_tags($infoValue);
            $infoValue = htmlentities($infoValue);
            $infoValue = connect()->real_escape_string($infoValue);
            $sanitizedInfo[$infoField] = $infoValue;
        }
    return $sanitizedInfo;
    }

  function select($fields, $where = null, $andOr = null, $joins = null){
    $select = "SELECT ";
    $from = " FROM ";
    $whereStatement = "";
    $joinOn = "";
    
    //loops through an array of key=>value pairs, where the key equals the talbe name
    //and the value the field you want to select. This is needed to join multiple tables
    //
    //Uses the table names for the FROM statement
    //
    //change to [tablename]=>array(field1, field2)
    foreach($fields as $tableName => $field){
		foreach($field as $fieldName){
			if($fieldName == "*"){
	    		$select .= "*";
			}
			else{
	    		$select .= "`$tableName`.`$fieldName`,";
				}		
			}
			$from .= "`$tableName`, ";
    }
    //cleans up the SELECT part of the query
    if($fieldName == "*"){
	    $select = "SELECT *";
    }
    else{
	    $select = substr($select, 0, -1);
    }
	 //cleans up the FROM part of the query
    $from = substr($from, 0, -2)." ";
    
	
    /*foreach($tables as $table){
    	$from .= "`$table`, ";
    }*/
   
    
    //multi-dimensional array, first layers key equals the table name and the second layer equals a pair of fields and 
    //values
    //
    // [talbename]=>array(array(field, value))
	if($where !== null){
		$whereStatement = " WHERE ";
		foreach($where as $wTableName => $wStatement){
			foreach($wStatement as $name => $value){
				$whereStatement .= "`$wTableName`.`$name` = '$value' AND";
			}
		}
		$whereStatement = substr($whereStatement, 0, -3);
		if($andOr !== null){
			preg_replace('/and/', $andOr, $whereStatement);
		}
	}
	
	//multi-dimensional array, first layers contains arrays the second layer equals a pair of fields and 
    //values to join on
	if($joins !== null){
		foreach($joins as $join){
			$key1 = key($join[0]);
			$key2 = key($join[1]);
			$joinOn = "`".$key1."`.`".$join[0][$key1]."` = `".$key2."`.`".$join[1][$key2]."` AND";
		}
		$joinOn = ' AND ('.substr($joinOn, 0, -3).')';
	}

    $query = $select.$from.$whereStatement.$joinOn.';';
    //print_r($query);
    return connect()->query($query);
    //close();
  }

  function insert($fields, $table){
    $query = "INSERT INTO `$table`";
    $fieldNames = Array();
    $fieldValues = Array();

    foreach($fields as $name => $value){
      $fieldNames[] = $name;
      $fieldValues[] = $value;
    }
    $fieldNames = '(`'.implode('`, `', $fieldNames).'`)';
    $fieldValues = "('".implode("', '", $fieldValues)."')";
  
    $query .= $fieldNames .'VALUES '. $fieldValues.';';

    connect()->query($query);
    //close();
  }

  function close(){
      connect()->close();
  }

?>
