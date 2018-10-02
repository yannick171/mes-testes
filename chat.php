
<?php
session_start();
include('functions.php');
$db = db_connect();
?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>Unbenannt</title>
	<link rel="stylesheet" type="text/css" href="stylechat.css">
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>

<script src="chat.js"></script>
</head>
<body>
<div id="container">
	<h1>Mon super chat</h1>
<?php
// permettra de créer l'utilisateur lors de la validation du formulaire
if(isset($_POST['login']) AND !preg_match("#^[-. ]+$#", $_POST['login'])) {
}

/* Si l'utilisateur n'est pas connecté, 
d'où le ! devant la fonction, alors on affiche le formulaire */
if(!user_verified()) {
?>
        <!-- Statut //////////////////////////////////////////////////////// -->				
	<table class="status"><tr>
		<td>
			<span id="statusResponse"></span>
			<select name="status" id="status" style="width:200px;" onchange="setStatus(this)">
				<option value="0">Absent</option>
				<option value="1">Occup&eacute;</option>
				<option value="2" selected>En ligne</option>
			</select>
		</td>
	</tr></table>
	<table class="chat"><tr>		
	<!-- zone des messages -->
	<td valign="top" id="text-td">
            	<div id="annonce"></div>
		<div id="text">
			<div id="loading">
				<center>
				<span class="info" id="info">Chargement du chat en cours...</span><br />
				<img src="ajax-loader.gif" alt="patientez...">
				</center>
			</div>
		</div>
	</td>
			
	<!-- colonne avec les membres connectés au chat -->
	<td valign="top" id="users-td"><div id="users">Chargement</div></td>
</tr></table>
Enfin, nous affichons la barre contenant la zone de texte pour taper le message et le bouton :

<!-- Zone de texte //////////////////////////////////////////////////////// -->
        <a name="post"></a>
	<table class="post_message"><tr>
		<td>
		<form action="" method="" onsubmit="envoyer(); return false;">
			<input type="text" id="message" maxlength="255" />
			<input type="button" onclick="envoyer()" value="Envoyer" id="post" />
		</form>
                <div id="responsePost" style="display:none"></div>
		</td>
	</tr></table>
</div>
	
	//................................................................php
<?php
CREATE TABLE IF NOT EXISTS `chat_online` (
  `online_id` int(11) NOT NULL auto_increment,
  `online_ip` varchar(100) collate latin1_german1_ci NOT NULL,
  `online_user` int(11) NOT NULL,
  `online_status` enum('0','1','2') collate latin1_german1_ci NOT NULL,
  `online_time` bigint(21) NOT NULL,
  PRIMARY KEY  (`online_id`)
) ENGINE=MyISAM ;

?>
	
	<?php
Connexion à la base de données
La première fonction s'intitule db_connect(). Elle permettra de se connecter à la base de données, via l'utilisation de la librairie PDO. Pour la rendre utilisable, remplacez seulement les informations contenues dans INFORMATIONS DE CONNEXION par vos identifiants de connexion.

<?php
function db_connect() {
	// définition des variables de connexion à la base de données	
	try {
		$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
		// INFORMATIONS DE CONNEXION
		$host = 	'nom d\' hote';
		$dbname = 	'nom de la base';
		$user = 	'nom d\'utilisateur';
		$password = 	'mot de passe';
		// FIN DES DONNEES
		
		$db = new PDO('mysql:host='.$host.';dbname='.$dbname.'', $user, $password, $pdo_options);
		return $db;
	} catch (Exception $e) {
		die('Erreur de connexion : ' . $e->getMessage());
	}
}
?>
Test de la connexion de l'utilisateur
La seconde fonction sera nommée user_verified(). Elle vérifiera que l'utilisateur est connecté et renvoie true si la session 'id' existe, false en cas contraire.

<?php
function user_verified() {
	return isset($_SESSION['id']);
}
?>
Détection des liens
Enfin, la dernière fonction, appelée urllink() détectera les liens dans les messages et les transformera en URLs cliquables.

<?php
function urllink($content='') {
	$content = preg_replace('#(((https?://)|(w{3}\.))+[a-zA-Z0-9&;\#\.\?=_/-]+\.([a-z]{2,4})([a-zA-Z0-9&;\#\.\?=_/-]+))#i', '<a href="$0" target="_blank">$0</a>', $content);
	// Si on capte un lien tel que www.test.com, il faut rajouter le http://
	if(preg_match('#<a href="www\.(.+)" target="_blank">(.+)<\/a>#i', $content)) {
		$content = preg_replace('#<a href="www\.(.+)" target="_blank">(.+)<\/a>#i', '<a href="http://www.$1" target="_blank">www.$1</a>', $content);
		//preg_replace('#<a href="www\.(.+)">#i', '<a href="http://$0">$0</a>', $content);
	}

	$content = stripslashes($content);
	return $content;
}
?>
	
	Méthode d'appel
Pour appeler nos pages, nous utiliserons le format de données JSON. Pour ceux qui ont déjà utilisé le format XML, vous comprendrez très vite. Si vous n'avez jamais utilisé le JSON, ce ne sera pas très compliqué mais je vous conseille de vous référer à Google et sur notre fabuleux SdZ afin d'en savoir plus. 
Nos pages PHP seront donc parsées au format JSON. Petit rappel, il est de la forme :

{
	"variable-1" : "valeur",
	"variable-2" : "valeur",
	"variable-3" : {
		"sous-variable-1" : "valeur",
		"sous-variable-2" : "valeur",
		"sous-variable-3" : "valeur"
	}
}
Ce sera donc très simple, je vous expliquerais au fur et à mesure.

Récupération des messages - get-message.php
Créez un nouveau fichier et enregistrez-le dans le dossier phpscripts créé précédemment. Nommez-le : get-message.php. Ce fichier se trouvera donc dans le même dossier que functions.php.

Tout d'abord, étant donné que ce fichier sera appelé via la méthode Ajax, il devra inclure functions.php et demander la connexion à la base de données. L'appel de la fonction session_start() permettra d'utiliser les sessions que nous créerons par la suite.

<?php
session_start();
include('functions.php');
// Appel de la fonction de connexion à la base de données
$db = db_connect();
?>
Ici, nous allons vérifier que le compte de l'utilisateur existe toujours. S'il a été supprimé, il ne faut pas le laisser écrire des messages et on doit le rediriger après avoir supprimé ses sessions. La redirection s'effectuera en Javascript. Le fichier renvoie 0 si le compte a été supprimé et les messages dans le cas contraire.

<?php
/* On vérifie d'abord que le compte existe, si ce n'est pas le cas, 
on s'arrête, on supprime les sessions et on renvoie 0. */
$checkUser = $db->prepare("SELECT * FROM chat_accounts WHERE account_id = :id AND account_login = :login ");
$checkUser->execute(array(
	'id' => $_SESSION['id'],
	'login' => $_SESSION['login']
));	
$countUser = $checkUser->rowCount();
if($countUser == 0) {
	// On indique qu'il y a une erreur de type unlog
	// donc que l'utilisateur connecté n'a pas de compte
	$json['error'] = 'unlog';
	// On supprime les sessions
	unset($_SESSION['time']);
	unset($_SESSION['id']);
	unset($_SESSION['login']);
} else {
	// On indique qu'il n'y a aucune erreur
	$json['error'] = '0';
	// ON PEUT CONTINUER !!!
}
$checkUser->closeCursor();

// Encodage de la variable tableau json et affichage
echo json_encode($json);
?>
Nous devons ensuite afficher l'annonce, située en haut dans le chat. Extrêmement simple, nous ne feront qu'afficher le message présent dans la table chat_annonce. Pour rappel, nous utilisons la librairie PDO pour effectuer des interactions avec la base de données. Les autres codes qui vont suivre devront se trouver à la place du commentaire "ON PEUT CONTINUER !!!" du code précédent.

<?php
// Affichage de l'annonce //////////////////////////////////////////
$query = $db->query("SELECT * FROM chat_annonce LIMIT 0,1");
while ($data = $query->fetch())
	$json['annonce'] = $data['annonce_text'];
$query->closeCursor();
?>
Pour terminer, il nous suffit d'afficher les derniers messages du chat. Pour ce faire, nous devons effectuer une jointure entre la table chat_messages et chat_accounts afin de récupérer le pseudo du membre ayant envoyé le message.

<?php
/* On effectue la requête sur la table contenant les messages. On récupère
les 100 derniers messages. Enfin, on affiche le tout. */

/* Si vous voulez faire appraître les messages depuis l'actualisation
de la page, laissez l'AVANT-DERNIERE ligne de la requete, sinon, supprimez-la */
$query = $db->prepare("
	SELECT message_id, message_user, message_time, message_text, account_id, account_login
	FROM chat_messages
	LEFT JOIN chat_accounts ON chat_accounts.account_id = chat_messages.message_user
	WHERE message_time >= :time
	ORDER BY message_time ASC LIMIT 0,100
");
$query->execute(array(
	'time' => $_GET['dateConnexion']
));
$count = $query->rowCount();
if($count != 0) {
	$json['messages'] = '<div id="messages_content">';
	// On crée un tableau qui continendra notre...tableau
	// Afin de placer les emssages en bas du chat
	// On triche un peu mais c'est plus simple :D
	$json['messages'] .= '<table><tr><td style="height:500px;" valign="bottom">';
	$json['messages'] .= '<table style="width:100%">';

	$i = 1;
	$e = 0;
	$prev = 0;
	while ($data = $query->fetch()) {
		// Change la couleur dès que l'ID du membre est différent du précédent
		if($i != 1) {
			$idNew = $data['message_user'];		
			if($idNew != $id) {
				if($colId == 1) {
					$color = '#077692';
					$colId = 0;
				} else {
					$color = '#666';
					$colId = 1;
				}
				$id = $idNew;
			} else
				$color = $color;
		} else {
			$color = '#666';
			$id = $data['message_user'];
			$colId = 1;
		}


		$text .= '<tr><td style="width:15%" valign="top">';
		// Si le dernier message est du même membre, on écrit pas de nouveau son pseudo
		if($prev != $data['account_id']) {
			// contenu du message	
			$text .= '<a href="#post" onclick="insertLogin(\''.addslashes($data['account_login']).'\')" style="color:black">';
			$text .= date('[H:i]', $data['message_time']);
			$text .= '&nbsp;<span style="color:'.$color.'">'.$data['account_login'].'</span>';
			$text .= '</a>';	
		}
		$text .= '</td>';			
		$text .= '<td style="width:85%;padding-left:10px;" valign="top">';

			
		// On supprime les balises HTML
		$message = htmlspecialchars($data['message_text']); 

		// On transforme les liens en URLs cliquables
		$message = urllink($message);
			
		// Si le nom apparaît suivi de >, on le colore en orange
		if(user_verified()){
			if(preg_match('#'.$_SESSION['login'].'&gt;#is', $message)) {
				$message = preg_replace('#'.$_SESSION['login'].'&gt;#is', '<b><span style="color:orange;">'.$_SESSION['login'].'&gt;</span></b>', $message);
			}
		}
			
		// On ajoute le message en remplaçant les liens par des URLs cliquables
		$text .= $message.'<br />';
		$text .= '</td></tr>';

		$i++;
		$prev = $data['account_id'];
	}
		
	/* On crée la colonne messages dans le tableau json
	qui contient l'ensemble des messages */
	$json['messages'] = $text;

	$json['messages'] .= '</table>';
	$json['messages'] .= '</td></tr></table>';
	$json['messages'] .= '</div>';			
} else {
	$json['messages'] = 'Aucun message n\'a été envoyé pour le moment.';
}
$query->closeCursor();
?>
Notre fichier est fin prêt ! Il est désormais à votre service, O grand seigneur. Dès qu'on l'appellera, et on le fera bien vite, via notre magnifique fichier JS que vous rêvez tous de programmer, le fichier sera appelé et insérera tous les messages ET l'annonce dans le bloc ayant pour ID "text". Si votre courage ne vous a pas encore perdu, alors continuez et je suis sûr que vous irez très vite. Persévérez. Vous voyez ? Ce n'est quand même pas bien compliqué, si ?

Récupération des membres connectés - get-online.php
Nous y voilà ! L'une des principales raisons pour lesquelles j'ai décidé autrefois, il y a fort longtemps, de concevoir mon propre chat. Je cherchais partout sur internet des chats entièrement prêts à être utilisés, mais je n'en ai trouvé aucun permettant d'afficher la liste des membres de la façon dont je voulais. Ici, on va le faire. Et ce sera très simple.

Créez donc un nouveau fichier que vous appellerez get-online.php et que vous enregistrerez dans le dossier phpscripts comme get-message.php.

Tout d'abord, on effectue la même chose que pour la récupération des messages, soit :

<?php
session_start();
include('functions.php');
$db = db_connect();
?>
Ensuite, on doit vérifier que l'utilisateur est inscrit dans la table des membres connectés. Si c'est le cas, alors on modifie sa date de dernière connexion pour qu'il ne soit pas ensuite supprimé. Dans le cas contraire, on ajoute l'utilisateur dans la base de données.

<?php
// On vérifie que l'utilisateur est inscrit dans la base de données
$query = $db->prepare("
	SELECT *
	FROM chat_online
	WHERE online_user = :user 
");
$query->execute(array(
	'user' => $_SESSION['id']
));
// On compte le nombre d'entrées
$count = $query->rowCount();
$data = $query->fetch();

if(user_verified()) {
	/* si l'utilisateur n'est pas inscrit dans la BDD, on l'ajoute, sinon
	on modifie la date de sa derniere actualisation */
	if($count == 0) {
		$insert = $db->prepare('
			INSERT INTO chat_online (online_id, online_ip, online_user, online_status, online_time) 
			VALUES(:id, :ip, :user, :status, :time)
		');
		$insert->execute(array(
			'id' => '',
			'ip' => $_SERVER["REMOTE_ADDR"],
			'user' => $_SESSION['id'],
			'status' => '2',
			'time' => time()
		));
	} else {
		$update = $db->prepare('UPDATE chat_online SET online_time = :time WHERE online_user = :user');
		$update->execute(array(
			'time' => time(),
			'user' => $_SESSION['id']
		));
	}
}

$query->closeCursor();
?>
On doit maintenant supprimer tous les membres dont la dernière date de connexion date de plus de cinq secondes. Voilà pourquoi nous devions actualiser la date de dernière connexion dans le code précédent. Si le membre n'a pas actualisé depuis cinq secondes, il n'est, soit plus sur le chat, soit buggé. :-°

<?php
// On supprime les membres qui ne sont pas sur le chat,
// donc qui n'ont pas actualisé automatiquement ce fichier récemment
$time_out = time()-5;
$delete = $db->prepare('DELETE FROM chat_online WHERE online_time < :time');
$delete->execute(array(
	'time' => $time_out
));
?>
Pour terminer, on doit afficher la liste des membres connectés. Le champ online_status va enfin servir. Il va nous permettre de définir le statut de l'utilisateur (qui peut le modifier, on verra ça plus tard). Le code ci-dessous :

<?php
// Récupère les membres en ligne sur le chat
// et retourne une liste
$query = $db->prepare("
	SELECT online_id, online_id, online_user, online_status, online_time, account_id, account_login
	FROM chat_online 
	LEFT JOIN chat_accounts ON chat_accounts.account_id = chat_online.online_user 
	ORDER BY account_login
");
$query->execute();
// On compte le nombre de membres
$count = $query->rowCount();

/* Si au moins un membre est connecté, on l'affiche.
Sinon, on affiche un message indiquant que personne n'est connecté */
if($count != 0) {
	// On affiche qu'il n'y a aucune erreur
	$json['error'] = '0';
	
	$i = 0;
	while($data = $query->fetch()) {
		if($data['online_status'] == '0') {
			$status = 'inactive';
		} elseif($data['online_status'] == '1') {
			$status = 'busy';
		} elseif($data['online_status'] == '2') {
			$status = 'active';
		}
		
		// On enregistre dans la colonne [status] du tableau
		// le statut du membre : busy, active ou inactive (occupé, en ligne, absent)
		$infos["status"] = $status;
		// Et on enregistre dans la colonne [login] le pseudo
		$infos["login"] = $data['account_login'];
		
		// Enfin on enregistre le tableau des infos de CE MEMBRE
		// dans la [i ème] colonne du tableau des comptes 
		$accounts[$i] = $infos;
		$i++;
	}
	// On enregistre le tableau des comptes dans la colonne [list] de JSON
	$json['list'] = $accounts;
} else {
	// Il y a une erreur, aucun membre dans la liste
	$json['error'] = '1';
}

$query->closeCursor();

// Encodage de la variable tableau json et affichage
echo json_encode($json);
?>
Envoi d'un message - post-message.php
Et voilà ! On y est presque. Cette fois, créez encore un fichier nommé post-message.php et enregistrez-le...encore dans phpscripts.

Inutile de répéter qu'il faut appeler la fonction session_start() et l'inclusion du fichier de fonctions avec l'appel de la fonction db_connect()...hein ? Mince je l'ai déjà fait ! Tant pis ! Mais n'allez pas croire que ça se repassera encore une fois. Bon et n'oubliez pas que db_connect() est appelée de la manière suivante : <?php $db = db_connect(); ?>, tant qu'à faire.

Ne nous reste plus qu'à programmer la suite, très simple !
Tout d'abord, vérifier que l'utilisateur est connecté et que la zone de texte n'est pas vide. On ne va pas envoyer un message sans contenu, pas vrai ? Donc si l'utilisateur n'est pas connecté, on renvoie l'erreur. En cas contraire, on teste la zone de texte. Si elle est vide, on renvoie une nouvelle erreur, sinon, on continue ! Ensuite, on doit vérifier la similitude du nouveau message avec le dernier message de l'utilisateur. Si les deux messages sont trop ressemblants, alors on affiche une erreur. Enfin, on vérifie que le dernier message n'est pas trop récent pour éviter les floods automatiques.
Et comme je suis un magicien, pouf ! :magicien:

<?php
if(user_verified()) {
	if(isset($_POST['message']) AND !empty($_POST['message'])) {	
		/* On teste si le message ne contient qu'un ou plusieurs points et
		qu'un ou plusieurs espaces, ou s'il est vide. 
			^ -> début de la chaine - $ -> fin de la chaine
			[-. ] -> espace, rien ou point 
			+ -> une ou plusieurs fois
		Si c'est le cas, alors on envoie pas le message */
		if(!preg_match("#^[-. ]+$#", $_POST['message'])) {	
			$query = $db->prepare("SELECT * FROM chat_messages WHERE message_user = :user ORDER BY message_time DESC LIMIT 0,1");
			$query->execute(array(
				'user' => $_SESSION['id']
			));
			$count = $query->rowCount();
			$data = $query->fetch();
			// Vérification de la similitude
			if($count != 0)
				similar_text($data['message_text'], $_POST['message'], $percent);

			if($percent < 80) {
				// Vérification de la date du dernier message.
				if(time()-5 >= $data['message_time']) {

					// YES ! ON PEUT CONTINUER ! Ouiiiii.

				} else
					echo 'Votre dernier message est trop récent. Baissez le rythme :D';	
			} else
				echo 'Votre dernier message est très similaire.';	
		} else
			echo 'Votre message est vide.';	
	} else
		echo 'Votre message est vide.';	
} else
	echo 'Vous devez être connecté.';	
?>
Pour terminer, ou continuer comme je l'ai dit, nous devons insérer le message dans la base de données. On renvoie true à la fin de l'insertion SQL.

<?php
// A placer à l'intérieur du if(time()-5 >= $data['message_time'])

$insert = $db->prepare('
	INSERT INTO chat_messages (message_id, message_user, message_time, message_text) 
	VALUES(:id, :user, :time, :text)
');
$insert->execute(array(
	'id' => '',
	'user' => $_SESSION['id'],
	'time' => time(),
	'text' => $_POST['message']
));
echo true;
?>
Modification du statut du membre - set-status.php
C'est fini pour les actions, enfin pas encore, mais presque. Plus qu'une page à programmer et ce sera bon ! Nous allons donc coder de nos propres mains ce fichier. Je vous préviens : ça ne va pas être un jeu d'enfant ! Attention, coeurs fragiles, s'abstenir ! Non je PLAISANTE. Trêve de bavardage, allons-y !

Sérieusement ça va être très simple, sans aucun doute la plus rapide à réaliser. Vous allez donc créer un nouveau fichier du nom de set-status.php et le placer dans phpscripts/.

Comme d'habitude, commencez par inclure les trois lignes citées deux, voire trois fois plus haut. Ensuite, ajoutez une condition pour vérifier que le visiteur est connecté. Pour finir, il ne vous reste qu'à réaliser une requête SQL pour modifier le statut du membre concerné.

Je suis magicien, mais aussi un ange et je vais faire appel à mes pouvoirs divins :

<?php
if(user_verified()) {
	$insert = $db->prepare('
		UPDATE chat_online SET online_status = :status WHERE online_user = :user
	');
	$insert->execute(array(
		'status' => $_POST['status'],
		'user' => $_SESSION['id']		
	));
}
?>
On peut dire que ce fut très dur et fort en émotions, non ? Ah ben tant pis. Moi j'ai souffert. Si si je vous jure.

Voilà pour les actions PHP que nous appellerons via notre fichier JS. Ne vous inquiétez pas, sa programmation approche. Je sais que vous rêvez de toucher au Javascript.

Adaptation du fichier chat.php... ça faisait longtemps !
Cette fois, ça va être plutôt rapide. Nous allons devoir indiquer à notre fichier chat.php qu'il doit demander un pseudo et son mot de passe à l'utilisateur s'il n'est pas connecté. Après le formulaire rempli, l'utilisateur est connecté et peut donc "chatter" avec les autres membres.

Premièrement, il va encore falloir inclure les trois petites lignes au début de la page chat.php, donc session_start() etc. Mais voilà, si vous avez fait attention, il y a une correction à apporter. La voici :

<?php
session_start();
include('phpscripts/functions.php');
$db = db_connect();
?>
Vous avez pu constater que nous avons rajouté phpscripts/ avant functions.php. Pourquoi ? Ah, et bien si vous ne savez pas, c'est que vous avez dû faire une ENORME erreur. o_O Non c'est une blague. En fait, vous n'avez peut-être pas créé le dossier phpscripts/ dans lequel vous avez placé les fichiers suivants : functions.php, get-message.php, get-online.php, set-status.php et post-message.php. Dans ce cas, si TOUS les fichiers sont à la racine du site, vous pouvez mettre : <?php include('functions.php'); ?> tout simplement. Néanmoins, la création de ce dossier permet une meilleure organisation.

Maintenant, nous devons vérifier que l'utilisateur est connecté. S'il l'est, alors nous affichons le chat, sinon nous affichons le formulaire. Vous devez donc rajouter ce bout de code entre les lignes surlignées :

<h1>Mon super chat</h1>
<?php
// permettra de créer l'utilisateur lors de la validation du formulaire
if(isset($_POST['login']) AND !preg_match("#^[-. ]+$#", $_POST['login'])) {
}

/* Si l'utilisateur n'est pas connecté, 
d'où le ! devant la fonction, alors on affiche le formulaire */
if(!user_verified()) {
?>
<div class="unlog">
	<form action="" method="post">
	Indiquez votre pseudo afin de vous connecter au chat. 
	Aucun mot de passe n'est requis. Entrez simplement votre pseudo.<br><br>
				
	<center>
		<input type="text" name="login" placeholder="Pseudo" /><br />
                <input type="password" name="pass" placeholder="Mot de passe" /><br /> 
		<input type="submit" value="Connexion" />
	</center>
	</form>
</div>
<?php
} else {
?>
<table class="post_message"><tr>
Vous devez aussi rajouter entre les lignes surlignées :

<option value="2">En ligne</option>
	</select>
	</td>
</tr></table>
<?php
	}
?>
</div>
Pour vous donner un aperçu du formulaire de connexion, voici une image de la page de connexion.Image utilisateur

Vous avez dû remarquer la présence du <?php if(isset($_POST['login'])) ?>. Nous allons devoir la remplir. Pour rappel, elle vérifie s'il existe une variable de type POST nommée login. Si la fonction isset() renvoie true, alors il faut continuer, sinon, on passe cette étape.

Dans ce traitement, nous vérifions s'il existe un membre portant ce pseudo, s'il existe, alors il est seulement connecté. En cas contraire, nous l'enregistrons dans la base de données. Voici le code à insérer dans la condition précédente <?php if(isset($_POST['login']) AND !empty($_POST['login'])) ?> :

<?php
/* On crée la variable login qui prend la valeur POST envoyée
car on va l'utiliser plusieurs fois */
$login = $_POST['login'];
$pass = $_POST['pass'];
			
// On crée une requête pour rechercher un compte ayant pour nom $login
$query = $db->prepare("SELECT * FROM chat_accounts WHERE account_login = :login");
$query->execute(array(
	'login' => $login
));
// On compte le nombre d'entrées
$count=$query->rowCount();
			
// Si ce nombre est nul, alors on crée le compte, sinon on le connecte simplement
if($count == 0) {			
	// Création du compte
	$insert = $db->prepare('
		INSERT INTO chat_accounts (account_id, account_login, account_pass) 
		VALUES(:id, :login, :pass)
	');
	$insert->execute(array(
		'id' => '',
		'login' => htmlspecialchars($login),
		'pass' => md5($pass)
	));
				
	/* Création d'une session id ayant pour valeur le dernier ID créé
	par la dernière requête SQL effectuée */
	$_SESSION['id'] = $db->lastInsertId();
	// On crée une session time qui prend la valeur de la date de connexion
	$_SESSION['time'] = time();
	$_SESSION['login'] = $login;
} else {
	$data = $query->fetch();	
				
	if($data['account_pass'] == md5($pass)) {			
		$_SESSION['id'] = $data['account_id'];
		// On crée une session time qui prend la valeur de la date de connexion
		$_SESSION['time'] = time();
		$_SESSION['login'] = $data['account_login'];
	}
}
			
// On termine la requête
$query->closeCursor();
?>
Pour terminer, placez ce bout de code n'importe-où sur la page visible des membres connectés, pas des visiteurs. Il permettra à notre fichier JS de récupérer la date de connexion afin d'afficher les messages depuis cette date. (optionnel, vous pouvez définir d'afficher tous les messages).

<input type="hidden" id="dateConnexion" value="<?php echo $_SESSION['time']; ?>" />
Et voilà, c'est fini pour les codes PHP. Désormais nous allons nous attaquer à la programmation du fichier Javascript. Le grand, le plus fort, le plus courageux...j'ai nommé CHAT.JS !!!! merci à vous c'est trop d'applaudissements ! :p

Codons Javascript !
Utilisation d'une librairie
Citation : Vous

On a réussi ! Ah oups non c'est vrai il reste le Javascript ! Grrrrr...Bon, et bien comme je ne suis pas un vilain je ne vais pas recopier bêtement, mais je vais essayer de comprendre.

Ce fut long ! Et je veux vous rassurer. Nous avons presque fini ! En effet, nous ne réaliserons pas la fonction Ajax permettant d'appeler des pages PHP, mais nous allons utiliser une fonction toute prête. Nous allons donc nous armer du fabuleux, du magnifique : jQuery !

Je suis en effet convaincu, désolé pour ceux qui préfèrent créer leurs propres fonctions, que pour aller vite, il est inutile de réinventer la roue. jQuery nous fournit tout ça et il est assez léger et simple d'utilisation.

Nous allons donc commencer par inclure cette librairie Javascript entre les balises <head> de notre page juste avant l'inclusion du fichier chat.js (donc <head> jquery.js -> chat.js </head>).
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>

Ce fichier Javascript hébergé sur Google Apis inclue la toute dernière version de jQuery et sera donc mis à jour automatiquement. Vous pouvez préférer une autre version comme la dernière, qui ne s'actualisera pas :
<script src="http://code.jquery.com/jquery-1.7.2.min.js"></script>

Voilà pour jQuery. Désormais nous sommes pourvus de tout ce dont nous avons besoin et nous pouvons donc commencer !

Mise en place des fonctions
Nous y voilà ! Au coeur du chat ! Tout d'abord, commencez par créer un fichier du nom de chat.js si ce n'est pas déjà fait et enregistrez-le dans le même dossier que celui où se trouve chat.PHP, soit, le dossier parent de phpscripts/. Quand c'est fait, poursuivez mes amis !

jQuery dispose de deux fonctions intéressantes afin d'appeler des fichiers extérieurs et les voici :

$.ajax(); - elle permet d'appeler un fichier PHP, ou autre, et d'y envoyer des informations par deux méthodes : POST ou GET.

$.getJSON(); - elle permet la même chose que la précédente. Elle est seulement plus rapide à écrire. Elle permet de de récupérer les données reçues en format JSON de notre fichier.

Nous utiliserons chacune d'entre elles pour nous entraîner. Ci-dessous un code exemple permettant de comprendre leur fonctionnement.

$.ajax({
	type: "GET",
	url: "page-a-appeler.php",
	data: "valeur="+valeur+"&nom="+nom,
	success: function(msg){
		$("#bloc").html(msg);
	}
});

function insertLogin(login) {
	var $message = $("#message");
	$message.val($message.val() + login + '> ').focus();
}


var reloadTime = 1000;
var scrollBar = false;

function getMessages() {
	// On lance la requête ajax
	$.getJSON('phpscripts/get-message.php?dateConnexion='+$("#dateConnexion").val(), function(data) {
			/* On vérifie que error vaut 0, ce
			qui signifie qu'il n'y aucune erreur */
			if(data['error'] == '0') {
				// On intialise les variables pour le scroll jusqu'en bas
				// Pour voir les derniers messages
				var container = $('#text');
  				var content = $('#messages_content');
				var height = content.height()-500;
				var toBottom;

				// Si avant l'affichage des messages, on se trouve en bas, 
				// alors on met toBottom a true afin de rester en bas				
				// Il faut tester avant affichage car après, le message a déjà été
				// affiché et c'est aps facile de se remettre en bas :D
				if(container[0].scrollTop == height)
					toBottom = true;
				else
					toBottom = false;


				$("#annonce").html('<span class="info"><b>'+data['annonce']+'</b></span><br /><br />');
				$("#text").html(data['messages']);

				// On met à jour les variables de scroll
				// Après avoir affiché les messages
  				content = $('#messages_content');
				height = content.height()-500;
				
				// Si toBottom vaut true, alors on reste en bas
				if(toBottom == true)
					container[0].scrollTop = content.height();	
  
  				// Lors de la première actualisation, on descend
   				if(scrollBar != true) {
					container[0].scrollTop = content.height();
					scrollBar = true;
				}	
			} else if(data['error'] == 'unlog') {
				/* Si error vaut unlog, alors l'utilisateur connecté n'a pas
				de compte. Il faut le rediriger vers la page de connexion */
				$("#annonce").html('');
				$("#text").html('');
				$(location).attr('href',"chat.php");
			}
	});
}

function postMessage() {
	// On lance la requête ajax
	// type: POST > nous envoyons le message

	// On encode le message pour faire passer les caractères spéciaux comme +
	var message = encodeURIComponent($("#message").val());
	$.ajax({
		type: "POST",
		url: "phpscripts/post-message.php",
		data: "message="+message,
		success: function(msg){
			// Si la réponse est true, tout s'est bien passé,
			// Si non, on a une erreur et on l'affiche
			if(msg == true) {
				// On vide la zone de texte
				$("#message").val('');
				$("#responsePost").slideUp("slow").html('');
			} else
				$("#responsePost").html(msg).slideDown("slow");
			// on resélectionne la zone de texte, en cas d'utilisation du bouton "Envoyer"
			$("#message").focus();
		},
		error: function(msg){
			// On alerte d'une erreur
			alert('Erreur');
		}
	});
}
Et maintenant, afin que le chat s'actualise toutes les x secondes (toujours défini dans la variable reloadTime), il suffit de rajouter ce bout de code :

// Au chargement de la page, on effectue cette fonction
$(document).ready(function() {
	// On vérifie que la zone de texte existe
	// Servira pour la redirection en cas de suppression de compte
	// Pour ne pas rediriger quand on est sur la page de connexion
	if(document.getElementById('message')) {
		// actualisation des messages
		window.setInterval(getMessages, reloadTime);
		// on sélectionne la zone de texte
		$("#message").focus();
	}
});


function getOnlineUsers() {
	// On lance la requête ajax
	$.getJSON('phpscripts/get-online.php', function(data) {
		// Si data['error'] renvoi 0, alors ça veut dire que personne n'est en ligne
		// ce qui n'est pas normal d'ailleurs
		if(data['error'] == '0') {		
			var online = '', i = 1, image, text;
			// On parcours le tableau inscrit dans
			// la colonne [list] du tableau JSON
			for (var id in data['list']) {
				
				// On met dans la variable text le statut en toute lettre
				// Et dans la variable image le lien de l'image
				if(data["list"][id]["status"] == 'busy') {
					text = 'Occup&eacute;';
					image = 'busy';
				} else if(data["list"][id]["status"] == 'inactive') {
					text = 'Absent';
					image = 'inactive';
				} else {
					text = 'En ligne';
					image = 'active';
				}
				// On affiche d'abord le lien pour insérer le pseudo dans la zone de texte
				online += '<a href="#post" onclick="insertLogin(\''+data['list'][id]["login"]+'\')" title="'+text+'">';
				// Ensuite on affiche l'image
				online += '<img src="status-'+image+'.png" /> ';
				// Enfin on affiche le pseudo
				online += data['list'][id]["login"]+'</a>';
				
				// Si i vaut 1, ça veut dire qu'on a affiché un membre
				// et qu'on doit aller à la ligne			
				if(i == 1) {
					i = 0;	
					online += '<br>';
				}
				i++;		
			}
			$("#users").html(online);
		} else if(data['error'] == '1')
			$("#users").html('<span style="color:gray;">Aucun utilisateur connect&eacute;.</span>');
	});
}

function setStatus(status) {
	// On lance la requête ajax
	// type: POST > nous envoyons le nouveau statut
	$.ajax({
		type: "POST",
		url: "phpscripts/set-status.php",
		data: "status="+status.value,
		success: function(msg){
			// On affiche la réponse
			$("#statusResponse").html('<span style="color:green">Le statut a &eacute;t&eacute; mis &agrave; jour</span>');
			setTimeout(rmResponse, 3000);
		},
		error: function(msg){
			// On affiche l'erreur dans la zone de réponse
			$("#statusResponse").html('<span style="color:orange">Erreur</span>');
			setTimeout(rmResponse, 3000);
		}
	});
}
J'espère que vous aurez deviné... sinon ! En fait le setTimeout() permet d'appeler la fonction rmResponse() au bout de trois secondes afin d'enlever le message : "Le statut a été mis à jour". De cette façon, l'utilisateur verra quand il rechangera son statut qu'il se met bel et bien à jour (si le message reste, l'utilisateur ne voit aucune différence).

function rmResponse() {
	$("#statusResponse").html('');
}


<?php
function urllink($content='') {
	$content = preg_replace('#(((https?://)|(w{3}\.))+[a-zA-Z0-9&;\#\.\?=_/-]+\.([a-z]{2,4})([a-zA-Z0-9&;\#\.\?=_/-]+))#i', '<a href="$0" target="_blank">$0</a>', $content);
	// Si on capte un lien tel que www.test.com, il faut rajouter le http://
	if(preg_match('#<a href="www\.(.+)" target="_blank">(.+)<\/a>#i', $content)) {
		$content = preg_replace('#<a href="www\.(.+)" target="_blank">(.+)<\/a>#i', '<a href="http://www.$1" target="_blank">www.$1</a>', $content);
		//preg_replace('#<a href="www\.(.+)">#i', '<a href="http://$0">$0</a>', $content);
	}

	$content = stripslashes($content);
	return $content;
}
?>
en cette fonction :

<?php
function parseText($content='') {
	$content = preg_replace('#(((https?://)|(w{3}\.))+[a-zA-Z0-9&;\#\.\?=_/-]+\.([a-z]{2,4})([a-zA-Z0-9&;\#\.\?=_/-]+))#i', '<a href="$0" target="_blank">$0</a>', $content);
	// Si on capte un lien tel que www.test.com, il faut rajouter le http://
	if(preg_match('#<a href="www\.(.+)" target="_blank">(.+)<\/a>#i', $content)) {
		$content = preg_replace('#<a href="www\.(.+)" target="_blank">(.+)<\/a>#i', '<a href="http://www.$1" target="_blank">www.$1</a>', $content);
		//preg_replace('#<a href="www\.(.+)">#i', '<a href="http://$0">$0</a>', $content);
	}

	// Insérez vos smiley ici, dans le premier tableau smiliesName
	// Et dans la colonne correpsondante du second tableau smiliesUrl	
	// Indiquez le nom de l'image
	
	$smiliesName = array(':magicien:', ':colere:', ':diable:', ':ange:', ':ninja:', '&gt;_&lt;', ':pirate:', ':zorro:', ':honte:', ':soleil:', ':\'\\(', ':waw:', ':\\)', ':D', ';\\)', ':p', ':lol:', ':euh:', ':\\(', ':o', ':colere2:', 'o_O', '\\^\\^', ':\\-@');
	$smiliesUrl  = array('magicien.png', 'angry.gif', 'diable.png', 'ange.png', 'ninja.png', 'pinch.png', 'pirate.png', 'zorro.png', 'rouge.png', 'soleil.png', 'pleure.png', 'waw.png', 'smile.png', 'heureux.png', 'clin.png', 'langue.png', 'rire.gif', 'unsure.gif', 'triste.png', 'huh.png', 'mechant.png', 'blink.gif', 'hihi.png', 'siffle.png');
	$smiliesPath = "http://www.siteduzero.com/Templates/images/smilies/";

	for ($i = 0, $c = count($smiliesName); $i < $c; $i++) {
		$content = preg_replace('`' . $smiliesName[$i] . '`isU', '<img src="' . $smiliesPath . $smiliesUrl[$i] . '" alt="smiley" />', $content);
	}
	
	$content = stripslashes($content);
	return $content;
}
?>

<?php
if(user_verified()){
	if(preg_match('#'.$_SESSION['login'].'&gt;#is', $message)) {
		// Si le message n'a pas été lu, alors on compte
		if(!preg_match('#'.$_SESSION['id'].'#', $data['message_read'])) {
			$read = $db->prepare("
				UPDATE chat_messages
				SET message_read = :user
				WHERE message_id = :id
			");
			$read->execute(array(
				'user' => $data['message_read'].';'.$_SESSION['id'].';',
				'id' => $data['message_id']
			));
			$e++;
		}
		$message = preg_replace('#'.$_SESSION['login'].'&gt;#is', '<b><span style="color:orange;">'.$_SESSION['login'].'</span></b>', $message);
	}
}
?>

<?php
$json['messages'] .= $text;
		
$json['messages'] .= '</table>';
$json['messages'] .= '</div>';

// Dans la colonne unreads, on affiche le nombre de non lus
$json['unreads'] = $e;
?>
.

function playSound() {
	if(!isFocus)
		$('#soundNotification').trigger("play");
}

$(window).focus(function() {isFocus=true});
$(window).blur(function() {isFocus=false});


<!-- A placer n'importe où dans la page visible par les membres -->
<audio style="display:none" id="soundNotification">
	<source src="sound.ogg" type="audio/ogg" />
	<source src="sound.mp3" type="audio/mp3" />
</audio>

	
</body>
</html>

