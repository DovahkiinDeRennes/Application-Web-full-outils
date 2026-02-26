# 🛠 Outils Personnels & Gestionnaire Local
Suite d’outils personnels développés pour éviter l’utilisation de solutions en ligne existantes.  
Objectif : garder un contrôle total sur les données, en local.


## 📁 Convertisseur de fichiers
Application permettant de convertir des images et documents vers différents formats.

### ✅ Formats pris en charge
- JPG
- JPEG
- PNG
- WEBP
- PDF


### 🔐 Stockage
- Sauvegarde locale
- Téléchargement direct des fichiers convertis
---

## 🔑 Gestionnaire de mot de passe (100% local)

Gestionnaire sécurisé fonctionnant entièrement en local.

- Aucune synchronisation cloud
- Aucune transmission de données externe
- Stockage local uniquement

---

# ⚙️ Installation & Lancement

## 🖥 Lancer le serveur Symfony

Projet basé sur :
- PHP 8.4
- Symfony 5.16.1
- Composer

Installation des dépendances :

```bash
composer install
```

Lancer le serveur :

```bash
symfony serve
```

---

## 🗄 Base de données MySQL

Depuis le dossier `/bin` de MySQL :

```bash
mysqld
```

ou sous Windows :

```bash
.\mysqld
```

---

## 🛠 Migrations Doctrine

```bash
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

---

## 🧰 Outils utilisés

- PHP 8.4
- Symfony 5.16.1
- MySQL
- HeidiSQL

---

# 🚀 Tech Stack

```bash
Frontend  → Twig
Backend   → PHP / Symfony
Database  → MySQL
```
PS: oublier pas de créer un utilisateur à la main directement dans la bdd
---