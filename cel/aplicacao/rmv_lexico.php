<?php
// rmv_lexico.php:remove_lexicon.php: This script makes a request to remove a lexis from project.
// Caller file: main.php 

session_start();

include("funcoes_genericas.php");
include("httprequest.inc");


chkUser("index.php");        // checks whether the user has been authenticated

//  Scenery - Delete Lexicon

//  Purpose: Allow User to Delete a word from the lexicon that is active
//  Context: User wants to delete a word from the lexicon
//  Precondition: Login word lexicon registered in the system
//  Actors: User, System
//  Resources: Data informed
//  Episodes: The system will provide a screen for the user to justify the need
//  That exclusion so that the administrator can read and approve or not.
//  This screen also contains a button to confirm the deletion.
//  Restriction: Once clicked the button the system verifies that all fields have been filled
//  Exception: If all fields are empty, returns to the user
//  A message that all fields must be filled
//  And a button to return to the previous page.

inserirPedidoRemoverLexico($id_projeto, $id_lexico, $_SESSION['id_usuario_corrente']);

?>
<script language="javascript1.3">

opener.parent.frames['code'].location.reload();
opener.parent.frames['text'].location.replace('main.php?id_projeto=<?=$_SESSION['id_projeto_corrente']?>');

</script>

<h4>Opera&ccedil;&atilde;o efetuada com sucesso!</h4>
<script language="javascript1.3">

self.close();

</script> 
