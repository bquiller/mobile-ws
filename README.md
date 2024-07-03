# Présentation
Mobile-ws est une application Symfony 7 qui offre des webservices à esup-multi et qui l'envoi de notifications dans une interface easy-admin.
 
## Pré-requis 
Symfony 7 requiert PHP 8.2 ou plus et composer.
L'authentification se fait sur une serveur CAS.
Les webservices s'appuient sur un annuaire LDAP type supann et sur HyperPlanning pour l'emploi du temps.

Pour le moment, l'envoi de notification s'appuie sur une base de données Oracle. 
Pour la constitution des groupes de destinataires, une vue permet de lier les utilisateurs aux groupes.

## Configuration et Installation
La configuration se passe dans le fichier .env que vous pouvez surcharger dans un .env.local si besoin.

Pour initialiser les tables de notifications : php bin/console doctrine:schema:update --force
Pour créer le projet : composer update
Le répertoire public est donc le répertoire racine de cette application.

# Utilisation
Les différentes routes possibles : 

## WS pour les menus
/api/restaurants pour lister les restaurants du CROUS
/api/restaurants/{restaurantId}/ pour récupérer le menu d'un restaurant

## WS pour les informations de profil
/api/profil/{username} pour récupérer les informations d'un utilisateur

## WS pour l'emploi du temps
/api/edt/{username} pour récupérer l'emploi du temps hyperplanning d'un utilisateur

## WS pour les cartes
/api/carte/{username} pour récupérer les informations des cartes

## WS Souscription aux notifications
/api/channels/{username} pour lister les canaux de l'abonné
/api/notifications/{username} pour lister les notifications de l'abonné
/api/notifications pour supprimer les notifications (DELETE)
/api/notifications/read pour marquer une notification comme lue
/api/notifications/unread pour marquer une notification comme non lue
/api/register pour enregistrer le token FCM 
/api/unregister pour supprimer le token FCM

## Envoi des notifications
Rendez-vous sur /admin pour l'interface d'envoi des notifications
Il faut suivre les étapes :
 - 1 : créer une notification,
 - 2 : ajouter les groupes / utilisateurs
 - 3 : en cas de groupes, répartir les utilisateurs,
 - 4 : envoyer la notification
Seul les utilisateurs s'étant connecté sur l'application recevront une notification.
