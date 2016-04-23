# Projet SATO
###### *Mariam Bouzid et Justine Evrard*
---

>Encadré par le maître conférencier Igor Stephan, ce travail d’étude et de recherche s’inscrit dans le cadre d’un stage clôturant les trois années de licence informatique à l’université d’Angers. Le sujet porte sur le travail accompli par les quatre chercheurs japonais Naoyuki Tamura, Akiko Taga, Satoshi Kitagawa et Mutsunori Banbara, dans l’article Compiling finite linear CSP into SAT, publié en 2009.

### Objectif
Prenant conscience du contexte de cette étude, nous essayerons de lever toutes ambiguïtés concernant la méthodologie des quatre chercheurs japonais cités précédemment. L’extraction de ce substrat théorique nous préparera méthodiquement en vue de créer notre propre script en PHP illustrant le fonctionnement de l’encodage décrit dans cette publication.

### Présentation
Le domaine de notre étude se partage entre celui des mathématiques et celui de l’informatique théorique. En effet, nous allons résoudre des problèmes d’aspect mathématiques grâce à la logique informatique, et plus précisément la logique propositionnelle, bien que la limite entre l’ordre 0 et l’ordre 1 devienne parfois ténue. De manière plus formelle, nous voulons transformer un problème de satisfaction de contraintes (CSP) linéaire sous nombres entiers en un problème de satisfiabilité SAT.

Une telle manipulation nécessite d’encoder les contraintes du CSP en formules propositionnelles, permettant ainsi l’utilisation d’un solveur SAT. La méthode d’encodage proposée dans cette étude est nommée `order encoding` qui représente par des symboles propositionnels des comparaisons de la forme `x ≤ a`, x étant une variable entière et a une valeur entière. Cet encodage diffère à celui – plus classique – du `sparse encoding` qui se base plutôt sur de l’assignation de valeurs aux variables entières comme `x = a`.

### Script PHP
Notre script encode un problème CSP en un problème SAT puis écrit le fichier `.dimacs` nécessaire au *solver SAT Glucose* utilsé. Au terme de son exécution, il affiche le dialogue du *solver* et écrit son résultat dans le fichier `output.txt`.
