<?php
if(empty($_POST['name_video'])){
echo '  <p>Enregistrer votre vidéo :</p>
    <form method="post" action="mapage.php" enctype="multipart/form-data">
      <input type="file" name="video">
      <input type="hidden" name="MAX_FILE_SIZE" value="1500000000">
      <input type="hidden" name="name_video" value="video1"/>
      <input type="submit" value="Envoyer">
    </form>';
}
else{
  // Testons si le fichier a bien été envoyé et s'il n'y a pas d'erreur
  if(isset($_FILES['video']) AND $_FILES['video']['error'] == 0){
    // Testons si le fichier n'est pas trop gros
    if($_FILES['video']['size'] <= 1500000000){
      // Testons si l'extension est autorisée
      $infosfichier = pathinfo($_FILES['video']['name']);
      $extension_upload = $infosfichier['extension'];
      $extensions_autorisees = array('mpg', 'avi');
      if(in_array($extension_upload, $extensions_autorisees)){
        // On peut valider le fichier et le stocker définitivement
        if(move_uploaded_file($_FILES['video']['tmp_name'], 'video/'.$_POST['name_video'].'.'.$extension_upload)){
          $_SESSION['name_video'] = $_POST['name_video'].'.'.$extension_upload;
          echo 'L\'envoi a bien été effectué !<br />';
        }
        else
          echo 'Erreur, Déplacement du dossier temporaire au dossier "video"';
      }
      else
        echo 'Erreur, Extenstion non autorisée';
    }
    else
      echo 'Erreur, Fichier trop lourd';
  }
  else
    echo 'Erreur, Upload : '.$_FILES['video']['error']; // tu trouveras à quoi correspond le code erreur : http://fr.php.net/manual/fr/features.file-upload.errors.php
}