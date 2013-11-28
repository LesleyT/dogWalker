<?php

  function connect(){//$host, $username, $password, $database, $charset = "utf8"){
      $connect = new mysqli("lesleytaihitu.nl.mysql", "lesleytaihitu_n", "tilburg12", "lesleytaihitu_n");
      if(!$connect){
        return "connection could not be astablished" . mysqli_connect_error();
      }
      else{
        $connect->set_charset("utf8");
      }

      return $connect;
  }

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

  function select($fields, $tables, $where = null, $andOr = null, $joins = null){
  	//print_r($fields);
    $select = "SELECT ";
    $from = " FROM ";
    $whereStatement = "";
    $joinOn = "";
    foreach($fields as $tableName => $field){
    	//print_R($field == "*");
    	if($field == "*"){
	    	$select .= "*";
    	}
    	else{
	    	$select .= "`$tableName`.`$field`,";
    	}		
    }
    if(!isset($fields[0])){
	    $select = substr($select, 0, -1);
    }

    foreach($tables as $table){
    	$from .= "`$table`, ";
    }
    $from = substr($from, 0, -2)." ";
    
	if($where !== null){
		$whereStatement = " WHERE ";
		foreach($where as $name => $value){
			$whereStatement .= "`$table`.`$name` = '$value' AND";
		}
		$whereStatement = substr($whereStatement, 0, -3);
		if($andOr !== null){
			preg_replace('/and/', $andOr, $whereStatement);
		}
	}
	
	if($joins !== null){
		foreach($joins as $join){
			$key1 = key($join[0]);
			$key2 = key($join[1]);
			$joinOn = "`".$key1."`.`".$join[0][$key1]."` = `".$key2."`.`".$join[1][$key2]."` AND";
		}
		$joinOn = ' AND ('.substr($joinOn, 0, -3).')';
	}

    $query = $select.$from.$whereStatement.$joinOn.';';
    print_r(array($query, connect()->query($query)));
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