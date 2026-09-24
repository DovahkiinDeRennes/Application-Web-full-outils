<div align="center">

# 🛠️ Outils personnels & gestionnaire local

**Application personnelle regroupant différents outils utilisables localement**
*avec notamment un gestionnaire de mots de passe.*

</div>

---

## 🔐 Gestionnaire de mots de passe

Le gestionnaire permet de **stocker et gérer ses mots de passe localement**.

> 🎯 **Objectif** : conserver les données sur son propre environnement, sans synchronisation avec un service cloud externe.

---

## ✨ Fonctionnalités

| | Fonctionnalité |
|---|---|
| 🔑 | Création d'une master key pour protéger le coffre |
| 🔐 | Chiffrement des mots de passe |
| 📁 | Création de dossiers pour organiser les mots de passe |
| 🔓 | Possibilité de créer des mots de passe sans dossier |
| ✏️ | Modification des mots de passe |
| 🗑️ | Suppression des mots de passe |
| 🔗 | Association d'une URL à chaque entrée |
| 👤 | Gestion de l'identifiant associé au compte |
| 📱 | Interface responsive pour ordinateur, tablette et mobile |

---

## 🗂️ Organisation

Les mots de passe peuvent être organisés de deux manières : **dans un dossier**, ou **directement sans dossier**.

```
Gestionnaire
├── 📁 Réseaux sociaux
│   ├── 🔑 Instagram
│   ├── 🔑 Facebook
│   └── 🔑 GitHub
│
├── 📁 Administratif
│   ├── 🔑 Impôts
│   └── 🔑 Banque
│
└── 🔑 Compte personnel
```

---

## 🔒 Sécurité

Le gestionnaire utilise une **master key** pour déverrouiller le coffre. Cette clé n'est **jamais stockée en clair**.

Le projet repose notamment sur :

- 🧂 **Argon2id** — stockage du hash de la master key
- 🔁 **PBKDF2-SHA256** — dérivation de la clé du coffre
- 🛡️ **AES-256-GCM** — chiffrement des mots de passe
- 🎲 Valeurs aléatoires pour les **salts** et les **nonces**
- 🧾 Tokens **CSRF** pour les opérations sensibles

Les données chiffrées sont stockées avec les informations nécessaires au déchiffrement (nonce, tag d'authentification).

> ⚠️ **Avertissement**
> Ce projet est avant tout un projet personnel. Il est recommandé de réaliser un **audit de sécurité** avant toute utilisation dans un environnement où des données sensibles importantes seraient stockées.

---

## 🐳 Installation avec Docker

**1. Construire les conteneurs**

```bash
docker compose build
```

**2. Démarrer l'application**

```bash
docker compose up
```

Pour démarrer les conteneurs en arrière-plan :

```bash
docker compose up -d
```

---

## 👤 Création d'un utilisateur

Une commande Symfony permet de créer un utilisateur :

```bash
php bin/console app:user:create \
    --email="john@example.com" \
    --password="mon-super-password" \
    --name="John Doe" \
    --roles="ROLE_USER"
```

### Paramètres

| Paramètre | Description |
|---|---|
| `--email` | Adresse e-mail de l'utilisateur |
| `--password` | Mot de passe de connexion |
| `--name` | Nom de l'utilisateur |
| `--roles` | Rôle Symfony attribué à l'utilisateur |

---

## 📂 Gestion des dossiers

Les dossiers permettent d'organiser les différentes entrées du coffre. Il est possible de :

- ➕ Créer un dossier
- 👀 Consulter son contenu
- ✏️ Modifier un dossier
- 🗑️ Supprimer un dossier
- 🔑 Ajouter des mots de passe dans un dossier
- 🔓 Créer des mots de passe sans dossier

---

## 🔑 Gestion des mots de passe

Chaque entrée peut contenir notamment :

- 🌐 un site
- 🔗 une URL
- 👤 un identifiant
- 🔑 un mot de passe
- 📁 un dossier facultatif

Les entrées peuvent ensuite être **modifiées** ou **supprimées**.

---

## 📱 Responsive

L'interface s'adapte aux différentes tailles d'écran :

🖥️ Ordinateur &nbsp;•&nbsp; 💻 Tablette &nbsp;•&nbsp; 📱 Smartphone

> Sur mobile, les tableaux sont adaptés sous forme de **cartes** afin d'éviter le défilement horizontal.

---

## 🚧 Fonctionnalités à venir

- ⭐ Mettre les mots de passe en favoris
- ⭐ Mettre les dossiers en favoris
- 🔀 Trier les mots de passe
- 🔍 Améliorer la recherche
- 🎨 Ajouter différents thèmes
- 🌙 Ajouter un thème sombre/clair
- 📋 Améliorer les actions de copie
- 🔐 Améliorer encore la gestion du coffre

---

## 🧰 Technologies

<div align="center">

`PHP` `Symfony` `Twig` `Doctrine` `Symfony Security` `Docker` `Tailwind CSS` `OpenSSL` `AES-256-GCM` `Argon2id` `PBKDF2-SHA256`

</div>

---

## 📌 Statut

🚧 **Projet personnel en développement**

De nouvelles fonctionnalités et améliorations de sécurité sont ajoutées progressivement.
