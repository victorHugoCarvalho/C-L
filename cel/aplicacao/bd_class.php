<?php
/*
##   Simple PostgreSQL Abstraction Layer v1.0
##   by Cem ÇEVÝK <cemc@linux.org.tr>
##   Sturday, January 06, 2001
##
##   Easy way to access database and store fetched results.
##   Database connection and query operation implemented in a separate
##   class to play with multiple query results simultaneously over
##   the same connection.
##
##   You can implement your own abstraction layer for other DBMSes that
##   PHP supports.
##
##   If you find a way/idea to run this software better,
##   please send it to author.
##   Tanks to my colleagues.
##
##   Usage :
##          $DB = new PGDB ();
##          $sql = new QUERY ($DB);
##          $sql->execute ("select name, surname, email from users where " .
##                "username = '$username'");
##          if ($sql->getntuples () == 0) {
##                 echo "No records found !";
##                 $DB->close ();
##                 exit;
##          }
##          $record = $sql->gofirst ();
##          $name = $record['name'];
##          echo "Realname of ($username) : " . $name;
##          $DB->close ();
*/

/* Atenção!!!
 *
 * Este script foi adaptado para o MySQL!!!
 *
 */

include_once("CELConfig/CELConfig.inc");
include_once("bd.inc");

$ipNome  = "IpBD =";
$ipValor = CELConfig_ReadVar("BD_ip");

$DBNAME       = CELConfig_ReadVar("BD_ip");
$DBUSER       = CELConfig_ReadVar("BD_user");
$DBPASSWD     = CELConfig_ReadVar("BD_password");
$DBDATABASE   = CELConfig_ReadVar("BD_database");
$DBHOST       = CELConfig_ReadVar("BD_host");
$DBPORT       = CELConfig_ReadVar("BD_porta");


class Abstract_DB
{

##PRIVATE##

        var $db_linkid = 0;

##PUBLIC##

        function open($dbname, $user, $pass, $host, $port)
        {
        }

        function close()
        {
        }
}

class PGDB extends Abstract_DB
{

        function PGDB()
        {
                global $DBNAME;
                global $DBUSER;
                global $DBPASSWD;
                global $DBHOST;
                global $DBPORT;
                global $DBDATABASE;

                $this->open($DBNAME, $DBUSER, $DBPASSWD, $DBHOST, $DBPORT);

        }

        function _PGDB()
        {
                $this->close();
        }

        function open($dbname, $user, $passwd, $host, $port)
        {
                $this->db_linkid = bd_connect() or die("Erro na conexão à BD : " . mysql_error()) ;

//              if( $this->db_linkid && mysql_select_db(CELConfig_ReadVar("BD_database") . "" ) )
                if( $this->db_linkid )
                {
                   return $this->db_linkid;
                }
                else
                {
                   return(FALSE);
                }
        }

        function close()
        {
                return mysql_close($this->db_linkid);
        }


}

class QUERY
{

##PRIVATE##
        var $dbobject;
        var $ntuples;
        var $operationresult;
        var $resultset;
        var $currentrow = 0;

##PUBLIC##

        function QUERY($pdbobject)
        {
                if ($pdbobject)
                        $this->associate($pdbobject);
        }

         function associate($pdbobject)
        {
                $this->dbobject = $pdbobject;
        }

        function execute($querystring)
        {
        //echo( $querystring);
                $this->operationresult = mysql_query($querystring) or die(mysql_error() . "<br>" . $querystring);
                return $this->operationresult;
        }

        function getntuples()
        {
                $this->ntuples = mysql_numrows($this->operationresult);
                return $this->ntuples;
        }

        function getfieldname($fieldnumber)
        {
                return mysql_fieldname($this->operationresult, $fieldnumber);
        }

        function readrow()
        {
                $this->resultset = mysql_fetch_array($this->operationresult);
                return ($this->currentresultset = $this->resultset);
        }

        function gofirst()
        {
                $this->currentrow = 0;
                return $this->readrow();
        }

        function golast()
        {
                $this->currentrow = ($this->getntuples()) - 1;
                return $this->readrow();
        }

        function getLastId()
        {
        	return mysql_insert_id($this->dbobject->db_linkid);
        }


        function gonext()
        {
                $this->currentrow++;
                if ($this->currentrow < $this->getntuples()) {
                        $this->resultset = $this->readrow();
                        return $this->resultset;
                }
                else
                        return "LAST_RECORD_REACHED";
        }

        function goprevious()
        {
                $this->currentrow--;
                if ($this->currentrow >= 0) {
                        $this->resultset = $this->readrow();
                        return $this->resultset;
                }
                else
                        return "FIRST_RECORD_REACHED";
        }

        function beginTransaction()
        {
                if (!$this->execute("BEGIN"))
                        return false;
                return true;
        }

        function commitTransaction()
        {
                if (!$this->execute("COMMIT"))
                        return false;
                return true;
        }

        function rollbackTransaction()
        {
                if (!$this->execute("ROLLBACK"))
                        return false;
                return true;
        }

}
?>