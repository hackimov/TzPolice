<?function sql($SQL_String)
{
//  sql_log($SQL_String);
    if(!($Query = mysql_query($SQL_String)))
    {
        echo mysql_error()."<BR>";
        echo nl2br($SQL_String);
        //sql_log($SQL_String,mysql_error());
    }
    return $Query;
}

function sql_a($Query)
{
    $req = mysql_fetch_array($Query);
    if(mysql_errno())
    {
        //sql_log($Query,mysql_error());
    }
    return $req;
}

function sql_count($Query){ return mysql_num_rows($Query);}

?>