ToDoList
========

English version - __[How to contribute to the project](#how-to-contribute-to-the-project)__  
Version française - __[Comment contribuer au projet](#comment-contribuer-au-projet)__  

---
## HOW TO CONTRIBUTE TO THE PROJECT

---
## COMMENT CONTRIBUER AU PROJET

### 1/ Gérez les issues
Pour contribuer au projet de l'application ToDoList, rendez-vous pour commencer sur le tableau Kanban de suivi de projet sur GitHub : https://github.com/ElodieBichet/ToDoList/projects/1  
Vérifiez si une issue existe déjà pour ce que vous souhaitez faire, et mettez à jour son contenu et/ou son statut si besoin. Sinon, créez une nouvelle issue que vous mettrez à jour tout au long du processus.

### 2/ Installez le projet en local
Si ce n'est déjà fait, installez [le projet](https://github.com/ElodieBichet/ToDoList) sur votre machine via Git, en suivant les insctructions d'installation du fichier [Readme](README.md).  
Plus de détails sur [la documentation GitHub](https://docs.github.com/en/get-started/quickstart/fork-a-repo).

### 3/ Créez une branche
Créer une nouvelle branche pour votre contribution en prenant soin de la nommer de manière cohérente et compréhensible (en anglais de préférence).  
Convention de nommage de branche : type-de-contrib/description-de-la-contrib  
Exemples : feature/add-delete-user-action, fix/link-tasks-to-user , documentation/update-contrib-with-tests-instruction, ...  
Faites vos modifications de code, en divisant si besoin en plusieurs commits. Rédigez les messages de commit de préférence en anglais.

### 4/ Testez vos modifications
Lancez les tests pour vérifier qu'ils passent toujours après vos modifs :
```
$ ./vendor/bin/phpunit
```
Si besoin mettez à jour les tests existants ou créez-en de nouveaux pour tester votre contribution.  
Mettez ensuite à jour le fichier de test coverage pour Codacy, avec la commande suivante :
```
$ ./vendor/bin/phpunit --coverage-clover tests/coverage.xml
```

### 5/ Créez une pull request avec votre branche
Enfin, pushez vos modifications et créez une pull request.  
Plus de détails à propos des PR sur [la documentation GitHub](https://docs.github.com/en/get-started/quickstart/fork-a-repo).  
Si votre contribution est validée, elle sera intégrée à la branche principale du projet.