# Contribution to ToDoList App
========

English version - __[How to contribute to the project](#how-to-contribute-to-the-project)__  
Version française - __[Comment contribuer au projet](#comment-contribuer-au-projet)__  

---
## HOW TO CONTRIBUTE TO THE PROJECT

### 1/ Manage the issues
To contribute to the ToDoList app project, go to the Project Tracking Kanban board on GitHub to get started: https://github.com/ElodieBichet/ToDoList/projects/1  
Check if an issue already exists for what you want to do, and update its content and / or status as needed. Otherwise, create a new issue that you will update throughout the process.

### 2/ Install the project locally
If you haven't already, install [the project](https://github.com/ElodieBichet/ToDoList) on your machine via Git, following the installation instructions in the [Readme](README.md) file.  
More details on [the GitHub documentation](https://docs.github.com/en/get-started/quickstart/fork-a-repo).

### 3/ Create a new branch
Create a branch for your contribution, taking care to name it in a coherent and understandable way (in English preferably).
Branch naming convention: contrib-type/contrib-description  
Examples: feature/add-delete-user-action, fix/link-tasks-to-user, documentation/update-contrib-with-tests-instruction, ...  
Make your code changes, dividing into multiple commits if necessary. Write commit messages preferably in English.

### 4/ Test your changes
Run the tests to verify that they always pass after your changes:
```
$ ./vendor/bin/phpunit
```
If necessary update the existing tests or create new ones to test your contribution.  
Then update the coverage test file for Codacy, with the following command:
```
$ ./vendor/bin/phpunit --coverage-clover tests/coverage.xml
```
Don't forget to commit this new *tests/coverage.xml* file!

### 5/ Create a pull request
Finally, push your changes and create a pull request.  
More details about PR on [GitHub documentation](https://docs.github.com/en/github/collaborating-with-pull-requests/proposing-changes-to-your-work-with-pull-requests/about-pull-requests).  

If your contribution is approved, it will be merged into the main branch of the project.  
Thanks!

---
## COMMENT CONTRIBUER AU PROJET

### 1/ Gérez les issues
Pour contribuer au projet de l'application ToDoList, rendez-vous pour commencer sur le tableau Kanban de suivi de projet sur GitHub : https://github.com/ElodieBichet/ToDoList/projects/1  
Vérifiez si une issue existe déjà pour ce que vous souhaitez faire, et mettez à jour son contenu et/ou son statut si besoin. Sinon, créez une nouvelle issue que vous mettrez à jour tout au long du processus.

### 2/ Installez le projet en local
Si ce n'est déjà fait, installez [le projet](https://github.com/ElodieBichet/ToDoList) sur votre machine via Git, en suivant les insctructions d'installation du fichier [Readme](README.md).  
Plus de détails sur [la documentation GitHub](https://docs.github.com/en/get-started/quickstart/fork-a-repo).

### 3/ Créez une nouvelle branche
Créez une branche pour votre contribution en prenant soin de la nommer de manière cohérente et compréhensible (en anglais de préférence).  
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
N'oubliez pas de "commiter" ce nouveau fichier *tests/coverage.xml*!

### 5/ Créez une pull request
Enfin, pushez vos modifications et créez une pull request.  
Plus de détails à propos des PR sur [la documentation GitHub](https://docs.github.com/en/github/collaborating-with-pull-requests/proposing-changes-to-your-work-with-pull-requests/about-pull-requests).  

Si votre contribution est validée, elle sera intégrée à la branche principale du projet.  
Merci !