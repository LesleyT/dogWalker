<?php
error_reporting(E_ALL);
require('mysqli.php');
if(isset($_POST['action'])){
if($_POST['action'] == 'insertUser'){
    insertUser();
}
elseif($_POST['action'] == 'insertWalk'){
    insertWalk();
}
elseif($_POST['action'] == 'getLastWalk'){
    getLastWalk();
}
elseif($_POST['action'] == 'getGroupInfo'){
    getGroupInfo();
}
}

function insertUser(){
    //$data = sanitize($_POST);
    $select = select(array('user_id'), 'gw_users', array('user_id' => $_POST['user_id']));
    if($select->num_rows > 0){
        $insert = array('id' => $_POST['user_id'], 'firstname' => $_POST['firstname'], 'lastname' => $_POST['lastname'], 'gender' => $_POST['gender']);
        insert($insert, 'dw_users');
    }
}

function insertWalk(){
    //$data = sanitize($_POST);
    $insert = array(
                'id'=> null,
                'group_id'=>$_POST['group_id'],
                'user_id'=>$_POST['user_id'],
                'dog_id'=>$_POST['dog_id'],
                'location'=>$_POST['location'],
                'datetime' => $_POST['datetime']
              );
    insert($insert ,'dw_walk');
}

function getLastWalk(){
    //$data = sanitize($_POST);
    $select = select(
    array(
    	'dw_walk' => array('datetime'),
		'dw_dogs' => array('name'),
		'dw_users' => array('firstname')
    ),
     array(
     	'dw_walk' => array('id'=>$_POST['dog_id'])
     ),
     NULL,
     array(
     	array(
     		array(
     			'dw_walk' => 'user_id'
     		),
     		 	array('dw_users' => 'id')
     	),
     	array(
     		array('dw_walk' => 'dog_id'),
     		 array('dw_dogs' => 'id')
     	)
 	)
 	);

    //print_r(array($select));
    if(!is_null($select) && $select->num_rows > 0){
    	if($select->num_rows > 0){
        $result = $select->fetch_assoc();
        echo json_encode($result);
        }
    }
}

function getGroupInfo(){
    //$data = sanitize($_POST);
    $select = select(array("dw_walk" => array('*'), "dw_group" => array("*")), array('dw_walk' => array('dog_id'=>$_POST['dog_id'])), null, array(array(array("dw_walk" => "group_id"),array("dw_group" => "id"))));
    //print_r(array($select));
    if(!is_null($select)){
    	if($select->num_rows > 0){
        $result = $select->fetch_assoc();
        echo json_encode($result);
        }
    }
}



?>