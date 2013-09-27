<?php

// Remove_relationship.php: This script makes a request to remove a relation of the project.
// File that calls: main.php

session_start();

include("funcoes_genericas.php");
include("httprequest.inc");


chkUser("index.php");     // checks whether the user has been authenticated   

inserirPedidoRemoverRelacao($_SESSION['id_projeto_corrente'], $id_relacao, $_SESSION['id_usuario_corrente']);

?>
<script language="javascript1.3">

opener.parent.frames['code'].location.reload();
opener.parent.frames['text'].location.replace('main.php?id_projeto=<?=$_SESSION['id_projeto_corrente']?>');

<?php

//  Scenery - Delete Relation
//  Purpose: Allow User to Delete a Relation that is active
//  Context: User wants to delete a Relation
//  Precondition: Login, backdrop registered in the system
//  Actors: User, System
//  Resources: Data informed
//  Episodes: The system will provide a screen for the user to justify the need for that
//  Exclusion so that the administrator can read and approve or disapprove the same.
//  This screen also contains a button to confirm the deletion.
//  Restriction: After clicking the button, the system checks whether all fields were filled
//  Exception: If all fields are empty, returns to the user a message
//  Warning that all fields must be completed and a button to return to the previous page.

?>

</script>

<h4>Operação efetuada com sucesso!</h4>
<script language="javascript1.3">

self.close();

</script> 
