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
Le script php `sato` encode un problème CSP en un problème SAT et fournit ce dernier, exporté au format `dimacs`, au *solver SAT* `glucose` qui détermine ainsi sa **satisfiabilité**. Si une solution est trouvée, celle-ci sera présentée de façon rudimentaire.
Pour le bon fonctionnement de ce programme, il est nécessaire d'exécuter dans un premier temps le script `configure.sh` qui compile le programme `glucose` :
```sh
$ cd sato
$ ./configure.sh
```
Attention, la bibliothèque `libz` est requise :
```sh
$ apt-get install zlib1g-dev
```
Pour connaître en détails les possibilités du script **sato**, il suffit d'utiliser l'option `-h` ou `--help` :
```sh
$ ./sato.php --help
```
