# Module Panier + Wishlist — installation

Prérequis : le module Catalogue est déjà installé (ce module réutilise ProductResource et les traits du catalogue).

1. Copie le contenu de ce dossier à la racine de ton projet (mêmes chemins que le dépôt).

2. Fichiers EXISTANTS remplacés :
   - routes/api.php                      (contient toutes les routes : catalogue + panier + wishlist)
   - app/Providers/RepositoryServiceProvider.php   (5 liaisons au lieu de 3)
   - app/Models/Wishlist.php

3. Fichiers à SUPPRIMER (ancien nom "card" remplacé par "cart") :
   - database/migrations/2026_10_09_152447_create_cards_table.php
   - app/Models/Card.php

4. Dans le terminal du projet :
       php artisan migrate:fresh --seed        (ATTENTION : supprime toutes les tables de la base du projet)
       php artisan optimize:clear
       php artisan serve

5. Postman : importe Jaiphat-Panier-Wishlist.postman_collection.json puis lance le Runner.
   (Tests PHP facultatifs : php artisan test --filter=Shopping)

Routes (préfixe /api/v1) — client = utilisateur connecté (Bearer) OU invité (en-tête X-Guest-Token) :
   GET    /cart                      voir le panier
   POST   /cart/items                {product_id, size, quantity?}
   PATCH  /cart/items/{item}         {quantity}
   DELETE /cart/items/{item}
   DELETE /cart                      vider
   GET    /wishlist
   POST   /wishlist/items            {product_id}
   DELETE /wishlist/items/{product_id}
