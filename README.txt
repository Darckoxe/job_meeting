----- ACCES -----

Gestionnaire de fichier : smb://infoweb.iut-nantes.univ-nantes.prive/job-dating
Accès au site en passant par le serveur : http://infoweb.iut-nantes.univ-nantes.prive/~job-dating
Accès à phpMyAdmin : http://infoweb.iut-nantes.univ-nantes.prive/phpMyAdmin

----- IDENTIFIANTS -----

Site web:
JobMeetAdmin
projet2017!

BDD:
info2-2015-jobda
jobdating

----- TACHES A REALISER EN PRIORITE -----

Tâche 1 :  Calendrier ics par formation

Tâche 2 : Sur le planning admin, quand on choisit une formation,
ca actualise la liste étudiant qui sont dans cette formation ainsi que le planning.

Tâche 3 : Quand une entreprise coche une nouvelle formation sur son compte ou en retire une il faut que dans la vue Admin,
le tableau des créneaux gris soit actualisé.

Tâche 4 : Vérifier le site sur infoweb
	- Lors de la suppression, afficher une boite de dialogue demandant la confirmation de la suppression

Tâche 5 : Dans la vue config, créer une liste pour la durée des créneaux.
Quand on indiquera la durée des créneaux via cette liste, la liste des heures de pause s'actualisera sans recharger la page (Ajax et Javascript)

Tache 6: Quand on choisi une heure pour ajouter l'étudiant, la liste des étudiants est actualisés ( ex 10h -> on a tous les étudiants qui ne sont pas à 10h)
	- On fait un onChange sur le créneau. La fonction javavascript se déclenche et récupère les étudiants du créneau. Javascript rempli la liste des étudiants

Tache 7 : Quand on clique sur une case vide du planning on ajoute un certaine étudiant (javascript)

Tâche 8 : Si l'entreprise a coché une formation mais sans avoir déposer d'offre alors afficher le input de dépôt

----- TACHES A REALISER APRES AVOIR TERMINE LES TACHES PRIORITAIRES -----

Tâche : Création d'un QCM de satisfaction

Tâche : Modifier l’affichage du planning général root pour éviter le scroll horizontal

Tâche : Ajout d’un système interactif de passage avec code couleur

----- TACHES TERMINEES S3 -----

Tâche : Rédaction du cahier des charges

Tâche : Créer la fonction de dépôt du CV pour un étudiant

Tâche : Régler les problèmes d’images dans les mails

Tâche : Créer une fonctionnalité permettant aux étudiants de consulter les offres proposées par les entreprises quand ils vont sur le profil
de l'entreprise et peuvent télécharger le document pdf

Tâche : Créer la fonction de consultation des CV pour les entreprises. Quand l’entreprise cliquera sur l’étudiant, son CV se téléchargera directement

Tâche : Ajouter deux champs textuels dans la vue config de l'admin pour mettre en place les creneaux des pauses (1 matin / 1 am)

Tâche : Afficher le planning après un jour j pour les étudiants et les entreprises
	- L'étudiant doit voir uniquement le planning qui le concerne avec ses heures et les entreprises.
	- L'entreprise doit voir uniquement les étudiants qu'elle rencontre

Tâche : Ajouter une liste déroulante des étudiants pour en ajouter ou en supprimer dans le planning

Tâche : L'étudiant et l'entreprise doivent pouvoir mettre à jour le cv ou des nouvelles offres d'emploi

Tâche : Pouvoir mettre à jour des nouvelles offres d'emploi pour les entreprises.

----- TACHES TERMINEES S4 -----

Tâche : Afficher le bouton de validation des choix

Tâche : Lors de l'inscription des étudiants, s'ils ne parviennent pas à upload leur cv, il faut annuler l'inscription en base de données

Tâche : Lorsqu'on coche une case de sélection de formation, le bouton d'upoload apparait dans la vue inscription entreprise

Tâche : Quand on supprime un étudiant de la base de données, il faut aussi supprimer son CV

Tâche : Quand on supprime une entreprise de la base de données, il faut aussi supprimer ses offres

Tâche : Quand on a une erreur 404, afficher un message d'erreur --> On affiche uniquement s'il y a des fichiers sinon rien.

Tache : Quand on met juste les créneaux de l'après midi on fait en sorte de bien afficher le planning (créneau matin = 0)

Tâche : Lorsque l'entreprise décoche une formation, il faut supprimer l'offre du serveur.

Tâche : Si le fichier d'offre existe, l'entreprise peut voir son offre sinon le lien n'apparait pas.

Tâche : Ne pas sauter les créneaux quand on met les pauses.

Tâche : Quand on supprime une entreprise des comptes, il faut supprimer les créneaux qui lui sont associé.

Tâche : Calendrier ics par étudiant
