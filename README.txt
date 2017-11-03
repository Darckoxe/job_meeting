---------- ACCES NOMADE ----------
Gestionnaire de fichier : smb://infoweb.iut-nantes.univ-nantes.prive/job-dating
Accès au site en passant par le serveur : http://infoweb.iut-nantes.univ-nantes.prive/~job-dating
Accès à phpMyAdmin :  http://infoweb.iut-nantes.univ-nantes.prive/phpMyAdmin

Login site web:
JobMeetAdmin
projet2017!

Login BDD:
info2-2015-jobda
jobdating

---------- LIENS UTILES ----------
Upload d'un fichier + gérer le fichier dans la BDD : https://openclassrooms.com/courses/upload-de-fichiers-par-formulaire


---------- FONCTIONS A AMELIORER ----------
Dépot d'un CV : 
	1. Ne pas valider si pas upload [X]
	2. Recharger la page si mauvais format (sans perte dans le formulaire)
	3. Recharger si mauvaise taille (sans perte dans le formulaire)
	4. Intégrer le code proprement (dans le routeur.php)
	5. Ajout dans la BDD la relation cv(id, idCv, nomfichier)
		-> idCv est clé étrangère et référence idEtu
		-> nomfichier = prenom.nom.pdf 
