<?php
/* Begin Config */

// IDEALLY THESE SHOULD RESIDE IN SEPARATE FILE OUTSIDE OF WEB ROOT WHICH IS REQUIRE()'ED ////
// OTHERWISE CREDENTIALS ARE TECHNICALLY AT RISK IF YOUR WEB SERVER GETS EXPLOITED/////////
///////////////////////////////////////////////////////////////////////////////////////
const Server = "localhost";////////////////////////////////////////////////////////
const Username = "DB_USERNAME";/////////////////////////////////////////////////
const Password = "DB_PASSWORD";////////////////////////////////////////////
const DBName = "characters";///////////////////////////////////////////
///////////////////////////////////////////////////////////////////

// CHANGE THIS TO YOUR CUSTOM BOOST ITEM
const CUSTOM_BOOST_ITEMID = 90001;

/* End Config */

/* Connection to Database */
function GetDBConnection()
{
    $mysqli = new mysqli(Server, Username, Password, DBName);
    if ($mysqli->connect_error) {
        exit('Could not connect to database.'); 
    }
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $mysqli->set_charset("utf8mb4");
    return $mysqli;
}
/* End Connection to Database */
// created 11/29/18 by Brady Russell for https://github.com/talamortis/mod_reward_shop
function CreateNewCode($connection, $action,$action_data, $quantity,$code,$created_by){
        $stmt = $connection->prepare("INSERT INTO reward_shop (action,action_data,quantity,code,CreatedBy) VALUES (?, ?, ?, ?, ?)");

        $stmt->bind_param("sssss", $action,$action_data,$quantity,$code,$created_by);
        $stmt->execute();
        $rows = $stmt->affected_rows;
        $stmt->close();

        if($rows === 1){
            return true;
        } else {
            return false;
        }
}
?>

<html>
<head>
    <title>Wow Code Generator</title>
</head>
<body>

<?php
if(isset($_POST['action']) && isset($_POST['code']) && isset($_POST['creator']) ){
    $action = $_POST['action'];
    $code = $_POST['code'];
    $creator = $_POST['creator'];
    
    if(isset($_POST['action_data']) && !empty($_POST['action_data'])){
        $action_data = $_POST['action_data'];
    } else {
        $action_data = 0;
    }
    
    if(isset($_POST['quantity']) && !empty($_POST['quantity'])){
        $quantity = $_POST['quantity'];
    } else {
        $quantity = 1;
    }
    
    if($action === "boost"){
        $action = 1;
        $action_data = CUSTOM_BOOST_ITEMID;
    }
    
    if(CreateNewCode(GetDBConnection(),$action,$action_data,$quantity,$code,$creator)){
        echo("<p style=\"color:green\">Successfully created code: [ ".$code." ]</p>");
    } else {
        echo("<p style=\"color:red\">Failed to create code! It may already exist.</p>");
    }
}
?>

<form action="" method="post">

<label>Code Type *:
    <select name="action">
        <option value="1">Item</option>
        <option value="2">Gold</option>
        <option value="boost">Boost</option>
        <option value="3">Name Change</option>
        <option value="4">Faction Change</option>
        <option value="5">Race Change</option>
    </select>
</label>
<br/><br/>

<label>Code Data:
    <input type="text" name="action_data" placeholder="ItemID / Gold Amount"/>
</label>
<br/><br/>

<label>Item Quantity:
    <input type="text" name="quantity" placeholder="For items only"/>
</label>
<br/><br/>

<label>Code *:
    <input type="text" name="code" placeholder="Key to use In Game"/>
</label>
<br/><br/>

<label>Code Creator *:
    <input type="text" name="creator" placeholder="Your username"/>
</label>
<br/><br/>

<input type="submit" value="Generate Code"/>

</form>

</body>
</html>
