Le but de ce projet est de développer une application pour gérer une collection de billets d'argent provenant de différentes régions du monde. 

L'objectif est de créer une plateforme qui permet de stocker, organiser et afficher les billets en fonction de leur pays d'origine, de leur valeur, et leur date d'apparition.

Veuillez-trouver ci-dessous le tableau de correspendance vous permettant d'établir le lien entre les abstractions [Inventaire], [Galerie] et [Objet] dans l'énoncé du TP ainsi que les entités du projet.

| [Objet]      | Billet d'argent |
|--------------|-----------------|
| [Inventaire] | Album           |
| [Galerie]    | Exposition      |

Pour plus de clarifications, vous trouverez ci-dessous le diagramme de classe de mon application:

![alt text](diagramme_classe.png)

A priori, tout ce qui a été demandé jusqu'à la séance de CSS fonctionne normalement.

Des commandes sont à votre disposition si vous voulez tester le CRUD d'un Billet ou d'un Album.

Les routes disponibles sont les suivantes:
-   Pour les billets:
    - /billet/{id}: Pour consulter les détails d'un billet
    - /billet: Pour consulter tous les billets
- Pour les albums:
    - /album/{id}: Pour consulter les détails d'un album
    -   /billet: Pour consulter tous les albums

Des datafixtures sont aussi à votre disposition pour charger les données via la commande symfony console doctrine:fixtures:load -n (après avoir créé la base de donnée et le schema bien sur)

Pour toute information complémentaire, je reste joignable sur khaldoun.taktak@telecom-sudparis.eu