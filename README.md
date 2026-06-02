# 🛒 Mon Magasin - Plateforme E-commerce

Ce projet a été développé dans le cadre de ma première année de formation en développement à la Cité des Métiers et des Compétences (CMC). Il s'agit d'une application web e-commerce complète conçue pour mettre en pratique les concepts fondamentaux du développement web (Front-end et Back-end).

## 🚀 Fonctionnalités Principales

* **Gestion des Produits :** Affichage dynamique du catalogue à partir d'un fichier `.csv`, avec un système de filtrage par catégorie côté client (JavaScript).
* **Panier d'Achat :** Système de panier fonctionnel géré via les `Sessions` PHP (ajout de produits, calcul du total, gestion des quantités).
* **Authentification Sécurisée :** Inscription et connexion des utilisateurs avec hachage des mots de passe (`password_hash`) et stockage dans une base de données MySQL.
* **Thème Dynamique (Dark/Light Mode) :** Sauvegarde des préférences de l'utilisateur grâce aux `Cookies` pour une expérience personnalisée.
* **Assistant Virtuel (Chatbot) :** Un chatbot interactif intégré, développé en JavaScript pur (Vanilla JS), utilisant les expressions régulières (Regex) pour répondre aux questions fréquentes.
* **Sécurité :** Protection contre les failles XSS lors de la soumission des formulaires (`htmlspecialchars`).

## 🛠️ Technologies Utilisées

* **Back-end :** PHP (Vanilla), Architecture de fichiers modulaire (includes).
* **Front-end :** HTML5, CSS3, JavaScript (ES6).
* **Base de données :** MySQL (pour les comptes utilisateurs) et fichiers CSV (pour le catalogue produits).
## 📂 Structure du Projet

* `/pages/` : Contient les pages principales (menu, produit, panier, login, etc.).
* `/includes/` : Composants réutilisables (`header.php`, `footer.php`, `functions.php`).
* `/data/` : Fichiers de données (ex: `produits.csv`).
* `database.sql` : Script de création de la base de données.


